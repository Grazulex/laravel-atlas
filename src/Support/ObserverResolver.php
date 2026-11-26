<?php

declare(strict_types=1);

namespace LaravelAtlas\Support;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Str;
use ReflectionClass;
use Throwable;

/**
 * Resolves the observers attached to an Eloquent model, from:
 *
 *  - the `#[ObservedBy]` attribute (on the model or its parent models),
 *  - `Model::observe()` calls in service providers or `static::observe()`
 *    in the model itself,
 *  - the model event listeners registered on the dispatcher at runtime.
 */
class ObserverResolver
{
    public const SOURCE_ATTRIBUTE = '#[ObservedBy]';

    public const SOURCE_OBSERVE = 'Model::observe()';

    public const SOURCE_DISPATCHER = 'dispatcher';

    /**
     * Parsed `observe()` calls of the provider files: model => observers.
     *
     * @var array<string, array<int, string>>|null
     */
    protected ?array $providerCalls = null;

    /**
     * @param  class-string<Model>  $model
     *
     * @return array<int, array{class: string, source: string}>
     */
    public function forModel(string $model): array
    {
        $observers = [];

        foreach ($this->fromAttributes($model) as $observer) {
            $observers[$observer] ??= self::SOURCE_ATTRIBUTE;
        }

        foreach ($this->fromObserveCalls($model) as $observer) {
            $observers[$observer] ??= self::SOURCE_OBSERVE;
        }

        foreach ($this->fromDispatcher($model) as $observer) {
            $observers[$observer] ??= self::SOURCE_DISPATCHER;
        }

        $result = [];
        foreach ($observers as $class => $source) {
            $result[] = ['class' => $class, 'source' => $source];
        }

        return $result;
    }

    /**
     * @param  class-string<Model>  $model
     *
     * @return array<int, string>
     */
    public function fromAttributes(string $model): array
    {
        $observers = [];
        $reflection = new ReflectionClass($model);

        while ($reflection !== false && $reflection->getName() !== Model::class) {
            foreach ($reflection->getAttributes(ObservedBy::class) as $attribute) {
                foreach ($attribute->getArguments() as $argument) {
                    foreach ((array) $argument as $observer) {
                        if (is_string($observer)) {
                            $observers[] = ltrim($observer, '\\');
                        }
                    }
                }
            }

            $reflection = $reflection->getParentClass();
        }

        return array_values(array_unique($observers));
    }

    /**
     * @param  class-string<Model>  $model
     *
     * @return array<int, string>
     */
    public function fromObserveCalls(string $model): array
    {
        $observers = $this->providerObserveCalls()[$model] ?? [];

        $file = (new ReflectionClass($model))->getFileName();
        if ($file !== false) {
            foreach ($this->parseObserveCalls($file, $model) as $observed => $classes) {
                if ($observed === $model) {
                    $observers = array_merge($observers, $classes);
                }
            }
        }

        return array_values(array_unique($observers));
    }

    /**
     * @param  class-string<Model>  $model
     *
     * @return array<int, string>
     */
    public function fromDispatcher(string $model): array
    {
        try {
            $dispatcher = $model::getEventDispatcher();
        } catch (Throwable) {
            return [];
        }

        if (! $dispatcher instanceof Dispatcher) {
            return [];
        }

        $observers = [];
        $suffix = ': ' . $model;

        foreach ($dispatcher->getRawListeners() as $event => $listeners) {
            if (! str_starts_with((string) $event, 'eloquent.') || ! str_ends_with((string) $event, $suffix)) {
                continue;
            }

            foreach ((array) $listeners as $listener) {
                if (is_string($listener) && str_contains($listener, '@')) {
                    $observers[] = Str::before($listener, '@');
                }
            }
        }

        return array_values(array_unique($observers));
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function providerObserveCalls(): array
    {
        if ($this->providerCalls !== null) {
            return $this->providerCalls;
        }

        $this->providerCalls = [];
        $files = glob(app_path('Providers/*.php')) ?: [];

        try {
            foreach (app()->getLoadedProviders() as $provider => $loaded) {
                if (! class_exists($provider)) {
                    continue;
                }

                $file = (new ReflectionClass($provider))->getFileName();

                if ($file !== false && ! str_contains(str_replace('\\', '/', $file), '/vendor/')) {
                    $files[] = $file;
                }
            }
        } catch (Throwable) {
            // No application available
        }

        foreach (array_unique($files) as $file) {
            foreach ($this->parseObserveCalls($file) as $model => $observers) {
                $this->providerCalls[$model] = array_values(array_unique(array_merge(
                    $this->providerCalls[$model] ?? [],
                    $observers,
                )));
            }
        }

        return $this->providerCalls;
    }

    /**
     * Parse `Model::observe(Observer::class)` / `static::observe([...])` calls.
     *
     * @return array<string, array<int, string>> model => observers
     */
    protected function parseObserveCalls(string $file, ?string $selfClass = null): array
    {
        $source = @file_get_contents($file);

        if ($source === false || ! str_contains($source, '::observe(')) {
            return [];
        }

        $calls = [];
        $imports = $this->imports($source);
        $namespace = preg_match('/^\s*namespace\s+([\w\\\\]+)\s*;/m', $source, $match) ? $match[1] : '';

        if (! preg_match_all('/([\\\\\w]+)::observe\(\s*(\[[^\]]*\]|[^)]*)\)/', $source, $matches, PREG_SET_ORDER)) {
            return [];
        }

        foreach ($matches as [$_, $target, $arguments]) {
            if (in_array($target, ['static', 'self'], true)) {
                if ($selfClass === null) {
                    continue;
                }
                $model = $selfClass;
            } else {
                $model = $this->resolveName($target, $imports, $namespace);
            }

            if (! preg_match_all('/([\\\\\w]+)::class/', $arguments, $observerMatches)) {
                continue;
            }

            foreach ($observerMatches[1] as $observer) {
                $calls[$model][] = $this->resolveName($observer, $imports, $namespace);
            }
        }

        return $calls;
    }

    /**
     * @return array<string, string> alias => class
     */
    protected function imports(string $source): array
    {
        $imports = [];

        if (preg_match_all('/^use\s+([\w\\\\]+)(?:\s+as\s+(\w+))?\s*;/m', $source, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $alias = $match[2] ?? '';
                $imports[$alias !== '' ? $alias : class_basename($match[1])] = $match[1];
            }
        }

        return $imports;
    }

    /**
     * @param  array<string, string>  $imports
     */
    protected function resolveName(string $name, array $imports, string $namespace): string
    {
        if (str_starts_with($name, '\\')) {
            return ltrim($name, '\\');
        }

        $first = Str::before($name, '\\');

        if (isset($imports[$first])) {
            return $imports[$first] . substr($name, strlen($first));
        }

        $qualified = $namespace !== '' ? $namespace . '\\' . $name : $name;

        if (class_exists($qualified) || ! class_exists($name)) {
            return $qualified;
        }

        return $name;
    }
}
