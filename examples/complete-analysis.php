<?php

/**
 * Laravel Atlas - Complete Analysis Example
 * 
 * This example demonstrates comprehensive application analysis:
 * - Scanning all available component types
 * - Generating complete architecture documentation
 * - Exporting to multiple formats
 */

use LaravelAtlas\Facades\Atlas;

echo "=== Laravel Atlas - Complete Analysis Example ===\n\n";

// 1. Comprehensive application scan
echo "1. Comprehensive application scan:\n";
$allComponents = Atlas::scan('all');

echo "Application architecture overview:\n";
$totalComponents = 0;
foreach ($allComponents as $type => $data) {
    if (is_array($data) && isset($data['count'])) {
        $count = $data['count'];
        $totalComponents += $count;
        echo "- " . ucfirst($type) . ": {$count} components\n";
    }
}
echo "Total components analyzed: {$totalComponents}\n\n";

// 2. Detailed analysis by component type
echo "2. Detailed analysis by component type:\n";

$componentTypes = [
    'models' => ['include_relationships' => true, 'include_observers' => true],
    'routes' => ['include_middleware' => true, 'include_controllers' => true],
    'commands' => ['include_signatures' => true, 'include_descriptions' => true],
    'services' => ['include_dependencies' => true, 'include_methods' => true],
    'notifications' => ['include_channels' => true, 'include_flow' => true],
    'middlewares' => ['include_parameters' => true, 'include_dependencies' => true],
    'form_requests' => ['include_rules' => true, 'include_authorization' => true],
];

$detailedAnalysis = [];
foreach ($componentTypes as $type => $options) {
    echo "Analyzing {$type}...\n";
    $data = Atlas::scan($type, $options);
    $detailedAnalysis[$type] = $data;
    
    // Show summary for each type
    if (isset($data['data']) && is_array($data['data'])) {
        $count = count($data['data']);
        echo "  Found {$count} {$type}\n";
        
        // Type-specific analysis
        switch ($type) {
            case 'models':
                $withRelationships = 0;
                foreach ($data['data'] as $model) {
                    if (isset($model['relationships']) && !empty($model['relationships'])) {
                        $withRelationships++;
                    }
                }
                echo "  - {$withRelationships} with relationships\n";
                break;
                
            case 'routes':
                $withMiddleware = 0;
                foreach ($data['data'] as $route) {
                    if (isset($route['middleware']) && !empty($route['middleware'])) {
                        $withMiddleware++;
                    }
                }
                echo "  - {$withMiddleware} with middleware\n";
                break;
                
            case 'services':
                $withDependencies = 0;
                foreach ($data['data'] as $service) {
                    if (isset($service['dependencies']) && !empty(array_filter($service['dependencies']))) {
                        $withDependencies++;
                    }
                }
                echo "  - {$withDependencies} with dependencies\n";
                break;
                
            case 'notifications':
                $withChannels = 0;
                foreach ($data['data'] as $notification) {
                    if (isset($notification['channels']) && !empty($notification['channels'])) {
                        $withChannels++;
                    }
                }
                echo "  - {$withChannels} with defined channels\n";
                break;
                
            case 'middlewares':
                $withParams = 0;
                foreach ($data['data'] as $middleware) {
                    if (isset($middleware['parameters']) && !empty($middleware['parameters'])) {
                        $withParams++;
                    }
                }
                echo "  - {$withParams} with parameters\n";
                break;
                
            case 'form_requests':
                $withRules = 0;
                foreach ($data['data'] as $formRequest) {
                    if (isset($formRequest['rules']) && !empty($formRequest['rules'])) {
                        $withRules++;
                    }
                }
                echo "  - {$withRules} with validation rules\n";
                break;
        }
    }
    echo "\n";
}

// 3. Generate comprehensive documentation
echo "3. Generating comprehensive documentation:\n";

// JSON documentation
echo "Generating JSON documentation...\n";
$jsonDoc = Atlas::export('all', 'json');
echo "- JSON documentation: " . strlen($jsonDoc) . " characters\n";

// Markdown documentation
echo "Generating Markdown documentation...\n";
$markdownDoc = Atlas::export('all', 'markdown');
echo "- Markdown documentation: " . strlen($markdownDoc) . " characters\n";

// HTML documentation
echo "Generating HTML documentation...\n";
$htmlDoc = Atlas::export('all', 'html');
echo "- HTML documentation: " . strlen($htmlDoc) . " characters\n";

