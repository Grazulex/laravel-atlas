<?php

/**
 * Laravel Atlas - Routes Analysis Example
 * 
 * This example demonstrates how to analyze application routes:
 * - Route mapping with middleware information
 * - Controller connections
 * - HTTP methods and URIs
 */

use LaravelAtlas\Facades\Atlas;

echo "=== Laravel Atlas - Routes Analysis Example ===\n\n";

// 1. Basic route scanning
echo "1. Basic route scanning:\n";
$routes = Atlas::scan('routes');

echo "Total routes found: " . ($routes['count'] ?? 0) . "\n";
if (isset($routes['data']) && is_array($routes['data'])) {
    echo "Route methods distribution:\n";
    $methods = [];
    foreach ($routes['data'] as $route) {
        if (isset($route['method'])) {
            $method = is_array($route['method']) ? implode('|', $route['method']) : $route['method'];
            $methods[$method] = ($methods[$method] ?? 0) + 1;
        }
    }
    foreach ($methods as $method => $count) {
        echo "- {$method}: {$count} routes\n";
    }
}
echo "\n";

// 2. Routes with middleware information
echo "2. Routes with middleware:\n";
$routesWithMiddleware = Atlas::scan('routes', [
    'include_middleware' => true,
    'include_controllers' => true,
    'group_by_prefix' => true,
]);

if (isset($routesWithMiddleware['data']) && is_array($routesWithMiddleware['data'])) {
    foreach (array_slice($routesWithMiddleware['data'], 0, 5) as $route) { // Show first 5 routes
        if (isset($route['uri'])) {
            $method = is_array($route['method'] ?? []) ? implode('|', $route['method']) : ($route['method'] ?? 'GET');
            echo "Route: {$method} {$route['uri']}\n";
            
            // Show middleware
            if (isset($route['middleware']) && is_array($route['middleware'])) {
                echo "  Middleware: " . implode(', ', $route['middleware']) . "\n";
            }
            
            // Show controller
            if (isset($route['controller'])) {
                echo "  Controller: {$route['controller']}\n";
            }
            
            // Show action
            if (isset($route['action'])) {
                echo "  Action: {$route['action']}\n";
            }
            
            echo "\n";
        }
    }
}

// 3. Route groupings
echo "3. Route analysis by patterns:\n";
if (isset($routesWithMiddleware['data']) && is_array($routesWithMiddleware['data'])) {
    $apiRoutes = 0;
    $webRoutes = 0;
    $adminRoutes = 0;
    
    foreach ($routesWithMiddleware['data'] as $route) {
        if (isset($route['uri'])) {
            if (str_starts_with($route['uri'], 'api/')) {
                $apiRoutes++;
            } elseif (str_starts_with($route['uri'], 'admin/')) {
                $adminRoutes++;
            } else {
                $webRoutes++;
            }
        }
    }
    
    echo "- API routes: {$apiRoutes}\n";
    echo "- Admin routes: {$adminRoutes}\n";
    echo "- Web routes: {$webRoutes}\n";
}
echo "\n";

// 4. Export routes to different formats
echo "4. Exporting routes:\n";

// JSON export
$jsonExport = Atlas::export('routes', 'json');
echo "- JSON export ready (length: " . strlen($jsonExport) . " characters)\n";

// Markdown export
$markdownExport = Atlas::export('routes', 'markdown');
echo "- Markdown export ready (length: " . strlen($markdownExport) . " characters)\n";

// HTML export
$htmlExport = Atlas::export('routes', 'html');
echo "- HTML export ready (length: " . strlen($htmlExport) . " characters)\n";

// 5. Route filtering and analysis
echo "\n5. Custom route analysis:\n";
$filteredRoutes = Atlas::scan('routes', [
    'include_middleware' => true,
    'include_controllers' => true,
]);

if (isset($filteredRoutes['data']) && is_array($filteredRoutes['data'])) {
    $routesWithAuth = 0;
    $routesWithThrottle = 0;
    
    foreach ($filteredRoutes['data'] as $route) {
        if (isset($route['middleware']) && is_array($route['middleware'])) {
            if (in_array('auth', $route['middleware']) || in_array('auth:api', $route['middleware'])) {
                $routesWithAuth++;
            }
            if (array_filter($route['middleware'], fn($m) => str_contains($m, 'throttle'))) {
                $routesWithThrottle++;
            }
        }
    }
    
    echo "Routes with authentication: {$routesWithAuth}\n";
    echo "Routes with throttling: {$routesWithThrottle}\n";
}

echo "\nRoutes analysis example completed successfully!\n";