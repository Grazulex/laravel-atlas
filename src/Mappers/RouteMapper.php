<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Override;

class RouteMapper extends BaseMapper
{
    protected Router $router;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->router = app('router');
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'routes';
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    protected function getDefaultOptions(): array
    {
        return [
            'include_middleware' => true,
            'include_controllers' => true,
            'include_parameters' => true,
            'group_by_prefix' => false,
            'group_by_middleware' => false,
            'exclude_vendor_routes' => true,
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
        $routes = $this->router->getRoutes();

        /** @var Route $route */
        foreach ($routes as $route) {
            if ($this->shouldSkipRoute($route)) {
                continue;
            }

            $routeData = $this->analyzeRoute($route);
            $routeKey = $this->generateRouteKey($route);

            $results->put($routeKey, $routeData);
        }

        // Group routes if requested
        // Temporarily disable grouping to fix type issues
        // if ($this->config('group_by_prefix')) {
        //     $results = $this->groupByPrefix($results);
        // }

        // if ($this->config('group_by_middleware')) {
        //     $results = $this->groupByMiddleware($results);
        // }

        /** @var Collection<string, array<string, mixed>> $results */
        return $results;
    }

    /**
     * Analyze a single route
     *
     * @return array<string, mixed>
     */
    protected function analyzeRoute(Route $route): array
    {
        $routeData = [
            'uri' => $route->uri(),
            'methods' => $route->methods(),
            'name' => $route->getName(),
            'action' => $route->getAction(),
            'domain' => $route->getDomain(),
            'prefix' => $this->extractPrefix($route),
        ];

        // Add middleware if enabled
        if ($this->config('include_middleware')) {
            $routeData['middleware'] = $route->gatherMiddleware();
        }

        // Add controller info if enabled
        if ($this->config('include_controllers')) {
            $routeData['controller'] = $this->extractControllerInfo($route);
        }

        // Add parameters if enabled
        if ($this->config('include_parameters')) {
            $routeData['parameters'] = $this->extractParameters($route);
        }

        return $routeData;
    }

    /**
     * Extract controller information from route
     *
     * @return array<string, mixed>|null
     */
    protected function extractControllerInfo(Route $route): ?array
    {
        $action = $route->getAction();

        // Handle modern Laravel route formats
        if (isset($action['uses'])) {
            if (is_string($action['uses'])) {
                if (str_contains($action['uses'], '@')) {
                    [$class, $method] = explode('@', $action['uses']);
                    return [
                        'class' => $class,
                        'method' => $method,
                        'namespace' => $this->extractNamespace($class),
                        'short_name' => class_basename($class),
                    ];
                } else {
                    // Invokable controller
                    return [
                        'class' => $action['uses'],
                        'method' => '__invoke',
                        'namespace' => $this->extractNamespace($action['uses']),
                        'short_name' => class_basename($action['uses']),
                    ];
                }
            } elseif (is_array($action['uses']) && count($action['uses']) === 2) {
                [$class, $method] = $action['uses'];
                return [
                    'class' => $class,
                    'method' => $method,
                    'namespace' => $this->extractNamespace($class),
                    'short_name' => class_basename($class),
                ];
            }
        }

        // Legacy controller format
        if (isset($action['controller']) && is_string($action['controller'])) {
            $controller = $action['controller'];

            if (str_contains($controller, '@')) {
                [$class, $method] = explode('@', $controller);
                return [
                    'class' => $class,
                    'method' => $method,
                    'namespace' => $this->extractNamespace($class),
                    'short_name' => class_basename($class),
                ];
            } else {
                // Invokable controller
                return [
                    'class' => $controller,
                    'method' => '__invoke',
                    'namespace' => $this->extractNamespace($controller),
                    'short_name' => class_basename($controller),
                ];
            }
        }

        // Array format [Controller::class, 'method']
        if (is_array($action['controller'] ?? null) && count($action['controller']) === 2) {
            [$class, $method] = $action['controller'];
            return [
                'class' => $class,
                'method' => $method,
                'namespace' => $this->extractNamespace($class),
                'short_name' => class_basename($class),
            ];
        }

        return null;
    }

    /**
     * Extract route parameters
     *
     * @return array<int, array<string, mixed>>
     */
    protected function extractParameters(Route $route): array
    {
        $parameters = [];

        // Extract parameters from URI pattern
        preg_match_all('/\{([^}?]+)\??\}/', $route->uri(), $matches);

        foreach ($matches[1] as $param) {
            $isOptional = str_ends_with($matches[0][array_search($param, $matches[1])], '?}');
            $parameters[] = [
                'name' => $param,
                'optional' => $isOptional,
                'type' => $this->guessParameterType($param),
            ];
        }

        return $parameters;
    }

    /**
     * Guess parameter type from name
     */
    protected function guessParameterType(string $param): string
    {
        if (str_ends_with($param, '_id') || $param === 'id') {
            return 'integer';
        }

        if (in_array($param, ['slug', 'token', 'hash'])) {
            return 'string';
        }

        return 'mixed';
    }

    /**
     * Extract namespace from class name
     */
    protected function extractNamespace(string $class): string
    {
        $parts = explode('\\', $class);
        array_pop($parts); // Remove class name

        return implode('\\', $parts);
    }

    /**
     * Extract route prefix
     */
    protected function extractPrefix(Route $route): ?string
    {
        $prefix = $route->getPrefix();

        return $prefix ? ltrim($prefix, '/') : null;
    }

    /**
     * Generate a unique key for the route
     */
    protected function generateRouteKey(Route $route): string
    {
        $methods = implode('|', $route->methods());
        $uri = $route->uri();

        return $methods . ':' . $uri;
    }

    /**
     * Check if route should be skipped
     */
    protected function shouldSkipRoute(Route $route): bool
    {
        // Skip vendor routes if configured
        if ($this->config('exclude_vendor_routes')) {
            $action = $route->getAction();
            if (isset($action['controller'])) {
                $controller = is_array($action['controller'])
                    ? $action['controller'][0]
                    : explode('@', (string) $action['controller'])[0];

                if (str_contains((string) $controller, 'vendor\\')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Group routes by prefix
     *
     * @param  Collection<string, array<string, mixed>>  $routes
     *
     * @return Collection<string, Collection<string, array<string, mixed>>>
     */
    protected function groupByPrefix(Collection $routes): Collection
    {
        return $routes->groupBy(function ($route) {
            if (isset($route['prefix'])) {
                return $route['prefix'] ?? 'no-prefix';
            }

            return 'no-prefix';
        });
    }

    /**
     * Group routes by middleware
     *
     * @param  Collection<string, array<string, mixed>>  $routes
     *
     * @return Collection<string, Collection<string, array<string, mixed>>>
     */
    protected function groupByMiddleware(Collection $routes): Collection
    {
        $grouped = collect();

        foreach ($routes as $key => $route) {
            $middleware = (is_array($route) && isset($route['middleware'])) ? $route['middleware'] : [];
            $middlewareKey = empty($middleware) ? 'no-middleware' : implode(',', $middleware);

            if (! $grouped->has($middlewareKey)) {
                $grouped->put($middlewareKey, collect());
            }

            $grouped->get($middlewareKey)->put($key, $route);
        }

        return $grouped;
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

        /** @var array<string, int> $methodCounts */
        $methodCounts = [];
        /** @var array<string, int> $controllerCounts */
        $controllerCounts = [];

        foreach ($this->results as $route) {
            if (is_array($route) && isset($route['methods'])) {
                foreach ($route['methods'] as $method) {
                    $methodCounts[$method] = ($methodCounts[$method] ?? 0) + 1;
                }
            }

            if (is_array($route) && isset($route['controller']) && is_array($route['controller'])) {
                $controller = $route['controller'];
                if (isset($controller['short_name'])) {
                    $controllerName = $controller['short_name'];
                    $controllerCounts[$controllerName] = ($controllerCounts[$controllerName] ?? 0) + 1;
                }
            }
        }

        $summary['methods_count'] = $methodCounts;
        $summary['controllers_count'] = $controllerCounts;
        $summary['unique_controllers'] = count($controllerCounts);

        return $summary;
    }
}
