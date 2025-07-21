<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

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
        ];

        /** @var Collection<string, array<string, mixed>> $observers */
        $observers = collect();

        foreach ($observerPaths as $path) {
            if (is_dir($path)) {
                $foundObservers = $this->scanDirectory($path);
                foreach ($foundObservers as $observer) {
                    $observers->put($observer['class_name'], $observer);
                }
            }
        }

        return $observers;
    }

    /**
     * {@inheritdoc}
     */
    public function scan(array $options = []): array
    {
        // Merge options with config
        $this->config = array_merge($this->config, $options);
        
        // Perform the scan
        $this->results = $this->performScan();
        
        return [
            'type' => $this->getType(),
            'data' => $this->results->values()->toArray(),
            'summary' => $this->getSummary(),
        ];
    }

    /**
     * Scan directory for observer files
     */
    protected function scanDirectory(string $directory): array
    {
        $observers = [];
        $files = glob($directory . '/*.php');

        foreach ($files as $file) {
            $className = $this->getClassNameFromFile($file);
            if ($className && $this->isObserver($className)) {
                $observers[] = $this->extractObserverData($className);
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
            $reflection = new ReflectionClass($className);
            
            // Check if class name ends with 'Observer'
            if (!str_ends_with($className, 'Observer')) {
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
     */
    protected function extractObserverData(string $className): array
    {
        try {
            $reflection = new ReflectionClass($className);
            
            $data = [
                'class_name' => $className,
                'model' => $this->extractObservedModel($className),
                'methods' => [],
            ];

            if ($this->config('include_methods')) {
                $data['methods'] = $this->extractObserverMethods($reflection);
            }

            if ($this->config('include_dependencies')) {
                $data['dependencies'] = $this->extractDependencies($reflection);
            }

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
     * Extract the model this observer is observing
     */
    protected function extractObservedModel(string $observerClassName): string
    {
        // Try to infer model from observer name
        // e.g., UserObserver -> User
        $baseName = str_replace('Observer', '', class_basename($observerClassName));
        
        // Try common model namespaces
        $modelNamespaces = [
            'App\\Models\\',
            'App\\',
        ];

        foreach ($modelNamespaces as $namespace) {
            $modelClass = $namespace . $baseName;
            if (class_exists($modelClass)) {
                return $modelClass;
            }
        }

        return $baseName; // Return base name if model class not found
    }

    /**
     * Extract observer methods (lifecycle hooks)
     */
    protected function extractObserverMethods(ReflectionClass $reflection): array
    {
        $methods = [];
        $observerMethods = [
            'creating', 'created', 'updating', 'updated', 
            'saving', 'saved', 'deleting', 'deleted', 
            'restoring', 'restored', 'replicating'
        ];

        foreach ($observerMethods as $methodName) {
            if ($reflection->hasMethod($methodName)) {
                $method = $reflection->getMethod($methodName);
                if ($method->isPublic()) {
                    $methods[] = [
                        'name' => $methodName,
                        'lifecycle_event' => $this->getLifecycleType($methodName),
                        'parameters' => $this->extractMethodParameters($method),
                        'visibility' => 'public',
                    ];
                }
            }
        }

        return $methods;
    }

    /**
     * Get lifecycle event type
     */
    protected function getLifecycleType(string $methodName): string
    {
        $beforeEvents = ['creating', 'updating', 'saving', 'deleting', 'restoring'];
        $afterEvents = ['created', 'updated', 'saved', 'deleted', 'restored'];
        
        if (in_array($methodName, $beforeEvents)) {
            return 'before';
        } elseif (in_array($methodName, $afterEvents)) {
            return 'after';
        }
        
        return 'other';
    }

    /**
     * Extract dependencies from constructor
     */
    protected function extractDependencies(ReflectionClass $reflection): array
    {
        $dependencies = [];
        
        if ($reflection->hasMethod('__construct')) {
            $constructor = $reflection->getMethod('__construct');
            foreach ($constructor->getParameters() as $parameter) {
                $type = $parameter->getType();
                if ($type && !$type->isBuiltin()) {
                    $dependencies[] = $type->getName();
                }
            }
        }

        return $dependencies;
    }

    /**
     * Extract events that might be triggered by this observer
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
        
        // Extract namespace
        if (preg_match('/^namespace\s+([^;]+);/m', $content, $namespaceMatches)) {
            $namespace = $namespaceMatches[1];
        } else {
            $namespace = '';
        }

        // Extract class name
        if (preg_match('/^class\s+([^\s{]+)/m', $content, $classMatches)) {
            $className = $classMatches[1];
            return $namespace ? $namespace . '\\' . $className : $className;
        }

        return null;
    }

    /**
     * Extract method parameters
     */
    protected function extractMethodParameters(\ReflectionMethod $method): array
    {
        $parameters = [];
        
        foreach ($method->getParameters() as $parameter) {
            $paramData = [
                'name' => $parameter->getName(),
                'type' => $parameter->getType() ? $parameter->getType()->getName() : 'mixed',
                'optional' => $parameter->isOptional(),
                'default' => null,
            ];

            if ($parameter->isDefaultValueAvailable()) {
                $paramData['default'] = $parameter->getDefaultValue();
            }

            $parameters[] = $paramData;
        }

        return $parameters;
    }
}
