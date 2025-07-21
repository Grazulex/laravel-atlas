<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use ReflectionNamedType;
use ReflectionMethod;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionException;

class ObserverMapper extends BaseMapper
{
    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'observers';
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function performScan(): Collection
    {
        $basePath = app_path();

        // Standard observer paths
        $observerPaths = [
            $basePath . '/Observers',
            $basePath . '/Models/Observers',
            $basePath . '/Domain/Observers',
        ];

        /** @var Collection<string, array<string, mixed>> $observers */
        $observers = collect();

        foreach ($observerPaths as $path) {
            if (is_dir($path)) {
                $pathObservers = $this->scanDirectory($path);
                foreach ($pathObservers as $observer) {
                    $observers->put($observer['class_name'], $observer);
                }
            }
        }

        return $observers;
    }

    /**
     * Scan directory for observer files
     *
     * @return array<int, array<string, mixed>>
     */
    protected function scanDirectory(string $directory): array
    {
        $observers = [];
        $files = glob($directory . '/*.php');

        if ($files !== false) {
            foreach ($files as $file) {
                $className = $this->getClassNameFromFile($file);
                if ($className && $this->isObserver($className)) {
                    $observers[] = $this->extractObserverData($className);
                }
            }
        }

        return $observers;
    }

    /**
     * Check if class is an observer
     */
    protected function isObserver(string $className): bool
    {
        try {
            /** @var class-string $className */
            $reflection = new ReflectionClass($className);

            // Check if class name ends with 'Observer'
            if (! str_ends_with($className, 'Observer')) {
                return false;
            }

            // Check if it has typical observer methods
            $observerMethods = ['creating', 'created', 'updating', 'updated', 'saving', 'saved', 'deleting', 'deleted', 'restoring', 'restored'];
            foreach ($observerMethods as $method) {
                if ($reflection->hasMethod($method)) {
                    return true;
                }
            }

            return false;
        } catch (ReflectionException) {
            return false;
        }
    }

    /**
     * Extract observer data
     *
     * @return array<string, mixed>
     */
    protected function extractObserverData(string $className): array
    {
        try {
            /** @var class-string $className */
            $reflection = new ReflectionClass($className);

            $data = [
                'class_name' => $className,
                'model' => $this->extractObservedModel($className),
                'methods' => [],
                'dependencies' => [],
                'events' => [],
            ];

            // Extract methods if enabled
            if ($this->config('include_methods')) {
                $data['methods'] = $this->extractObserverMethods($reflection);
            }

            // Extract dependencies if enabled
            if ($this->config('include_dependencies')) {
                $data['dependencies'] = $this->extractDependencies($reflection);
            }

            // Extract events if enabled
            if ($this->config('include_events')) {
                $data['events'] = $this->extractEventsTriggered($reflection);
            }

            return $data;
        } catch (ReflectionException $e) {
            return [
                'class_name' => $className,
                'error' => 'Failed to reflect class: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Extract the model this observer observes
     */
    protected function extractObservedModel(string $className): ?string
    {
        // Remove 'Observer' suffix and namespace to get model name
        $baseName = str_replace('Observer', '', class_basename($className));

        // Try to find corresponding model class
        $possibleModelClasses = [
            "App\\Models\\{$baseName}",
            "App\\{$baseName}",
            $baseName,
        ];

        foreach ($possibleModelClasses as $modelClass) {
            if (class_exists($modelClass)) {
                return $modelClass;
            }
        }

        return $baseName; // Return base name if model class not found
    }

    /**
     * Extract observer methods (lifecycle hooks)
     *
     * @return array<string, array<string, mixed>>
     */
    protected function extractObserverMethods(ReflectionClass $reflection): array
    {
        $methods = [];
        $observerMethods = ['creating', 'created', 'updating', 'updated', 'saving', 'saved', 'deleting', 'deleted', 'restoring', 'restored'];

        foreach ($observerMethods as $methodName) {
            if ($reflection->hasMethod($methodName)) {
                $method = $reflection->getMethod($methodName);
                $methods[$methodName] = [
                    'name' => $methodName,
                    'parameters' => $this->extractMethodParameters($method),
                    'type' => $this->getObserverMethodType($methodName),
                ];
            }
        }

        return $methods;
    }

    /**
     * Get observer method type (before/after)
     */
    protected function getObserverMethodType(string $methodName): string
    {
        $beforeMethods = ['creating', 'updating', 'saving', 'deleting', 'restoring'];
        $afterMethods = ['created', 'updated', 'saved', 'deleted', 'restored'];

        if (in_array($methodName, $beforeMethods)) {
            return 'before';
        }

        if (in_array($methodName, $afterMethods)) {
            return 'after';
        }

        return 'other';
    }

    /**
     * Extract dependencies from constructor
     *
     * @return array<int, string>
     */
    protected function extractDependencies(ReflectionClass $reflection): array
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
     * Extract events that might be triggered by this observer
     *
     * @return array<int, string>
     */
    protected function extractEventsTriggered(ReflectionClass $reflection): array
    {
        // This would require analyzing the method bodies for event dispatches
        // For now, return empty array - could be enhanced with AST parsing
        return [];
    }

    /**
     * Get class name from file path
     */
    protected function getClassNameFromFile(string $filePath): ?string
    {
        $content = file_get_contents($filePath);

        if ($content === false) {
            return null;
        }

        // Extract namespace
        $namespace = preg_match('/^namespace\s+([^;]+);/m', $content, $namespaceMatches) ? $namespaceMatches[1] : '';

        // Extract class name
        if (preg_match('/^class\s+(\w+)/m', $content, $classMatches)) {
            $className = $classMatches[1];

            if ($namespace !== '' && $namespace !== '0') {
                return $namespace . '\\' . $className;
            }

            return $className;
        }

        return null;
    }

    /**
     * Extract method parameters
     *
     * @return array<int, array<string, mixed>>
     */
    protected function extractMethodParameters(ReflectionMethod $method): array
    {
        $parameters = [];

        foreach ($method->getParameters() as $param) {
            $type = $param->getType();
            $parameters[] = [
                'name' => $param->getName(),
                'type' => $type instanceof ReflectionNamedType ? $type->getName() : null,
                'default' => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null,
                'required' => ! $param->isOptional(),
            ];
        }

        return $parameters;
    }
}
