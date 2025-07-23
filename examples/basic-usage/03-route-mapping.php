<?php

/**
 * Laravel Atlas - Route Mapping Example
 * 
 * This example demonstrates comprehensive route analysis including
 * middleware, controllers, parameters, and route grouping.
 */

use LaravelAtlas\Facades\Atlas;

echo "🛣️ Laravel Atlas - Route Mapping Example\n";
echo "=========================================\n\n";

try {
    // Scan routes with comprehensive information
    echo "🔍 Analyzing route structure...\n";
    $routes = Atlas::scan('routes', [
        'include_middleware' => true,
        'include_controllers' => true,
        'include_parameters' => true,
        'include_names' => true,
        'group_by_prefix' => false
    ]);

    $routeCount = count($routes['data'] ?? []);
    echo "✅ Analyzed {$routeCount} routes\n\n";

    if ($routeCount === 0) {
        echo "⚠️ No routes found. Make sure your application has defined routes.\n";
        exit(0);
    }

    // Analyze route data
    echo "📊 Route Analysis Results:\n";
    echo "=========================\n\n";

    $methodCounts = [];
    $middlewareCounts = [];
    $controllerCounts = [];
    $prefixGroups = [];
    $protectedRoutes = 0;
    $apiRoutes = 0;
    $webRoutes = 0;

    foreach ($routes['data'] as $route) {
        // Count HTTP methods
        foreach ($route['methods'] ?? [] as $method) {
            $methodCounts[$method] = ($methodCounts[$method] ?? 0) + 1;
        }
        
        // Count middleware usage
        foreach ($route['middleware'] ?? [] as $middleware) {
            $middlewareCounts[$middleware] = ($middlewareCounts[$middleware] ?? 0) + 1;
            
            // Track protected routes
            if (in_array($middleware, ['auth', 'auth:api', 'auth:sanctum'])) {
                $protectedRoutes++;
            }
        }
        
        // Count controller usage
        if (!empty($route['controller'])) {
            $controllerCounts[$route['controller']] = ($controllerCounts[$route['controller']] ?? 0) + 1;
        }

        // Group by prefix
        $uri = $route['uri'] ?? '';
        if (strpos($uri, 'api/') === 0) {
            $apiRoutes++;
        } else {
            $webRoutes++;
        }

        // Extract prefix
        $uriParts = explode('/', trim($uri, '/'));
        if (!empty($uriParts[0])) {
            $prefix = $uriParts[0];
            $prefixGroups[$prefix] = ($prefixGroups[$prefix] ?? 0) + 1;
        }
    }

    // Display method distribution
    echo "📈 HTTP Method Distribution:\n";
    arsort($methodCounts);
    foreach ($methodCounts as $method => $count) {
        $percentage = round(($count / $routeCount) * 100, 1);
        echo "  - {$method}: {$count} routes ({$percentage}%)\n";
    }
    echo "\n";

    // Display most used middleware
    echo "🛡️ Most Used Middleware:\n";
    arsort($middlewareCounts);
    foreach (array_slice($middlewareCounts, 0, 10, true) as $middleware => $count) {
        echo "  - {$middleware}: {$count} routes\n";
    }
    echo "\n";

    // Display top controllers
    echo "🎮 Most Active Controllers:\n";
    arsort($controllerCounts);
    foreach (array_slice($controllerCounts, 0, 10, true) as $controller => $count) {
        echo "  - {$controller}: {$count} routes\n";
    }
    echo "\n";

    // Display route groupings
    echo "📂 Route Prefix Groups:\n";
    arsort($prefixGroups);
    foreach (array_slice($prefixGroups, 0, 10, true) as $prefix => $count) {
        echo "  - /{$prefix}: {$count} routes\n";
    }
    echo "\n";

    // Security analysis
    echo "🔒 Security Analysis:\n";
    echo "  - Protected routes: {$protectedRoutes} / {$routeCount} (" . round(($protectedRoutes / $routeCount) * 100, 1) . "%)\n";
    echo "  - API routes: {$apiRoutes}\n";
    echo "  - Web routes: {$webRoutes}\n";
    echo "\n";

    // Route complexity analysis
    $parameterizedRoutes = 0;
    $namedRoutes = 0;
    
    foreach ($routes['data'] as $route) {
        if (strpos($route['uri'] ?? '', '{') !== false) {
            $parameterizedRoutes++;
        }
        if (!empty($route['name'])) {
            $namedRoutes++;
        }
    }

    echo "⚙️ Route Complexity:\n";
    echo "  - Parameterized routes: {$parameterizedRoutes} / {$routeCount} (" . round(($parameterizedRoutes / $routeCount) * 100, 1) . "%)\n";
    echo "  - Named routes: {$namedRoutes} / {$routeCount} (" . round(($namedRoutes / $routeCount) * 100, 1) . "%)\n";
    echo "\n";

    // Export comprehensive route analysis
    echo "💾 Exporting Route Analysis...\n";
    
    // Create output directories
    if (!is_dir('public/atlas')) {
        mkdir('public/atlas', 0755, true);
    }
    if (!is_dir('storage/atlas')) {
        mkdir('storage/atlas', 0755, true);
    }

    // Export interactive HTML report
    $htmlReport = Atlas::export('routes', 'html');
    file_put_contents('public/atlas/route-map.html', $htmlReport);
    echo "✅ Interactive HTML report: public/atlas/route-map.html\n";

    // Export JSON data for API consumption
    $jsonData = Atlas::export('routes', 'json');
    file_put_contents('storage/atlas/route-map.json', $jsonData);
    echo "✅ JSON data: storage/atlas/route-map.json\n";

    // Export markdown documentation
    $markdownDocs = Atlas::export('routes', 'markdown');
    file_put_contents('docs/route-documentation.md', $markdownDocs);
    echo "✅ Markdown documentation: docs/route-documentation.md\n";

    echo "\n🎉 Route mapping completed successfully!\n";
    echo "🌐 Open public/atlas/route-map.html in your browser to explore routes interactively.\n";
    echo "📊 Check storage/atlas/route-map.json for raw data processing.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "💡 Make sure you're running this from a Laravel application root directory.\n";
    exit(1);
}