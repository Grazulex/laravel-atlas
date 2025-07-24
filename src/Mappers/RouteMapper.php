<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use LaravelAtlas\Contracts\ComponentMapper;
use ReflectionClass;
use Throwable;

class RouteMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'routes';
    }

    public function scan(array $options = []): array
    {
        $allRoutes = RouteFacade::getRoutes();
        $routes = [];

        foreach ($allRoutes as $route) {
            $routes[] = $this->analyzeRoute($route);
        }

        usort($routes, fn ($a, $b): int => strcmp((string) $a['uri'], (string) $b['uri']));

        return [
            'type' => $this->type(),
            'count' => count($routes),
            'data' => $routes,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeRoute(Route $route): array
    {
        $action = $route->getActionName();
        $usesController = is_string($action) && str_contains($action, '@');

        $flow = $usesController
            ? $this->analyzeControllerFlow($route)
            : ['jobs' => [], 'events' => [], 'dependencies' => []];

        return [
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'methods' => array_values(array_diff($route->methods(), ['HEAD'])),
            'middleware' => $route->gatherMiddleware(),
            'action' => $action,
            'controller' => $usesController ? explode('@', $action)[0] : null,
            'uses' => $usesController ? explode('@', $action)[1] ?? null : null,
            'prefix' => $route->getPrefix(),
            'domain' => $route->getDomain(),
            'is_closure' => $action === 'Closure',
            'type' => $this->guessRouteType($route),
            'file' => $this->guessRouteFile($route),
            'flow' => $flow,
        ];
    }

    protected function guessRouteType(Route $route): string
    {
        $uri = $route->uri();

        return match (true) {
            str_starts_with($uri, 'api/') => 'api',
            str_starts_with($uri, 'admin/') => 'admin',
            str_starts_with($uri, 'webhooks') => 'webhook',
            str_starts_with($uri, 'health') || str_starts_with($uri, 'status') => 'system',
            default => 'web',
        };
    }

    protected function guessRouteFile(Route $route): string
    {
        $uri = $route->uri();
        $middleware = $route->gatherMiddleware();

        // Check if route has API middleware or starts with api/
        if (in_array('api', $middleware) || str_starts_with($uri, 'api/')) {
            return base_path('routes/api.php');
        }

        // Check for web middleware or common web patterns
        if (in_array('web', $middleware) || str_starts_with($uri, 'admin/')) {
            return str_starts_with($uri, 'admin/')
                ? base_path('routes/admin.php')  // If admin routes file exists
                : base_path('routes/web.php');
        }

        // Check for specific route files based on URI patterns
        $routePatterns = [
            'auth/' => 'routes/auth.php',
            'console' => 'routes/console.php',
            'channels' => 'routes/channels.php',
        ];

        foreach ($routePatterns as $pattern => $file) {
            if (str_starts_with($uri, $pattern)) {
                $fullPath = base_path($file);

                return file_exists($fullPath) ? $fullPath : base_path('routes/web.php');
            }
        }

        // Default to web.php
        return base_path('routes/web.php');
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeControllerFlow(Route $route): array
    {
        $controllerClass = $route->getAction('controller');

        if (! $controllerClass || ! method_exists($controllerClass, $route->getActionMethod())) {
            return ['jobs' => [], 'events' => [], 'dependencies' => []];
        }

        $method = $route->getActionMethod();
        $reflection = new ReflectionClass($controllerClass);

        try {
            $file = $reflection->getFileName();
            if ($file === false) {
                return ['jobs' => [], 'events' => [], 'dependencies' => []];
            }
            $contents = file_get_contents($file);
        } catch (Throwable) {
            return ['jobs' => [], 'events' => [], 'dependencies' => []];
        }

        if (! $file || ! $contents) {
            return ['jobs' => [], 'events' => [], 'dependencies' => []];
        }

        // Limiter l’analyse à la méthode ciblée
        $methodRegex = "/function\s+{$method}\s*\([^\)]*\)\s*\{(.*?)^\}/sm";
        preg_match($methodRegex, $contents, $match);
        $source = $match[1] ?? $contents;

        return [
            'jobs' => $this->extractDispatches($source),
            'events' => $this->extractEvents($source),
            'dependencies' => $this->extractDependencies($source),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function extractDispatches(string $source): array
    {
        preg_match_all('/dispatch(?:Now)?\(\s*([A-Z][\w\\\\]+)::class/', $source, $matches);

        return array_map(fn ($fqcn): array => [
            'class' => $fqcn,
            'async' => ! str_contains($source, "dispatchNow({$fqcn}"),
        ], $matches[1]);
    }

    /**
     * @return array<int, array<string, string>>
     */
    protected function extractEvents(string $source): array
    {
        preg_match_all('/event\(\s*([A-Z][\w\\\\]+)::class/', $source, $matches);

        return array_map(fn ($fqcn): array => ['class' => $fqcn], $matches[1]);
    }

    /**
     * @return array<int, string>
     */
    protected function extractDependencies(string $source): array
    {
        preg_match_all('/new\s+([A-Z][\w\\\\]+)|([A-Z][\w\\\\]+)::/', $source, $matches);
        $found = array_filter(array_merge($matches[1], $matches[2]));

        return array_values(array_unique(array_filter($found)));
    }
}
