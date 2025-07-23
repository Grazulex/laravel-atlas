<?php

/**
 * Laravel Atlas - Simple Scan Example
 * 
 * This example demonstrates basic scanning capabilities of Laravel Atlas.
 * Run this script from your Laravel application root directory.
 */

use LaravelAtlas\Facades\Atlas;

echo "🔍 Laravel Atlas - Simple Scan Example\n";
echo "=====================================\n\n";

try {
    // Scan all models
    echo "📊 Scanning Models...\n";
    $models = Atlas::scan('models');
    $modelCount = count($models['data'] ?? []);
    echo "✅ Found {$modelCount} models\n\n";

    // Display basic model information
    if ($modelCount > 0) {
        echo "📋 Model Summary:\n";
        foreach (array_slice($models['data'], 0, 3) as $model) {
            echo "  - {$model['class']} (table: {$model['table']})\n";
        }
        if ($modelCount > 3) {
            echo "  ... and " . ($modelCount - 3) . " more models\n";
        }
        echo "\n";
    }

    // Scan all routes
    echo "🛣️ Scanning Routes...\n";
    $routes = Atlas::scan('routes');
    $routeCount = count($routes['data'] ?? []);
    echo "✅ Found {$routeCount} routes\n\n";

    // Display basic route information
    if ($routeCount > 0) {
        echo "🚦 Route Summary:\n";
        $methodCounts = [];
        foreach ($routes['data'] as $route) {
            foreach ($route['methods'] ?? [] as $method) {
                $methodCounts[$method] = ($methodCounts[$method] ?? 0) + 1;
            }
        }
        
        foreach ($methodCounts as $method => $count) {
            echo "  - {$method}: {$count} routes\n";
        }
        echo "\n";
    }

    // Export to JSON
    echo "💾 Exporting Results...\n";
    
    // Create output directory if it doesn't exist
    if (!is_dir('storage/atlas')) {
        mkdir('storage/atlas', 0755, true);
    }

    // Export models to JSON
    $modelsJson = Atlas::export('models', 'json');
    file_put_contents('storage/atlas/models.json', $modelsJson);
    echo "✅ Models exported to storage/atlas/models.json\n";

    // Export routes to JSON
    $routesJson = Atlas::export('routes', 'json');
    file_put_contents('storage/atlas/routes.json', $routesJson);
    echo "✅ Routes exported to storage/atlas/routes.json\n";

    echo "\n🎉 Simple scan completed successfully!\n";
    echo "📁 Check the storage/atlas/ directory for output files.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "💡 Make sure you're running this from a Laravel application root directory.\n";
    exit(1);
}