<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use ReflectionNamedType;
use ReflectionMethod;
use ReflectionType;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionException;

class ListenerMapper extends BaseMapper
{
    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'listeners';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultOptions(): array
    {
        return [
            'include_methods' => true,
            'include_dependencies' => true,
            'include_events' => true,
            'include_jobs' => true,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function performScan(): Collection
    {
        $basePath = app_path();

        // Standard listener paths
        $listenerPaths = [
            $basePath . '/Listeners',
            $basePath . '/Domain/Listeners',
            $basePath . '/App/Listeners',
        ];

        /** @var Collection<string, array<string, mixed>> $listeners */
        $listeners = collect();

        foreach ($listenerPaths as $path) {
            if (is_dir($path)) {
                $this->scanDirectoryForListeners($path, $listeners);
            }
        }

        return $listeners;
    }

    /**
     * Scan directory for Listener classes
     *
     * @param  Collection<string, array<string, mixed>>  $listeners
     */
    private function scanDirectoryForListeners(string $path, Collection $listeners): void
    {
        $files = glob($path . '/*.php');
        if ($files === false) {
            return;
        }

        foreach ($files as $file) {
            $className = $this->getClassFromFile($file);
            if ($className && $this->isListener($className)) {
                $listenerData = $this->analyzeListener($className, $file);
                if ($listenerData !== null) {
                    $listeners->put($className, $listenerData);
                }
            }
        }

        // Scan subdirectories recursively
        $subdirs = glob($path . '/*', GLOB_ONLYDIR);
        if ($subdirs !== false) {
            foreach ($subdirs as $subdir) {
                $this->scanDirectoryForListeners($subdir, $listeners);
            }
        }
    }

    /**
     * Check if a class is a Listener
     */
    private function isListener(string $className): bool
    {
        // Check if class name contains 'Listener'
        if (! str_contains($className, 'Listener')) {
            return false;
        }

        try {
            /** @var class-string $className */
            $reflection = new ReflectionClass($className);

            // Check if it's in a Listeners namespace or directory
            if (str_contains($className, '\\Listeners\\')) {
                return true;
            }            // Check if it has a handle method (common pattern for listeners)
            if ($reflection->hasMethod('handle')) {
                return true;
            }
        } catch (ReflectionException) {
            return false;
        }

        return false;
    }

    /**
     * Analyze a specific Listener class
     *
     * @return array<string, mixed>|null
     */
    private function analyzeListener(string $className, string $filePath): ?array
    {
        try {
            /** @var class-string $className */
            $reflection = new ReflectionClass($className);

            if ($reflection->isAbstract() || $reflection->isInterface() || $reflection->isTrait()) {
                return null;
            }            $listenerData = [
                'class_name' => $className,
                'file_path' => $filePath,
                'event' => $this->detectListenedEvent($reflection, $filePath),
            ];

            // Extract methods
            if ($this->config('include_methods')) {
                $listenerData['methods'] = $this->extractMethods($reflection);
            }

            // Extract dependencies from constructor
            if ($this->config('include_dependencies')) {
                $listenerData['dependencies'] = $this->extractDependencies($reflection);
            }

            // Extract jobs dispatched
            if ($this->config('include_jobs')) {
                $listenerData['jobs'] = $this->extractDispatchedJobs($filePath);
            }

            return $listenerData;
        } catch (ReflectionException $e) {
            return [
                'class_name' => $className,
                'error' => 'Failed to reflect class: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Detect which event this listener is listening to
     */
    private function detectListenedEvent(ReflectionClass $reflection, string $filePath): ?string
    {
        // Method 1: Check handle method signature
        if ($reflection->hasMethod('handle')) {
            $handleMethod = $reflection->getMethod('handle');
            $parameters = $handleMethod->getParameters();

            if (! empty($parameters)) {
                $eventParam = $parameters[0];
                $eventType = $eventParam->getType();

                if ($eventType instanceof ReflectionNamedType && ! $eventType->isBuiltin()) {
                    return $eventType->getName();
                }
            }
        }

        // Method 2: Look for event type in file content
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            // Look for patterns like "handle(EventName $event)"
            if ($content !== false && preg_match('/handle\s*\(\s*([A-Za-z\\\\]+)\s*\$/', $content, $matches)) {
                $eventClass = $matches[1];
                // If it doesn't start with a backslash, it might be a relative class
                if (! str_starts_with($eventClass, '\\')) {
                    // Try to resolve the full class name from use statements
                    if (preg_match('/use\s+([^;]+\\\\' . $eventClass . ')\s*;/', $content, $useMatches)) {
                        return $useMatches[1];
                    }

                    // Assume it's in App\Events namespace if no use statement found
                    if (! str_contains($eventClass, '\\')) {
                        return 'App\\Events\\' . $eventClass;
                    }
                }
                return ltrim($eventClass, '\\');
            }
        }

        return null;
    }

    /**
     * Extract listener methods
     *
     * @return array<string, mixed>
     */
    private function extractMethods(ReflectionClass $reflection): array
    {
        $methods = [];
        $publicMethods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($publicMethods as $method) {
            // Skip magic methods and inherited methods
            if ($method->isConstructor()) {
                continue;
            }
            if ($method->isDestructor()) {
                continue;
            }
            if ($method->getDeclaringClass()->getName() !== $reflection->getName()) {
                continue;
            }
            $methodName = $method->getName();
            $methods[$methodName] = [
                'name' => $methodName,
                'parameters' => $this->extractMethodParameters($method),
                'return_type' => $method->getReturnType() instanceof ReflectionType ? $method->getReturnType()->__toString() : null,
            ];
        }

        return $methods;
    }

    /**
     * Extract method parameters
     *
     * @return array<int, array<string, mixed>>
     */
    private function extractMethodParameters(ReflectionMethod $method): array
    {
        $parameters = [];

        foreach ($method->getParameters() as $param) {
            $parameters[] = [
                'name' => $param->getName(),
                'type' => $param->getType() instanceof ReflectionType ? $param->getType()->__toString() : null,
                'default' => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null,
                'required' => ! $param->isOptional(),
            ];
        }

        return $parameters;
    }

    /**
     * Extract dependencies from constructor
     *
     * @return array<int, string>
     */
    private function extractDependencies(ReflectionClass $reflection): array
    {
        $dependencies = [];
        $constructor = $reflection->getConstructor();

        if ($constructor) {
            foreach ($constructor->getParameters() as $param) {
                $type = $param->getType();
                if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
                    $dependencies[] = $type->getName();
                }
            }
        }

        return array_unique($dependencies);
    }

    /**
     * Extract jobs dispatched by analyzing the file content
     *
     * @return array<int, string>
     */
    private function extractDispatchedJobs(string $filePath): array
    {
        $jobs = [];

        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            if ($content !== false) {
                // Look for job dispatching patterns
                $patterns = [
                    '/dispatch\(new\s+([\w\\\\]+)/i',           // dispatch(new JobName)
                    '/([\w\\\\]+)::dispatch\(/i',               // JobName::dispatch(
                    '/Bus::dispatch\(new\s+([\w\\\\]+)/i',      // Bus::dispatch(new JobName)
                ];

                foreach ($patterns as $pattern) {
                    if (preg_match_all($pattern, $content, $matches)) {
                        foreach ($matches[1] as $jobName) {
                            $jobs[] = $jobName;
                        }
                    }
                }
            }
        }

        return array_unique($jobs);
    }

    /**
     * Get class name from file path
     */
    private function getClassFromFile(string $filePath): ?string
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return null;
        }

        // Extract namespace
        $namespace = '';
        if (preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches)) {
            $namespace = $namespaceMatches[1];
        }

        // Extract class name
        $className = '';
        if (preg_match('/class\s+(\w+)/', $content, $classMatches)) {
            $className = $classMatches[1];
        }

        if ($namespace && $className) {
            return $namespace . '\\' . $className;
        }

        return $className ?: null;
    }
}
