<?php

declare(strict_types=1);

namespace LaravelAtlas\Support;

use Closure;
use Illuminate\Contracts\Events\ShouldBeDiscovered;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Reflector;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionProperty;
use Throwable;

/**
 * Collects the event listeners of the application from every place Laravel
 * lets you register them:
 *
 *  - `$listen` arrays of the booted event service providers,
 *  - `$subscribe` arrays (event subscribers),
 *  - event discovery (listener classes whose `handle*()` / `__invoke()`
 *    methods type-hint the event, in the app's discovery directories),
 *  - anything else registered on the event dispatcher at runtime, such as
 *    `Event::listen()` calls in a service provider (closures included).
 *
 * Only application listeners are kept: listeners declared under a `vendor/`
 * directory are ignored unless `atlas.analysis.include_vendors` is enabled.
 * Eloquent model events (`eloquent.*`) are left to the observers mapping.
 */
class EventListenerDiscovery
{
    public const SOURCE_PROVIDER = 'EventServiceProvider';

    public const SOURCE_SUBSCRIBER = 'subscriber';

    public const SOURCE_DISCOVERY = 'event discovery';

    public const SOURCE_DISPATCHER = 'dispatcher';

    /**
     * @var array<string, array<int, array{class: string, name: string, method: string|null, source: string}>>|null
     */
    protected ?array $listeners = null;

    protected bool $includeVendors;

    /**
     * @param  array<int, string>|null  $discoveryPaths  Listener directories to scan, defaults to the app's discovery paths
     */
    public function __construct(
        protected ?array $discoveryPaths = null,
        ?bool $includeVendors = null,
    ) {
        $this->includeVendors = $includeVendors ?? (bool) config('atlas.analysis.include_vendors', false);
    }

    /**
     * Every application listener, keyed by event name.
     *
     * @return array<string, array<int, array{class: string, name: string, method: string|null, source: string}>>
     */
    public function all(): array
    {
        if ($this->listeners !== null) {
            return $this->listeners;
        }

        $this->listeners = [];

        foreach ($this->providerListeners() as $event => $listeners) {
            $this->add($event, $listeners, self::SOURCE_PROVIDER);
        }

        foreach ($this->subscriberListeners() as $event => $listeners) {
            $this->add($event, $listeners, self::SOURCE_SUBSCRIBER);
        }

        foreach ($this->discoveredListeners() as $event => $listeners) {
            $this->add($event, $listeners, self::SOURCE_DISCOVERY);
        }

        foreach ($this->runtimeListeners() as $event => $listeners) {
            $this->add($event, $listeners, self::SOURCE_DISPATCHER);
        }

        return $this->listeners ?? [];
    }

    /**
     * @return array<int, array{class: string, name: string, method: string|null, source: string}>
     */
    public function forEvent(string $event): array
    {
        return $this->all()[$event] ?? [];
    }

    /**
     * The events a listener class listens to.
     *
     * @return array<int, string>
     */
    public function eventsFor(string $listenerClass): array
    {
        $events = [];

        foreach ($this->all() as $event => $listeners) {
            foreach ($listeners as $listener) {
                if ($listener['class'] === $listenerClass) {
                    $events[] = $event;

                    break;
                }
            }
        }

        return $events;
    }

    /**
     * The listener directories Laravel discovers events in, plus the
     * listener paths configured for Atlas.
     *
     * @return array<int, string>
     */
    public function discoveryPaths(): array
    {
        if ($this->discoveryPaths !== null) {
            return $this->discoveryPaths;
        }

        $paths = [];

        try {
            $property = new ReflectionProperty(EventServiceProvider::class, 'eventDiscoveryPaths');
            $configured = $property->getValue();

            if (is_iterable($configured)) {
                foreach ($configured as $path) {
                    if (is_string($path)) {
                        $paths[] = $path;
                    }
                }
            }
        } catch (Throwable) {
            // Property not available on this framework version
        }

        if ($paths === []) {
            $paths[] = app_path('Listeners');
        }

        $configPaths = config('atlas.paths.listeners', []);
        if (is_array($configPaths)) {
            foreach ($configPaths as $path) {
                if (is_string($path)) {
                    $paths[] = $path;
                }
            }
        }

        $directories = [];
        foreach ($paths as $path) {
            foreach (glob($path, GLOB_ONLYDIR) ?: [] as $directory) {
                $directories[] = $directory;
            }
        }

        return array_values(array_unique($directories));
    }

