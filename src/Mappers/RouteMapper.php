<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use LaravelAtlas\Contracts\ComponentMapper;

class RouteMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'routes';
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function scan(array $options = []): array
    {
        $allRoutes = RouteFacade::getRoutes();
        $routes = [];

        foreach ($allRoutes as $route) {
            $routes[] = $this->analyzeRoute($route);
        }

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

        return [
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'methods' => $route->methods(),
            'middleware' => $route->gatherMiddleware(),
            'action' => $action,
            'controller' => $usesController ? explode('@', $action)[0] : null,
            'uses' => $usesController ? explode('@', $action)[1] ?? null : null,
        ];
    }
}
