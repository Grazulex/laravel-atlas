<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

class ListenerMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'listeners';
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @return array<string, mixed>
     */
    public function scan(array $options = []): array
    {
        $listeners = [];
        $defaultPaths = $this->getDefaultPaths();

        /** @var array<int, string> $paths */
        $paths = isset($options['paths']) && is_array($options['paths'])
            ? array_values(array_filter($options['paths'], 'is_string'))
            : $defaultPaths;
        $recursive = $options['recursive'] ?? true;
        $seen = [];

        foreach ($paths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            $files = $recursive ? File::allFiles($path) : File::files($path);

            foreach ($files as $file) {
                $fqcn = ClassResolver::resolveFromPath($file->getRealPath());

                if (
                    $fqcn &&
                    class_exists($fqcn) &&
                    ! isset($seen[$fqcn])
                ) {
                    $seen[$fqcn] = true;
                    $listeners[] = $this->analyzeListener($fqcn, $file->getRealPath());
                }
            }
        }

        return [
            'type' => $this->type(),
            'count' => count($listeners),
            'data' => $listeners,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeListener(string $fqcn, string $filePath): array
    {
        if (! class_exists($fqcn)) {
            throw new InvalidArgumentException("Class {$fqcn} does not exist");
        }

        $reflection = new ReflectionClass($fqcn);
        $source = file_exists($filePath) ? (file_get_contents($filePath) ?: null) : null;

        return [
            'class' => $fqcn,
            'file' => $filePath,
            'namespace' => $reflection->getNamespaceName(),
            'name' => $reflection->getShortName(),
            'methods' => $this->extractMethods($reflection),
            'handle_method' => $this->hasHandleMethod($reflection),
            'queued' => $this->isQueued($reflection),
            'is_abstract' => $reflection->isAbstract(),
            'is_final' => $reflection->isFinal(),
            'flow' => $this->analyzeFlow($source),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function extractMethods(ReflectionClass $reflection): array
    {
        $methods = [];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class === $reflection->getName() && ! $method->isConstructor()) {
                $methods[] = [
                    'name' => $method->getName(),
                    'parameters' => array_map(
                        fn (ReflectionParameter $param): array => [
                            'name' => $param->getName(),
                            'type' => $param->getType()?->__toString(),
                            'has_default' => $param->isDefaultValueAvailable(),
                        ],
                        $method->getParameters()
                    ),
                    'return_type' => $method->getReturnType()?->__toString(),
                    'is_static' => $method->isStatic(),
                ];
            }
        }

        return $methods;
    }

    protected function hasHandleMethod(ReflectionClass $reflection): bool
    {
        return $reflection->hasMethod('handle');
    }

    protected function isQueued(ReflectionClass $reflection): bool
    {
        return in_array(ShouldQueue::class, class_implements($reflection->getName()) ?: []);
    }

    /**
     * Get default listener paths from config with fallback
     *
     * @return array<int, string>
     */
    /**
     * @return array<int, string>
     */
    protected function getDefaultPaths(): array
    {
        /** @var array<int, string> $configPaths */
        $configPaths = config('atlas.paths.listeners', []);

        if (is_array($configPaths) && $configPaths !== []) {
            return array_values(array_filter($configPaths, 'is_string'));
        }

        // Default paths
        $paths = [app_path('Listeners')];

        // Add beta_app if it exists (for backward compatibility)
        $betaAppPath = base_path('beta_app/app/Listeners');
        if (is_dir($betaAppPath)) {
            $paths[] = $betaAppPath;
        }

        return $paths;
    }

    /**
     * Analyze the flow of a listener to detect dispatched events, jobs, and notifications
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    protected function analyzeFlow(?string $source): array
    {
        $flow = [
            'events' => [],
            'jobs' => [],
            'notifications' => [],
        ];

        if (! $source) {
            return $flow;
        }

        // Events dispatched via event() helper
        if (preg_match_all('/event\(\s*new\s+([A-Z][\w\\\\]+)/', $source, $matches)) {
            foreach ($matches[1] as $class) {
                $flow['events'][] = ['class' => $class];
            }
        }

        // Events dispatched via Event::dispatch()
        if (preg_match_all('/Event::dispatch\(\s*new\s+([A-Z][\w\\\\]+)/', $source, $matches)) {
            foreach ($matches[1] as $class) {
                $flow['events'][] = ['class' => $class];
            }
        }

        // Events dispatched via broadcast()
        if (preg_match_all('/broadcast\(\s*new\s+([A-Z][\w\\\\]+)/', $source, $matches)) {
            foreach ($matches[1] as $class) {
                $flow['events'][] = ['class' => $class, 'broadcast' => true];
            }
        }

        // Jobs dispatched
        if (preg_match_all('/dispatch(?:Now|Sync)?\(\s*new\s+([A-Z][\w\\\\]+)/', $source, $matches)) {
            foreach ($matches[1] as $class) {
                $flow['jobs'][] = [
                    'class' => $class,
                    'async' => ! str_contains($source, "dispatchNow(new {$class}") && ! str_contains($source, "dispatchSync(new {$class}"),
                ];
            }
        }

        // Notifications sent
        if (preg_match_all('/->notify\(\s*new\s+([A-Z][\w\\\\]+)/', $source, $matches)) {
            foreach ($matches[1] as $class) {
                $flow['notifications'][] = ['class' => $class];
            }
        }

        // Remove duplicates
        foreach ($flow as $key => $items) {
            $seen = [];
            $flow[$key] = array_values(array_filter($items, function ($item) use (&$seen) {
                $class = $item['class'];
                if (isset($seen[$class])) {
                    return false;
                }
                $seen[$class] = true;

                return true;
            }));
        }

        return $flow;
    }
}
