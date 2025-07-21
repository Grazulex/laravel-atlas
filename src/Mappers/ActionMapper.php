<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionException;

class ActionMapper extends BaseMapper
{
    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'actions';
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
            'include_invokable' => true,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function performScan(): Collection
    {
        $basePath = app_path();
        
        // Standard action paths
        $actionPaths = [
            $basePath . '/Actions',
            $basePath . '/Domain/Actions',
            $basePath . '/App/Actions',
        ];

        /** @var Collection<string, array<string, mixed>> $actions */
        $actions = collect();

        foreach ($actionPaths as $path) {
            if (is_dir($path)) {
                $this->scanDirectoryForActions($path, $actions);
            }
        }

        return $actions;
    }

    /**
     * Scan directory for Action classes
     *
     * @param  Collection<string, array<string, mixed>>  $actions
     */
    private function scanDirectoryForActions(string $path, Collection $actions): void
    {
        $files = glob($path . '/*.php');
        if ($files === false) {
            return;
        }

        foreach ($files as $file) {
            $className = $this->getClassFromFile($file);
            if ($className && $this->isAction($className)) {
                $actionData = $this->analyzeAction($className, $file);
                if ($actionData !== null) {
                    $actions->put($className, $actionData);
                }
            }
        }

        // Scan subdirectories recursively
        $subdirs = glob($path . '/*', GLOB_ONLYDIR);
        if ($subdirs !== false) {
            foreach ($subdirs as $subdir) {
                $this->scanDirectoryForActions($subdir, $actions);
            }
        }
    }

    /**
     * Check if a class is an Action
     */
    private function isAction(string $className): bool
    {
        // Check if class name ends with 'Action'
        if (! str_ends_with($className, 'Action')) {
            return false;
        }

        try {
            $reflection = new ReflectionClass($className);
            
            // Check if it's in an Actions namespace or directory
            if (str_contains($className, '\\Actions\\')) {
                return true;
            }

            // Check if it has an __invoke method (common pattern for actions)
            if ($reflection->hasMethod('__invoke')) {
                return true;
            }

            // Check if it has a handle method (another common pattern)
            if ($reflection->hasMethod('handle')) {
                return true;
            }

        } catch (ReflectionException) {
            return false;
        }

        return false;
    }

    /**
     * Analyze a specific Action class
     *
     * @return array<string, mixed>|null
     */
    private function analyzeAction(string $className, string $filePath): ?array
    {
        try {
            $reflection = new ReflectionClass($className);
            
            if ($reflection->isAbstract() || $reflection->isInterface() || $reflection->isTrait()) {
                return null;
            }

            $actionData = [
                'class_name' => $className,
                'file_path' => $filePath,
                'type' => $this->getActionType($reflection),
            ];

            // Extract methods
            if ($this->config('include_methods')) {
                $actionData['methods'] = $this->extractMethods($reflection);
            }

            // Extract dependencies from constructor
            if ($this->config('include_dependencies')) {
                $actionData['dependencies'] = $this->extractDependencies($reflection);
            }

            // Extract events dispatched
            if ($this->config('include_events')) {
                $actionData['events'] = $this->extractEvents($reflection, $filePath);
            }

            // Check if it's invokable
            if ($this->config('include_invokable')) {
                $actionData['is_invokable'] = $reflection->hasMethod('__invoke');
            }

            return $actionData;

        } catch (ReflectionException $e) {
            return [
                'class_name' => $className,
                'error' => 'Failed to reflect class: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Determine the action type based on its structure
     */
    private function getActionType(ReflectionClass $reflection): string
    {
        // Check for common action patterns
        if ($reflection->hasMethod('__invoke')) {
            return 'invokable';
        }
        
        if ($reflection->hasMethod('handle')) {
            return 'handle';
        }

        // Check for CRUD patterns
        $methods = $reflection->getMethods();
        $methodNames = array_map(fn($method) => $method->getName(), $methods);
        
        if (in_array('execute', $methodNames)) {
            return 'execute';
        }

        return 'custom';
    }

    /**
     * Extract action methods
     *
     * @return array<string, mixed>
     */
    private function extractMethods(ReflectionClass $reflection): array
    {
        $methods = [];
        $publicMethods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($publicMethods as $method) {
            // Skip magic methods and inherited methods
            if ($method->isConstructor() || 
                $method->isDestructor() || 
                $method->getDeclaringClass()->getName() !== $reflection->getName()) {
                continue;
            }

            $methodName = $method->getName();
            $methods[$methodName] = [
                'name' => $methodName,
                'parameters' => $this->extractMethodParameters($method),
                'return_type' => $method->getReturnType() ? $method->getReturnType()->__toString() : null,
            ];
        }

        return $methods;
    }

    /**
     * Extract method parameters
     *
     * @return array<int, array<string, mixed>>
     */
    private function extractMethodParameters(\ReflectionMethod $method): array
    {
        $parameters = [];
        
        foreach ($method->getParameters() as $param) {
            $parameters[] = [
                'name' => $param->getName(),
                'type' => $param->getType() ? $param->getType()->__toString() : null,
                'default' => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null,
                'required' => !$param->isOptional(),
            ];
        }

        return $parameters;
    }

    /**
     * Extract dependencies from constructor
     *
     * @return array<string, mixed>
     */
    private function extractDependencies(ReflectionClass $reflection): array
    {
        $dependencies = [];
        $constructor = $reflection->getConstructor();

        if ($constructor) {
            foreach ($constructor->getParameters() as $param) {
                $type = $param->getType();
                if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                    $dependencies[] = $type->getName();
                }
            }
        }

        return array_unique($dependencies);
    }

    /**
     * Extract events dispatched by analyzing the file content
     *
     * @return array<string, mixed>
     */
    private function extractEvents(ReflectionClass $reflection, string $filePath): array
    {
        $events = [];
        
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            if ($content !== false) {
                // Look for event dispatching patterns
                $patterns = [
                    '/event\(new\s+(\w+)/i',                    // event(new EventName)
                    '/Event::dispatch\(new\s+(\w+)/i',          // Event::dispatch(new EventName)
                    '/([\w\\\\]+)::dispatch\(/i',               // EventName::dispatch(
                    '/dispatch\(new\s+([\w\\\\]+)/i',          // dispatch(new EventName)
                ];

                foreach ($patterns as $pattern) {
                    if (preg_match_all($pattern, $content, $matches)) {
                        foreach ($matches[1] as $eventName) {
                            $events[] = $eventName;
                        }
                    }
                }
            }
        }

        return array_unique($events);
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