// PHP data export
echo "Generating PHP data export...\n";
$phpDoc = Atlas::export('all', 'php');
echo "- PHP data export: " . strlen($phpDoc) . " characters\n";

echo "\n";

// 4. Architecture insights
echo "4. Architecture insights:\n";

$insights = [
    'total_classes' => 0,
    'total_methods' => 0,
    'dependency_patterns' => [],
    'validation_complexity' => 0,
    'route_patterns' => [],
];

foreach ($detailedAnalysis as $type => $data) {
    if (isset($data['data']) && is_array($data['data'])) {
        $insights['total_classes'] += count($data['data']);
        
        foreach ($data['data'] as $component) {
            // Count methods
            if (isset($component['methods']) && is_array($component['methods'])) {
                $insights['total_methods'] += count($component['methods']);
            }
            
            // Analyze dependencies
            if (isset($component['dependencies']) && is_array($component['dependencies'])) {
                foreach (array_filter($component['dependencies']) as $dep) {
                    $baseClass = class_basename($dep);
                    $insights['dependency_patterns'][$baseClass] = ($insights['dependency_patterns'][$baseClass] ?? 0) + 1;
                }
            }
            
            // Count validation rules
            if (isset($component['rules']) && is_array($component['rules'])) {
                $insights['validation_complexity'] += count($component['rules']);
            }
            
            // Analyze route patterns
            if (isset($component['uri'])) {
                $segments = explode('/', trim($component['uri'], '/'));
                $pattern = count($segments) > 0 ? $segments[0] : 'root';
                $insights['route_patterns'][$pattern] = ($insights['route_patterns'][$pattern] ?? 0) + 1;
            }
        }
    }
}

echo "Architecture Overview:\n";
echo "- Total classes analyzed: {$insights['total_classes']}\n";
echo "- Total methods found: {$insights['total_methods']}\n";
echo "- Validation rules defined: {$insights['validation_complexity']}\n";

if (!empty($insights['dependency_patterns'])) {
    echo "Most common dependencies:\n";
    arsort($insights['dependency_patterns']);
    foreach (array_slice($insights['dependency_patterns'], 0, 5, true) as $dep => $count) {
        echo "  - {$dep}: used {$count} times\n";
    }
}

if (!empty($insights['route_patterns'])) {
    echo "Route patterns:\n";
    arsort($insights['route_patterns']);
    foreach (array_slice($insights['route_patterns'], 0, 5, true) as $pattern => $count) {
        echo "  - /{$pattern}: {$count} routes\n";
    }
}

echo "\n";

// 5. Save documentation to files
echo "5. Saving documentation to files:\n";

$timestamp = date('Y-m-d_H-i-s');
$outputDir = "atlas_export_{$timestamp}";

if (!file_exists($outputDir)) {
    mkdir($outputDir, 0755, true);
    echo "Created output directory: {$outputDir}/\n";
}

// Save each format
file_put_contents("{$outputDir}/architecture.json", $jsonDoc);
echo "- Saved: {$outputDir}/architecture.json\n";

file_put_contents("{$outputDir}/architecture.md", $markdownDoc);
echo "- Saved: {$outputDir}/architecture.md\n";

file_put_contents("{$outputDir}/architecture.html", $htmlDoc);
echo "- Saved: {$outputDir}/architecture.html\n";

file_put_contents("{$outputDir}/architecture.php", $phpDoc);
echo "- Saved: {$outputDir}/architecture.php\n";

// Create a summary file
$summary = "# Laravel Atlas - Complete Analysis Summary\n\n";
$summary .= "Generated on: " . date('Y-m-d H:i:s') . "\n\n";
$summary .= "## Component Summary\n\n";
foreach ($allComponents as $type => $data) {
    if (is_array($data) && isset($data['count'])) {
        $summary .= "- **" . ucfirst($type) . "**: {$data['count']} components\n";
    }
}
$summary .= "\n## Architecture Insights\n\n";
$summary .= "- Total classes: {$insights['total_classes']}\n";
$summary .= "- Total methods: {$insights['total_methods']}\n";
$summary .= "- Validation rules: {$insights['validation_complexity']}\n";

file_put_contents("{$outputDir}/summary.md", $summary);
echo "- Saved: {$outputDir}/summary.md\n";

echo "\nComplete analysis example finished successfully!\n";
echo "All documentation files have been saved to: {$outputDir}/\n";