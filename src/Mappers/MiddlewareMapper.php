<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Override;
use ReflectionClass;
use ReflectionNamedType;
use SplFileInfo;

class MiddlewareMapper extends BaseMapper
{
    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'middleware';
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    protected function getDefaultOptions(): array
    {
        return [
            'include_handle_method' => true,
            'include_dependencies' => true,
            'include_terminate_method' => true,
            'scan_path' => app_path('Http/Middleware'),
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
        $middlewarePath = $this->config('scan_path', app_path('Http/Middleware'));

        if (! is_string($middlewarePath) || ! File::isDirectory($middlewarePath)) {
            return $results;
        }

        $middlewareFiles = File::allFiles($middlewarePath);

        foreach ($middlewareFiles as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $middlewareData = $this->analyzeMiddlewareFile($file);
            if ($middlewareData !== null) {
                $results->put($middlewareData['class_name'], $middlewareData);
            }
        }

        /** @var Collection<string, array<string, mixed>> $results */
        return $results;
    }

    /**
     * Analyze a single middleware file
     *
     * @return array<string, mixed>|null
     */
    protected function analyzeMiddlewareFile(SplFileInfo $file): ?array
    {
        $content = File::get($file->getRealPath());
        $className = $this->extractClassName($content, $file);

        if (! $className || ! class_exists($className)) {
            return null;
        }

        try {
            $reflection = new ReflectionClass($className);

            // Check if it looks like a middleware
            if (! $this->looksLikeMiddleware($reflection)) {
                return null;
            }

            $parentClass = $reflection->getParentClass();
            $middlewareData = [
                'class_name' => $className,
                'file_path' => $file->getRealPath(),
                'namespace' => $reflection->getNamespaceName(),
                'short_name' => $reflection->getShortName(),
                'is_abstract' => $reflection->isAbstract(),
                'parent_class' => $parentClass ? $parentClass->getName() : null,
                'traits' => array_keys($reflection->getTraits()),
                'interfaces' => $reflection->getInterfaceNames(),
                'has_handle_method' => $reflection->hasMethod('handle'),
                'has_terminate_method' => $reflection->hasMethod('terminate'),
            ];

            // Add handle method info if enabled
            if ($this->config('include_handle_method')) {
                $middlewareData['handle_method'] = $this->extractHandleMethod($reflection);
            }

            // Add terminate method info if enabled
            if ($this->config('include_terminate_method')) {
                $middlewareData['terminate_method'] = $this->extractTerminateMethod($reflection);
            }

            // Add dependencies if enabled
            if ($this->config('include_dependencies')) {
                $middlewareData['dependencies'] = $this->extractDependencies($reflection);
            }

            return $middlewareData;
        } catch (Exception) {
            // Skip middleware that can't be analyzed
            return null;
        }
    }

    /**
     * Check if a class looks like middleware
     */
    protected function looksLikeMiddleware(ReflectionClass $reflection): bool
    {
        // Check if it has a handle method with Request parameter
        if ($reflection->hasMethod('handle')) {
            $handleMethod = $reflection->getMethod('handle');
            $parameters = $handleMethod->getParameters();

            if (count($parameters) >= 1) {
                $firstParam = $parameters[0];
                $type = $firstParam->getType();
                if ($type instanceof ReflectionNamedType &&
                    ($type->getName() === Request::class || is_subclass_of($type->getName(), Request::class))) {
                    return true;
                }
            }
        }

        // Check naming convention
        if (str_ends_with($reflection->getShortName(), 'Middleware')) {
            return true;
        }

        // Check namespace
        return str_contains($reflection->getNamespaceName(), 'Middleware');
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
        preg_match('/class\s+(\w+)/', $content, $classMatches);
        $className = $classMatches[1] ?? '';

        if ($className === '' || $className === '0') {
            return null;
        }

        return $namespace !== '' && $namespace !== '0' ? $namespace . '\\' . $className : $className;
    }

    /**
     * Extract handle method information
     *
     * @return array<string, mixed>|null
     */
    protected function extractHandleMethod(ReflectionClass $reflection): ?array
    {
        if (! $reflection->hasMethod('handle')) {
            return null;
        }

        $method = $reflection->getMethod('handle');

        $returnType = $method->getReturnType();
        $returnTypeName = 'mixed';
        if ($returnType instanceof ReflectionNamedType) {
            $returnTypeName = $returnType->getName();
        }

        return [
            'parameters_count' => $method->getNumberOfParameters(),
            'return_type' => $returnTypeName,
            'parameters' => array_map(
                fn ($param): array => [
                    'name' => $param->getName(),
                    'type' => $param->getType() instanceof ReflectionNamedType
                        ? $param->getType()->getName()
                        : 'mixed',
                    'optional' => $param->isOptional(),
                    'is_request' => $param->getType() instanceof ReflectionNamedType &&
                                  (is_subclass_of($param->getType()->getName(), Request::class) ||
                                   $param->getType()->getName() === Request::class),
                    'is_closure' => $param->getName() === 'next' ||
                                  ($param->getType() instanceof ReflectionNamedType &&
                                   $param->getType()->getName() === 'Closure'),
                ],
                $method->getParameters()
            ),
        ];
    }

    /**
     * Extract terminate method information
     *
     * @return array<string, mixed>|null
     */
    protected function extractTerminateMethod(ReflectionClass $reflection): ?array
    {
        if (! $reflection->hasMethod('terminate')) {
            return null;
        }

        $method = $reflection->getMethod('terminate');

        return [
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

    /**
     * Extract middleware dependencies from constructor
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
                    'is_service' => $this->looksLikeService($typeName),
                ];
            }
        }

        return $dependencies;
    }

    /**
     * Check if a type looks like a service
     */
    protected function looksLikeService(string $typeName): bool
    {
        return str_ends_with($typeName, 'Service') ||
               str_ends_with($typeName, 'Repository') ||
               str_ends_with($typeName, 'Manager') ||
               str_contains($typeName, 'Service');
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

        $middlewareWithHandle = 0;
        $middlewareWithTerminate = 0;
        $middlewareWithDependencies = 0;
        $totalParameters = 0;

        foreach ($this->results as $middleware) {
            if (is_array($middleware)) {
                if (isset($middleware['has_handle_method']) && $middleware['has_handle_method']) {
                    $middlewareWithHandle++;
                }
                if (isset($middleware['has_terminate_method']) && $middleware['has_terminate_method']) {
                    $middlewareWithTerminate++;
                }
                if (isset($middleware['dependencies']) && ! empty($middleware['dependencies'])) {
                    $middlewareWithDependencies++;
                }
                if (isset($middleware['handle_method']['parameters'])) {
                    $totalParameters += count($middleware['handle_method']['parameters']);
                }
            }
        }

        $summary['middleware_with_handle'] = $middlewareWithHandle;
        $summary['middleware_with_terminate'] = $middlewareWithTerminate;
        $summary['middleware_with_dependencies'] = $middlewareWithDependencies;
        $summary['total_handle_parameters'] = $totalParameters;

        return $summary;
    }
}
