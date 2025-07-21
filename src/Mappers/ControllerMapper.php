<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Override;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use SplFileInfo;

class ControllerMapper extends BaseMapper
{
    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'controllers';
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    protected function getDefaultOptions(): array
    {
        return [
            'include_actions' => true,
            'include_middleware' => true,
            'include_dependencies' => true,
            'include_request_types' => true,
            'scan_path' => app_path('Http/Controllers'),
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
        $controllerPath = $this->config('scan_path', app_path('Http/Controllers'));

        if (! is_string($controllerPath) || ! File::isDirectory($controllerPath)) {
            return $results;
        }

        $controllerFiles = File::allFiles($controllerPath);

        foreach ($controllerFiles as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $controllerData = $this->analyzeControllerFile($file);
            if ($controllerData !== null) {
                $results->put($controllerData['class_name'], $controllerData);
            }
        }

        /** @var Collection<string, array<string, mixed>> $results */
        return $results;
    }

    /**
     * Analyze a single controller file
     *
     * @return array<string, mixed>|null
     */
    protected function analyzeControllerFile(SplFileInfo $file): ?array
    {
        $content = File::get($file->getRealPath());
        $className = $this->extractClassName($content, $file);

        if (! $className || ! class_exists($className)) {
            return null;
        }

        try {
            $reflection = new ReflectionClass($className);

            // Check if it's a controller class
            if (! $this->isControllerClass($reflection)) {
                return null;
            }

            $parentClass = $reflection->getParentClass();
            $controllerData = [
                'class_name' => $className,
                'file_path' => $file->getRealPath(),
                'namespace' => $reflection->getNamespaceName(),
                'short_name' => $reflection->getShortName(),
                'is_abstract' => $reflection->isAbstract(),
                'parent_class' => $parentClass ? $parentClass->getName() : null,
                'traits' => array_keys($reflection->getTraits()),
                'interfaces' => $reflection->getInterfaceNames(),
                'is_api_controller' => $this->isApiController($reflection),
                'is_resource_controller' => $this->isResourceController($reflection),
            ];

            // Add actions if enabled
            if ($this->config('include_actions')) {
                $controllerData['actions'] = $this->extractActions($reflection);
            }

            // Add middleware if enabled
            if ($this->config('include_middleware')) {
                $controllerData['middleware'] = $this->extractMiddleware($content, $reflection);
            }

            // Add dependencies if enabled
            if ($this->config('include_dependencies')) {
                $controllerData['dependencies'] = $this->extractDependencies($reflection);
            }

            // Add request types if enabled
            if ($this->config('include_request_types')) {
                $controllerData['request_types'] = $this->extractRequestTypes($reflection);
            }

            return $controllerData;
        } catch (Exception) {
            // Skip controllers that can't be analyzed
            return null;
        }
    }

    /**
     * Check if a class is a controller
     */
    protected function isControllerClass(ReflectionClass $reflection): bool
    {
        // Check if extends Controller
        if ($reflection->isSubclassOf(Controller::class)) {
            return true;
        }

        // Check naming convention
        if (str_ends_with($reflection->getShortName(), 'Controller')) {
            return true;
        }

        // Check namespace
        return str_contains($reflection->getNamespaceName(), 'Controllers');
    }

    /**
     * Check if it's an API controller
     */
    protected function isApiController(ReflectionClass $reflection): bool
    {
        $namespace = $reflection->getNamespaceName();
        $shortName = $reflection->getShortName();

        return str_contains($namespace, 'Api') ||
               str_contains($shortName, 'Api') ||
               str_contains($namespace, 'API');
    }

    /**
     * Check if it's a resource controller
     */
    protected function isResourceController(ReflectionClass $reflection): bool
    {
        $resourceMethods = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $methodNames = array_map(fn ($method): string => $method->getName(), $methods);

        $resourceMethodsFound = array_intersect($resourceMethods, $methodNames);

        // If it has at least 4 resource methods, consider it a resource controller
        return count($resourceMethodsFound) >= 4;
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
     * Extract controller actions
     *
     * @return array<int, array<string, mixed>>
     */
    protected function extractActions(ReflectionClass $reflection): array
    {
        $actions = [];
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if ($method->getDeclaringClass()->getName() !== $reflection->getName()) {
                continue;
            }

            // Skip magic methods and constructor
            if (str_starts_with($method->getName(), '__')) {
                continue;
            }

            $returnType = $method->getReturnType();
            $returnTypeName = 'mixed';
            if ($returnType instanceof ReflectionNamedType) {
                $returnTypeName = $returnType->getName();
            }

            $actions[] = [
                'name' => $method->getName(),
                'return_type' => $returnTypeName,
                'parameters_count' => $method->getNumberOfParameters(),
                'parameters' => array_map(
                    fn ($param): array => [
                        'name' => $param->getName(),
                        'type' => $param->getType() instanceof ReflectionNamedType
                            ? $param->getType()->getName()
                            : 'mixed',
                        'optional' => $param->isOptional(),
                        'is_request' => $param->getType() instanceof ReflectionNamedType &&
                                      is_subclass_of($param->getType()->getName(), Request::class),
                    ],
                    $method->getParameters()
                ),
                'is_resource_action' => in_array($method->getName(), ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']),
            ];
        }

        return $actions;
    }

    /**
     * Extract middleware from constructor and content
     *
     * @return array<string, mixed>
     */
    protected function extractMiddleware(string $content, ReflectionClass $reflection): array
    {
        $middleware = [
            'constructor_middleware' => [],
            'method_middleware' => [],
        ];

        // Extract middleware from constructor
        if (preg_match_all('/\$this->middleware\([\'"]([^\'"]+)[\'"]([^)]*)\)/', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $middlewareName = $match[1];
                $options = $match[2];

                $middleware['constructor_middleware'][] = [
                    'name' => $middlewareName,
                    'options' => trim($options, ', '),
                ];
            }
        }

        return $middleware;
    }

    /**
     * Extract controller dependencies from constructor
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
     * Extract request types used in actions
     *
     * @return array<string, array<string>>
     */
    protected function extractRequestTypes(ReflectionClass $reflection): array
    {
        $requestTypes = [];
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if ($method->getDeclaringClass()->getName() !== $reflection->getName()) {
                continue;
            }

            $actionRequestTypes = [];
            foreach ($method->getParameters() as $parameter) {
                $type = $parameter->getType();
                if ($type instanceof ReflectionNamedType &&
                    is_subclass_of($type->getName(), Request::class)) {
                    $actionRequestTypes[] = $type->getName();
                }
            }

            if ($actionRequestTypes !== []) {
                $requestTypes[$method->getName()] = $actionRequestTypes;
            }
        }

        return $requestTypes;
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

        $actionsCount = 0;
        $middlewareCount = 0;
        $apiControllers = 0;
        $resourceControllers = 0;

        foreach ($this->results as $controller) {
            if (is_array($controller)) {
                if (isset($controller['actions'])) {
                    $actionsCount += count($controller['actions']);
                }
                if (isset($controller['middleware']['constructor_middleware'])) {
                    $middlewareCount += count($controller['middleware']['constructor_middleware']);
                }
                if (isset($controller['is_api_controller']) && $controller['is_api_controller']) {
                    $apiControllers++;
                }
                if (isset($controller['is_resource_controller']) && $controller['is_resource_controller']) {
                    $resourceControllers++;
                }
            }
        }

        $summary['actions_count'] = $actionsCount;
        $summary['middleware_count'] = $middlewareCount;
        $summary['api_controllers_count'] = $apiControllers;
        $summary['resource_controllers_count'] = $resourceControllers;

        return $summary;
    }
}