    /**
     * `$listen` arrays of the application's event service providers.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function providerListeners(): array
    {
        $map = [];

        foreach ($this->eventServiceProviders() as $provider) {
            try {
                $property = new ReflectionProperty($provider, 'listen');
                $listen = $property->getValue($provider);
            } catch (Throwable) {
                continue;
            }

            if (! is_array($listen)) {
                continue;
            }

            foreach ($listen as $event => $listeners) {
                foreach ((array) $listeners as $listener) {
                    $map[(string) $event][] = $listener;
                }
            }
        }

        return $map;
    }

    /**
     * Listeners registered by the subscribers of the `$subscribe` arrays.
     * Each subscriber is run against a scratch dispatcher, so this works
     * whether its `subscribe()` returns a map or calls `$events->listen()`.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function subscriberListeners(): array
    {
        $map = [];

        foreach ($this->eventServiceProviders() as $provider) {
            try {
                $property = new ReflectionProperty($provider, 'subscribe');
                $subscribers = $property->getValue($provider);
            } catch (Throwable) {
                continue;
            }

            foreach ((array) $subscribers as $subscriber) {
                try {
                    $scratch = new Dispatcher(app());
                    $scratch->subscribe($subscriber);
                } catch (Throwable) {
                    continue;
                }

                foreach ($scratch->getRawListeners() as $event => $listeners) {
                    foreach ((array) $listeners as $listener) {
                        $map[(string) $event][] = $listener;
                    }
                }
            }
        }

        return $map;
    }

    /**
     * Statically discover listeners the same way Laravel's event discovery
     * does: public `handle*()` / `__invoke()` methods whose first parameter
     * type-hints the event (union types included).
     *
     * @return array<string, array<int, mixed>>
     */
    protected function discoveredListeners(): array
    {
        $map = [];

        foreach ($this->discoveryPaths() as $path) {
            if (! is_dir($path)) {
                continue;
            }

            foreach (File::allFiles($path) as $file) {
                $class = ClassResolver::resolveFromFile($file->getRealPath());

                if ($class === null || ! class_exists($class)) {
                    continue;
                }

                try {
                    $reflection = new ReflectionClass($class);

                    if (! $reflection->isInstantiable()) {
                        continue;
                    }

                    // ShouldBeDiscovered only exists since Laravel 13
                    if (interface_exists(ShouldBeDiscovered::class)
                        && $reflection->implementsInterface(ShouldBeDiscovered::class)
                        && $class::shouldBeDiscovered() === false) {
                        continue;
                    }

                    foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                        if ((! Str::is('handle*', $method->name) && $method->name !== '__invoke')
                            || ! isset($method->getParameters()[0])) {
                            continue;
                        }

                        foreach (Reflector::getParameterClassNames($method->getParameters()[0]) as $event) {
                            $map[$event][] = $class . '@' . $method->name;
                        }
                    }
                } catch (Throwable) {
                    // Skip classes that cannot be reflected
                }
            }
        }

        return $map;
    }

    /**
     * Everything registered on the application's event dispatcher.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function runtimeListeners(): array
    {
        try {
            $dispatcher = app('events');
        } catch (Throwable) {
            return [];
        }

        if (! $dispatcher instanceof Dispatcher) {
            return [];
        }

        $map = [];

        foreach ($dispatcher->getRawListeners() as $event => $listeners) {
            foreach ((array) $listeners as $listener) {
                $map[(string) $event][] = $listener;
            }
        }

        try {
            $wildcards = (new ReflectionProperty($dispatcher, 'wildcards'))->getValue($dispatcher);

            if (is_array($wildcards)) {
                foreach ($wildcards as $event => $listeners) {
                    foreach ((array) $listeners as $listener) {
                        $map[(string) $event][] = $listener;
                    }
                }
            }
        } catch (Throwable) {
            // Wildcards are an implementation detail, ignore when unavailable
        }

        return $map;
    }

    /**
     * Booted event service providers declared by the application.
     *
     * @return array<int, EventServiceProvider>
     */
    protected function eventServiceProviders(): array
    {
        $providers = [];

        try {
            $loaded = app()->getProviders(EventServiceProvider::class);
        } catch (Throwable) {
            return [];
        }

        foreach ($loaded as $provider) {
            if ($provider::class === EventServiceProvider::class) {
                continue;
            }

            $file = (new ReflectionClass($provider::class))->getFileName();

            if ($file !== false && ! $this->includeVendors && $this->isVendorFile($file)) {
                continue;
            }

            $providers[] = $provider;
        }

        return $providers;
    }

    /**
     * @param  array<int, mixed>  $listeners
     */
    protected function add(string $event, array $listeners, string $source): void
    {
        if (str_starts_with($event, 'eloquent.')) {
            return;
        }

        foreach ($listeners as $listener) {
            $entry = $this->normalize($listener, $source);

            if ($entry === null) {
                continue;
            }

            foreach ($this->listeners[$event] ?? [] as $existing) {
                if ($existing['class'] === $entry['class'] && $existing['method'] === $entry['method']) {
                    continue 2;
                }
            }

            $this->listeners[$event][] = $entry;
        }
    }

    /**
     * @return array{class: string, name: string, method: string|null, source: string}|null
     */
    protected function normalize(mixed $listener, string $source): ?array
    {
        $class = null;
        $method = null;

        if ($listener instanceof Closure) {
            try {
                $function = new ReflectionFunction($listener);
            } catch (Throwable) {
                return null;
            }

            $file = $function->getFileName();

            // Closures wrapping a class listener (Dispatcher::makeListener) are skipped
            if ($file === false || (! $this->includeVendors && $this->isVendorFile($file))) {
                return null;
            }

            $name = 'Closure (' . basename($file) . ':' . $function->getStartLine() . ')';

            return ['class' => 'Closure', 'name' => $name, 'method' => null, 'source' => $source];
        }

        if (is_string($listener)) {
            [$class, $method] = str_contains($listener, '@')
                ? explode('@', $listener, 2)
                : [$listener, null];
        } elseif (is_array($listener) && isset($listener[0], $listener[1]) && is_string($listener[1])) {
            $class = is_object($listener[0]) ? $listener[0]::class : (is_string($listener[0]) ? $listener[0] : null);
            $method = $listener[1];
        } elseif (is_object($listener)) {
            $class = $listener::class;
            $method = '__invoke';
        }

        if (! is_string($class) || $class === '') {
            return null;
        }

        if ($method === null && class_exists($class)) {
            $method = method_exists($class, 'handle') ? 'handle' : (method_exists($class, '__invoke') ? '__invoke' : null);
        }

        if (class_exists($class) && ! $this->includeVendors) {
            $file = (new ReflectionClass($class))->getFileName();

            if ($file !== false && $this->isVendorFile($file)) {
                return null;
            }
        }

        return ['class' => $class, 'name' => class_basename($class), 'method' => $method, 'source' => $source];
    }

    protected function isVendorFile(string $file): bool
    {
        return str_contains(str_replace('\\', '/', $file), '/vendor/');
    }
}
