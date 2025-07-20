<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use SplFileInfo;
use Throwable;

class PolicyMapper extends BaseMapper
{
    public function getType(): string
    {
        return 'policies';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultOptions(): array
    {
        return [
            'include_abilities' => true,
            'include_model_binding' => true,
            'include_methods' => true,
            'include_dependencies' => true,
            'scan_path' => app_path('Policies'),
        ];
    }

    /**
     * @return Collection<string, array<string, mixed>>
     */
    protected function performScan(): Collection
    {
        $results = collect();
        $scanPath = $this->config('scan_path', app_path('Policies'));

        if (! is_string($scanPath) || ! File::isDirectory($scanPath)) {
            return $results;
        }

        $policyFiles = File::allFiles($scanPath);

        foreach ($policyFiles as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $policyData = $this->analyzePolicyFile($file);
            if ($policyData !== null) {
                $results->put($policyData['class_name'], $policyData);
            }
        }

        return $results;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function analyzePolicyFile(SplFileInfo $file): ?array
    {
        $filePath = $file->getPathname();
        $className = $this->extractClassName($filePath);

        if (! $className || ! $this->isPolicyClass($filePath)) {
            return null;
        }

        return $this->analyzePolicyClass($className, $filePath);
    }

    private function isPolicyClass(string $filePath): bool
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return false;
        }

        return str_contains($content, 'class ') &&
               (str_contains($content, 'Policy') ||
                preg_match('/function\s+(view|create|update|delete|viewAny|forceDelete|restore)\s*\(/', $content));
    }

    /**
     * @return array<string, mixed>|null
     */
    private function analyzePolicyClass(string $className, string $filePath): ?array
    {
        try {
            if (! class_exists($className)) {
                return null;
            }

            $reflection = new ReflectionClass($className);
            $content = file_get_contents($filePath);

            if ($content === false) {
                return null;
            }

            return [
                'class_name' => $reflection->getShortName(),
                'name' => $reflection->getShortName(),
                'full_name' => $reflection->getName(),
                'file' => $filePath,
                'namespace' => $reflection->getNamespaceName(),
                'abilities' => $this->config('include_abilities', true) ? $this->extractAbilities($reflection) : [],
                'model_binding' => $this->config('include_model_binding', true) ? $this->extractModelBinding($content) : null,
                'methods' => $this->config('include_methods', true) ? $this->extractMethods($reflection) : [],
                'dependencies' => $this->config('include_dependencies', true) ? $this->extractDependencies($reflection) : [],
                'line_count' => substr_count($content, "\n") + 1,
            ];
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function extractAbilities(ReflectionClass $reflection): array
    {
        $abilities = [];
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $policyMethods = ['viewAny', 'view', 'create', 'update', 'delete', 'restore', 'forceDelete'];

        foreach ($methods as $method) {
            if ($method->getDeclaringClass()->getName() === $reflection->getName()) {
                $methodName = $method->getName();

                if (in_array($methodName, $policyMethods) || str_starts_with($methodName, 'can')) {
                    $abilities[] = [
                        'name' => $methodName,
                        'parameters' => $this->extractMethodParameters($method),
                        'is_standard' => in_array($methodName, $policyMethods),
                    ];
                }
            }
        }

        return $abilities;
    }

    private function extractModelBinding(string $content): ?string
    {
        // Look for model type hints in method signatures
        if (preg_match('/function\s+\w+\s*\([^)]*(\w+)\s+\$\w+/', $content, $matches)) {
            return $matches[1];
        }

        // Look for model imports
        if (preg_match('/use\s+App\\\\Models\\\\(\w+)/', $content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function extractMethods(ReflectionClass $reflection): array
    {
        $methods = [];
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->getDeclaringClass()->getName() === $reflection->getName()) {
                $methods[] = [
                    'name' => $method->getName(),
                    'parameters' => $this->extractMethodParameters($method),
                    'visibility' => 'public',
                ];
            }
        }

        return $methods;
    }

    /**
     * @return array<string>
     */
    private function extractDependencies(ReflectionClass $reflection): array
    {
        $dependencies = [];

        // Check constructor dependencies
        $constructor = $reflection->getConstructor();
        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                $type = $parameter->getType();
                if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
                    $dependencies[] = $type->getName();
                }
            }
        }

        return $dependencies;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function extractMethodParameters(ReflectionMethod $method): array
    {
        $parameters = [];

        foreach ($method->getParameters() as $param) {
            $type = $param->getType();
            $typeName = 'mixed';

            if ($type instanceof ReflectionNamedType) {
                $typeName = $type->getName();
            }

            $parameters[] = [
                'name' => $param->getName(),
                'type' => $typeName,
                'optional' => $param->isOptional(),
                'default' => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null,
            ];
        }

        return $parameters;
    }

    private function extractClassName(string $filePath): ?string
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return null;
        }

        // Extract namespace
        $namespace = '';
        if (preg_match('/namespace\s+([^;]+)/', $content, $matches)) {
            $namespace = $matches[1] . '\\';
        }

        // Extract class name
        if (preg_match('/class\s+(\w+)/', $content, $matches)) {
            return $namespace . $matches[1];
        }

        return null;
    }
}
