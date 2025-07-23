<?php

/**
 * Laravel Atlas - Model Analysis Example
 * 
 * This example demonstrates detailed model analysis including relationships,
 * scopes, casts, and other model features.
 */

use LaravelAtlas\Facades\Atlas;

echo "🧱 Laravel Atlas - Model Analysis Example\n";
echo "=========================================\n\n";

try {
    // Scan models with detailed information
    echo "🔍 Performing detailed model analysis...\n";
    $models = Atlas::scan('models', [
        'include_relationships' => true,
        'include_observers' => true,
        'include_scopes' => true,
        'include_casts' => true,
        'include_fillable' => true,
        'include_hidden' => true
    ]);

    $modelCount = count($models['data'] ?? []);
    echo "✅ Analyzed {$modelCount} models\n\n";

    if ($modelCount === 0) {
        echo "⚠️ No models found. Make sure your models are in the app/Models directory.\n";
        exit(0);
    }

    // Detailed analysis
    echo "📊 Detailed Analysis Results:\n";
    echo "============================\n\n";

    $relationshipCounts = [];
    $castTypes = [];
    $scopeCounts = 0;

    foreach ($models['data'] as $model) {
        echo "🏷️ Model: {$model['class']}\n";
        echo "  📋 Table: {$model['table']}\n";
        echo "  🔑 Primary Key: {$model['primary_key']}\n";
        
        // Fillable fields
        if (!empty($model['fillable'])) {
            echo "  📝 Fillable Fields: " . implode(', ', $model['fillable']) . "\n";
        }
        
        // Hidden fields
        if (!empty($model['hidden'])) {
            echo "  🙈 Hidden Fields: " . implode(', ', $model['hidden']) . "\n";
        }
        
        // Casts
        if (!empty($model['casts'])) {
            echo "  🔄 Casts: " . count($model['casts']) . " fields\n";
            foreach ($model['casts'] as $field => $type) {
                echo "    - {$field}: {$type}\n";
                $castTypes[$type] = ($castTypes[$type] ?? 0) + 1;
            }
        }
        
        // Relationships
        if (!empty($model['relationships'])) {
            echo "  🔗 Relationships: " . count($model['relationships']) . "\n";
            foreach ($model['relationships'] as $name => $rel) {
                echo "    - {$name}: {$rel['type']} -> {$rel['related']}\n";
                $relationshipCounts[$rel['type']] = ($relationshipCounts[$rel['type']] ?? 0) + 1;
            }
        }
        
        // Scopes
        if (!empty($model['scopes'])) {
            $scopeNames = array_column($model['scopes'], 'name');
            echo "  🎯 Scopes: " . implode(', ', $scopeNames) . "\n";
            $scopeCounts += count($scopeNames);
        }

        // Observers
        if (!empty($model['observers'])) {
            echo "  👁 Observers: " . implode(', ', $model['observers']) . "\n";
        }

        echo "\n";
    }

    // Summary statistics
    echo "📈 Summary Statistics:\n";
    echo "=====================\n";
    echo "📊 Total Models: {$modelCount}\n";
    echo "🎯 Total Scopes: {$scopeCounts}\n";

    if (!empty($relationshipCounts)) {
        echo "🔗 Relationship Types:\n";
        foreach ($relationshipCounts as $type => $count) {
            echo "  - {$type}: {$count}\n";
        }
    }

    if (!empty($castTypes)) {
        echo "🔄 Cast Types Usage:\n";
        foreach ($castTypes as $type => $count) {
            echo "  - {$type}: {$count} fields\n";
        }
    }

    // Export detailed analysis
    echo "\n💾 Exporting Detailed Analysis...\n";
    
    // Create docs directory if it doesn't exist
    if (!is_dir('docs')) {
        mkdir('docs', 0755, true);
    }

    // Export to markdown for documentation
    $markdown = Atlas::export('models', 'markdown');
    file_put_contents('docs/model-analysis.md', $markdown);
    echo "✅ Markdown documentation: docs/model-analysis.md\n";

    // Export to HTML for interactive viewing
    $html = Atlas::export('models', 'html');
    file_put_contents('docs/model-analysis.html', $html);
    echo "✅ Interactive HTML report: docs/model-analysis.html\n";

    // Export raw data to JSON for further processing
    $json = Atlas::export('models', 'json');
    file_put_contents('storage/atlas/detailed-models.json', $json);
    echo "✅ Raw JSON data: storage/atlas/detailed-models.json\n";

    echo "\n🎉 Model analysis completed successfully!\n";
    echo "📖 Open docs/model-analysis.html in your browser for interactive exploration.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "💡 Make sure you're running this from a Laravel application root directory.\n";
    exit(1);
}