<?php

declare(strict_types=1);

namespace Grazulex\LaravelAtlas\Mappers;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Override;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use SplFileInfo;

class ServiceMapper extends BaseMapper
{
    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'services';
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    protected function getDefaultOptions(): array
    {
        return [
            'include_dependencies' => true,
            'include_methods' => true,
            'include_interfaces' => true,
            'include_bindings' => true,
            'scan_paths' => [
                app_path('Services'),
                app_path('Domain'),
                app_path('Application'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return Collection<string, array<string, mixed>>
     */
    protected function performScan(): Collection
    {
        $results = collect();
        $scanPaths = $this->config('scan_paths', [app_path('Services')]);

        foreach ($scanPaths as $scanPath) {
            if (! is_string($scanPath) || ! File::isDirectory($scanPath)) {
                continue;
            }

            $serviceFiles = File::allFiles($scanPath);

            foreach ($serviceFiles as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $serviceData = $this->analyzeServiceFile($file);
                if ($serviceData !== null) {
                    $results->put($serviceData['class_name'], $serviceData);
                }
            }
        }

        /** @var Collection<string, array<string, mixed>> $results */
        return $results;
    }

    /**
     * Analyze a single service file
     *
     * @return array<string, mixed>|null
     */
    protected function analyzeServiceFile(SplFileInfo $file): ?array
    {
        $content = File::get($file->getRealPath());
        $className = $this->extractClassName($content, $file);

        if (! $className || ! class_exists($className)) {
            return null;
        }

        try {
            $reflection = new ReflectionClass($className);

            // Skip if it's not a service-like class
            if (! $this->looksLikeServiceClass($reflection)) {
                return null;
            }

            $serviceData = [
                'class_name' => $className,
                'file_path' => $file->getRealPath(),
                'namespace' => $reflection->getNamespaceName(),
                'short_name' => $reflection->getShortName(),
                'is_abstract' => $reflection->isAbstract(),
                'is_interface' => $reflection->isInterface(),
                'parent_class' => $reflection->getParentClass()?->getName(),
                'traits' => array_keys($reflection->getTraits()),
                'interfaces' => $reflection->getInterfaceNames(),
            ];

            // Add dependencies if enabled
            if ($this->config('include_dependencies')) {
                $serviceData['dependencies'] = $this->extractDependencies($reflection);
            }

            // Add methods if enabled
            if ($this->config('include_methods')) {
                $serviceData['methods'] = $this->extractMethods($reflection);
            }

            // Add interface information if enabled
            if ($this->config('include_interfaces')) {
                $serviceData['interface_info'] = $this->extractInterfaceInfo($reflection);
            }

            return $serviceData;
        } catch (Exception) {
            // Skip services that can't be analyzed
            return null;
        }
    }

    /**
     * Check if a class looks like a service
     */
    protected function looksLikeServiceClass(ReflectionClass $reflection): bool
    {
        $className = $reflection->getShortName();
        $namespace = $reflection->getNamespaceName();

        // Check naming patterns
        if (str_ends_with($className, 'Service') || 
            str_ends_with($className, 'Repository') ||
            str_ends_with($className, 'Manager') ||
            str_ends_with($className, 'Handler') ||
            str_ends_with($className, 'Provider')) {
            return true;
        }

        // Check namespace patterns
        if (str_contains($namespace, 'Services') ||
            str_contains($namespace, 'Domain') ||
            str_contains($namespace, 'Application')) {
            return true;
        }

        // Check if it has service-like methods
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $serviceMethodPatterns = ['handle', 'execute', 'process', 'perform', 'run', 'create', 'update', 'delete', 'find', 'get'];
        
        foreach ($methods as $method) {
            foreach ($serviceMethodPatterns as $pattern) {
                if (str_starts_with(strtolower($method->getName()), $pattern)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Extract class name from file content
     */
    protected function extractClassName(string $content, SplFileInfo $file): ?string
    {
        // Extract namespace
        preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches);
        $namespace = $namespaceMatches[1] ?? '';

        // Extract class name
        preg_match('/(class|interface|trait)\s+(\w+)/', $content, $classMatches);
        $className = $classMatches[2] ?? '';

        if ($className === '' || $className === '0') {
            return null;
        }

        return $namespace !== '' && $namespace !== '0' ? $namespace . '\\' . $className : $className;
    }

    /**
     * Extract service dependencies from constructor
     *
     * @return array<int, array<string, mixed>>
     */
    protected function extractDependencies(ReflectionClass $reflection): array
    {
        $dependencies = [];
        $constructor = $reflection->getConstructor();

        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                $type = $parameter->getType();
                $typeName = 'mixed';
                if ($type instanceof ReflectionNamedType) {
                    $typeName = $type->getName();
                }

                $dependencies[] = [
                    'name' => $parameter->getName(),
                    'type' => $typeName,
                    'optional' => $parameter->isOptional(),
                    'default' => $parameter->isDefaultValueAvailable()
                        ? $parameter->getDefaultValue()
                        : null,
                ];
            }
        }

        return $dependencies;
    }

    /**
     * Extract service methods
     *
     * @return array<int, array<string, mixed>>
     */
    protected function extractMethods(ReflectionClass $reflection): array
    {
        $methods = [];
        $reflectionMethods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($reflectionMethods as $method) {
            if ($method->getDeclaringClass()->getName() !== $reflection->getName()) {
                continue;
            }

            // Skip magic methods
            if (str_starts_with($method->getName(), '__')) {
                continue;
            }

            $returnType = $method->getReturnType();
            $returnTypeName = 'mixed';
            if ($returnType instanceof ReflectionNamedType) {
                $returnTypeName = $returnType->getName();
            }

            $methods[] = [
                'name' => $method->getName(),
                'is_static' => $method->isStatic(),
                'is_abstract' => $method->isAbstract(),
                'visibility' => $this->getMethodVisibility($method),
                'return_type' => $returnTypeName,
                'parameters_count' => $method->getNumberOfParameters(),
                'parameters' => array_map(
                    fn ($param): array => [
                        'name' => $param->getName(),
                        'type' => $param->getType() instanceof ReflectionNamedType 
                            ? $param->getType()->getName() 
                            : 'mixed',
                        'optional' => $param->isOptional(),
                    ],
                    $method->getParameters()
                ),
            ];
        }

        return $methods;
    }

    /**
     * Extract interface information
     *
     * @return array<string, mixed>
     */
    protected function extractInterfaceInfo(ReflectionClass $reflection): array
    {
        $interfaceInfo = [
            'is_interface' => $reflection->isInterface(),
            'implements' => $reflection->getInterfaceNames(),
            'interface_methods' => [],
        ];

        if ($reflection->isInterface()) {
            $interfaceInfo['interface_methods'] = array_map(
                fn ($method): string => $method->getName(),
                $reflection->getMethods()
            );
        }

        return $interfaceInfo;
    }

    /**
     * Get method visibility
     */
    protected function getMethodVisibility(ReflectionMethod $method): string
    {
        if ($method->isPublic()) {
            return 'public';
        }
        if ($method->isProtected()) {
            return 'protected';
        }
        return 'private';
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    #[Override]
    protected function getSummary(): array
    {
        $summary = parent::getSummary();

        $methodCount = 0;
        $dependencyCount = 0;
        $interfaceCount = 0;

        foreach ($this->results as $service) {
            if (is_array($service)) {
                if (isset($service['methods'])) {
                    $methodCount += count($service['methods']);
                }
                if (isset($service['dependencies'])) {
                    $dependencyCount += count($service['dependencies']);
                }
                if (isset($service['interface_info']['is_interface']) && $service['interface_info']['is_interface']) {
                    $interfaceCount++;
                }
            }
        }

        $summary['methods_count'] = $methodCount;
        $summary['dependencies_count'] = $dependencyCount;
        $summary['interfaces_count'] = $interfaceCount;
        $summary['services_with_dependencies'] = $this->results->filter(
            fn ($service): bool => is_array($service) && ! empty($service['dependencies'])
        )->count();

        return $summary;
    }
}
