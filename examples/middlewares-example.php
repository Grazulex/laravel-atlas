<?php

/**
 * Laravel Atlas - Middlewares Analysis Example
 * 
 * This example demonstrates how to analyze HTTP middleware classes:
 * - Middleware parameters and dependencies
 * - Handle and terminate methods
 * - Flow patterns and facade usage
 */

use LaravelAtlas\Facades\Atlas;

echo "=== Laravel Atlas - Middlewares Analysis Example ===\n\n";

// 1. Basic middleware scanning
echo "1. Basic middleware scanning:\n";
$middlewares = Atlas::scan('middlewares');

echo "Total middlewares found: " . ($middlewares['count'] ?? 0) . "\n";
if (isset($middlewares['data']) && is_array($middlewares['data'])) {
    echo "Middleware classes:\n";
    foreach ($middlewares['data'] as $middleware) {
        if (isset($middleware['class'])) {
            echo "- " . class_basename($middleware['class']) . " ({$middleware['class']})\n";
        }
    }
}
echo "\n";

// 2. Middlewares with parameters and dependencies
echo "2. Middlewares with detailed information:\n";
$middlewaresWithDetails = Atlas::scan('middlewares', [
    'include_parameters' => true,
    'include_dependencies' => true,
]);

if (isset($middlewaresWithDetails['data']) && is_array($middlewaresWithDetails['data'])) {
    foreach ($middlewaresWithDetails['data'] as $middleware) {
        if (isset($middleware['class'])) {
            $className = class_basename($middleware['class']);
            echo "Middleware: {$className}\n";
            
            // Show handle method parameters (excluding request and next)
            if (isset($middleware['parameters']) && is_array($middleware['parameters'])) {
                if (!empty($middleware['parameters'])) {
                    echo "  Handle parameters:\n";
                    foreach ($middleware['parameters'] as $param) {
                        if (isset($param['name'], $param['type'])) {
                            $default = isset($param['default']) ? " = " . json_encode($param['default']) : '';
                            echo "    - {$param['name']} ({$param['type']}){$default}\n";
                        }
                    }
                } else {
                    echo "  Handle parameters: none (standard middleware)\n";
                }
            }
            
            // Show constructor dependencies
            if (isset($middleware['dependencies']) && is_array($middleware['dependencies'])) {
                $filteredDeps = array_filter($middleware['dependencies']);
                if (!empty($filteredDeps)) {
                    echo "  Dependencies: " . count($filteredDeps) . "\n";
                    foreach ($filteredDeps as $dependency) {
                        echo "    - " . class_basename($dependency) . "\n";
                    }
                }
            }
            
            // Show if it has terminate method
            if (isset($middleware['has_terminate']) && $middleware['has_terminate']) {
                echo "  Has terminate method: yes\n";
            }
            
            // Show flow patterns
            if (isset($middleware['flow']) && is_array($middleware['flow'])) {
                $flowTypes = array_keys(array_filter($middleware['flow'], fn($items) => !empty($items)));
                if (!empty($flowTypes)) {
                    echo "  Flow patterns: " . implode(', ', $flowTypes) . "\n";
                }
            }
            
            echo "\n";
        }
    }
}

// 3. Middleware pattern analysis
echo "3. Middleware patterns analysis:\n";
if (isset($middlewaresWithDetails['data']) && is_array($middlewaresWithDetails['data'])) {
    $middlewaresWithParams = 0;
    $middlewaresWithTerminate = 0;
    $middlewaresWithDependencies = 0;
    
    foreach ($middlewaresWithDetails['data'] as $middleware) {
        if (isset($middleware['parameters']) && !empty($middleware['parameters'])) {
            $middlewaresWithParams++;
        }
        
        if (isset($middleware['has_terminate']) && $middleware['has_terminate']) {
            $middlewaresWithTerminate++;
        }
        
        if (isset($middleware['dependencies']) && !empty(array_filter($middleware['dependencies']))) {
            $middlewaresWithDependencies++;
        }
    }
    
    echo "- Middlewares with parameters: {$middlewaresWithParams}\n";
    echo "- Middlewares with terminate method: {$middlewaresWithTerminate}\n";
    echo "- Middlewares with dependencies: {$middlewaresWithDependencies}\n";
}
echo "\n";

// 4. Middleware flow analysis
echo "4. Middleware flow patterns:\n";
if (isset($middlewaresWithDetails['data']) && is_array($middlewaresWithDetails['data'])) {
    $facadeUsage = [];
    
    foreach ($middlewaresWithDetails['data'] as $middleware) {
        if (isset($middleware['flow']) && is_array($middleware['flow'])) {
            foreach ($middleware['flow'] as $type => $items) {
                if (!empty($items)) {
                    if ($type === 'facades') {
                        foreach ($items as $facade) {
                            $facadeUsage[$facade] = ($facadeUsage[$facade] ?? 0) + 1;
                        }
                    }
                }
            }
        }
    }
    
    if (!empty($facadeUsage)) {
        echo "Facade usage in middlewares:\n";
        foreach ($facadeUsage as $facade => $count) {
            echo "- {$facade}: used by {$count} middleware(s)\n";
        }
    } else {
        echo "No facade usage detected in middlewares\n";
    }
}
echo "\n";

// 5. Export middlewares to different formats
echo "5. Exporting middlewares:\n";

// JSON export
$jsonExport = Atlas::export('middlewares', 'json');
echo "- JSON export ready (length: " . strlen($jsonExport) . " characters)\n";

// Markdown export
$markdownExport = Atlas::export('middlewares', 'markdown');
echo "- Markdown export ready (length: " . strlen($markdownExport) . " characters)\n";

// HTML export
$htmlExport = Atlas::export('middlewares', 'html');
echo "- HTML export ready (length: " . strlen($htmlExport) . " characters)\n";

// 6. Custom middleware analysis
echo "\n6. Custom middleware analysis:\n";
$customMiddlewares = Atlas::scan('middlewares', [
    'paths' => [app_path('Http/Middleware')],
]);

echo "Middlewares found in custom path: " . ($customMiddlewares['count'] ?? 0) . "\n";

if (isset($customMiddlewares['data']) && is_array($customMiddlewares['data'])) {
    foreach ($customMiddlewares['data'] as $middleware) {
        if (isset($middleware['class'])) {
            $params = isset($middleware['parameters']) && !empty($middleware['parameters']) 
                ? " (with parameters)" 
                : "";
            echo "- " . class_basename($middleware['class']) . "{$params}\n";
        }
    }
}

echo "\nMiddlewares analysis example completed successfully!\n";