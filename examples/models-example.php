<?php

/**
 * Laravel Atlas - Models Analysis Example
 * 
 * This example demonstrates how to analyze Eloquent models:
 * - Model relationships and metadata
 * - Observers and factories
 * - Dependencies and connections
 */

use LaravelAtlas\Facades\Atlas;

echo "=== Laravel Atlas - Models Analysis Example ===\n\n";

// 1. Basic model scanning
echo "1. Basic model scanning:\n";
$models = Atlas::scan('models');

echo "Total models found: " . ($models['count'] ?? 0) . "\n";
if (isset($models['data']) && is_array($models['data'])) {
    echo "Model classes:\n";
    foreach ($models['data'] as $model) {
        if (isset($model['class'])) {
            echo "- " . class_basename($model['class']) . " ({$model['class']})\n";
        }
    }
}
echo "\n";

// 2. Models with relationships
echo "2. Models with relationships:\n";
$modelsWithRelationships = Atlas::scan('models', [
    'include_relationships' => true,
    'include_observers' => true,
    'include_factories' => true,
]);

if (isset($modelsWithRelationships['data']) && is_array($modelsWithRelationships['data'])) {
    foreach ($modelsWithRelationships['data'] as $model) {
        if (isset($model['class'])) {
            $className = class_basename($model['class']);
            echo "Model: {$className}\n";
            
            // Show relationships
            if (isset($model['relationships']) && is_array($model['relationships'])) {
                echo "  Relationships: " . count($model['relationships']) . "\n";
                foreach ($model['relationships'] as $relationship) {
                    if (isset($relationship['type'], $relationship['related'])) {
                        echo "    - {$relationship['type']}: " . class_basename($relationship['related']) . "\n";
                    }
                }
            }
            
            // Show attributes
            if (isset($model['attributes']) && is_array($model['attributes'])) {
                echo "  Attributes: " . count($model['attributes']) . "\n";
                foreach (array_slice($model['attributes'], 0, 3) as $attribute) { // Show first 3
                    echo "    - {$attribute}\n";
                }
                if (count($model['attributes']) > 3) {
                    echo "    ... and " . (count($model['attributes']) - 3) . " more\n";
                }
            }
            
            echo "\n";
        }
    }
}

// 3. Export models to different formats
echo "3. Exporting models:\n";

// JSON export
$jsonExport = Atlas::export('models', 'json');
echo "- JSON export ready (length: " . strlen($jsonExport) . " characters)\n";

// Markdown export
$markdownExport = Atlas::export('models', 'markdown');
echo "- Markdown export ready (length: " . strlen($markdownExport) . " characters)\n";

// HTML export
$htmlExport = Atlas::export('models', 'html');
echo "- HTML export ready (length: " . strlen($htmlExport) . " characters)\n";

// 4. Model analysis with custom paths
echo "\n4. Custom model analysis:\n";
$customModels = Atlas::scan('models', [
    'paths' => [app_path('Models')],
    'recursive' => true,
    'include_relationships' => true,
]);

echo "Models found in custom path: " . ($customModels['count'] ?? 0) . "\n";

echo "\nModels analysis example completed successfully!\n";