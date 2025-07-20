# Model Analysis Examples

Examples demonstrating how to analyze Eloquent models and their relationships using Laravel Atlas.

## 📋 Prerequisites

- Laravel application with Eloquent models
- Laravel Atlas installed
- Some models with relationships for meaningful analysis

## 🔍 Basic Model Analysis

### Scan All Models

```bash
# Basic model scanning
php artisan atlas:generate --type=models

# Save to file for analysis
php artisan atlas:generate --type=models --format=json --output=models.json
```

**Example Output Structure:**
```json
{
  "type": "models",
  "data": [
    {
      "name": "User",
      "namespace": "App\\Models",
      "path": "/app/Models/User.php",
      "extends": "Illuminate\\Foundation\\Auth\\User",
      "implements": ["Illuminate\\Contracts\\Auth\\MustVerifyEmail"],
      "relationships": [
        {
          "type": "hasMany",
          "related": "App\\Models\\Post",
          "method": "posts"
        }
      ]
    }
  ]
}
```

### Detailed Model Analysis

```bash
# Include all model details
php artisan atlas:generate --type=models --detailed --format=markdown --output=docs/models.md
```

## 🔗 Relationship Analysis

### Visualize Model Relationships

```bash
# Generate relationship diagram
php artisan atlas:generate --type=models --format=mermaid --output=diagrams/model-relationships.mmd
```

**Example Mermaid Output:**
```mermaid
graph TD
    User[User Model]
    Post[Post Model]
    Comment[Comment Model]
    Category[Category Model]
    
    User --> Post : hasMany
    Post --> User : belongsTo  
    Post --> Comment : hasMany
    Post --> Category : belongsToMany
    Comment --> User : belongsTo
    Comment --> Post : belongsTo
```

### Programmatic Relationship Analysis

```php
<?php

use LaravelAtlas\Facades\Atlas;

class ModelRelationshipAnalyzer
{
    public function analyzeRelationships(): array
    {
        // Scan models with relationships
        $modelData = Atlas::scan('models', [
            'include_relationships' => true,
            'include_observers' => true
        ]);

        $analysis = [
            'total_models' => count($modelData['data']),
            'relationships' => $this->extractRelationships($modelData['data']),
            'relationship_counts' => $this->countRelationshipTypes($modelData['data']),
            'orphaned_models' => $this->findOrphanedModels($modelData['data'])
        ];

        return $analysis;
    }

    private function extractRelationships(array $models): array
    {
        $relationships = [];
        
        foreach ($models as $model) {
            if (isset($model['relationships'])) {
                foreach ($model['relationships'] as $relationship) {
                    $relationships[] = [
                        'from' => $model['name'],
                        'to' => basename(str_replace('\\', '/', $relationship['related'])),
                        'type' => $relationship['type'],
                        'method' => $relationship['method'] ?? null
                    ];
                }
            }
        }
        
        return $relationships;
    }

    private function countRelationshipTypes(array $models): array
    {
        $counts = [];
        
        foreach ($models as $model) {
            if (isset($model['relationships'])) {
                foreach ($model['relationships'] as $relationship) {
                    $type = $relationship['type'];
                    $counts[$type] = ($counts[$type] ?? 0) + 1;
                }
            }
        }
        
        return $counts;
    }

    private function findOrphanedModels(array $models): array
    {
        return array_filter($models, function($model) {
            return empty($model['relationships']);
        });
    }
}

// Usage
$analyzer = new ModelRelationshipAnalyzer();
$analysis = $analyzer->analyzeRelationships();

echo "Total Models: " . $analysis['total_models'] . "\n";
echo "Relationship Types:\n";
foreach ($analysis['relationship_counts'] as $type => $count) {
    echo "  - {$type}: {$count}\n";
}
echo "Orphaned Models: " . count($analysis['orphaned_models']) . "\n";
```

## 📊 Model Statistics and Metrics

### Generate Model Statistics

```php
<?php

use LaravelAtlas\Facades\Atlas;

class ModelStatisticsGenerator
{
    public function generateReport(): array
    {
        $modelData = Atlas::scan('models', [
            'include_relationships' => true,
            'include_observers' => true,
            'include_factories' => true,
            'include_attributes' => true,
            'include_scopes' => true
        ]);

        return [
            'summary' => $this->generateSummary($modelData['data']),
            'relationship_analysis' => $this->analyzeRelationships($modelData['data']),
            'coverage_analysis' => $this->analyzeCoverage($modelData['data']),
            'complexity_metrics' => $this->calculateComplexity($modelData['data'])
        ];
    }

    private function generateSummary(array $models): array
    {
        return [
            'total_models' => count($models),
            'models_with_relationships' => count(array_filter($models, fn($m) => !empty($m['relationships']))),
            'models_with_observers' => count(array_filter($models, fn($m) => !empty($m['observers']))),
            'models_with_factories' => count(array_filter($models, fn($m) => !empty($m['factory']))),
        ];
    }

    private function analyzeRelationships(array $models): array
    {
        $allRelationships = [];
        foreach ($models as $model) {
            if (isset($model['relationships'])) {
                $allRelationships = array_merge($allRelationships, $model['relationships']);
            }
        }

        $typeCount = [];
        foreach ($allRelationships as $relationship) {
            $type = $relationship['type'];
            $typeCount[$type] = ($typeCount[$type] ?? 0) + 1;
        }

        return [
            'total_relationships' => count($allRelationships),
            'relationship_types' => $typeCount,
            'average_per_model' => count($models) > 0 ? count($allRelationships) / count($models) : 0
        ];
    }

    private function analyzeCoverage(array $models): array
    {
        $totalModels = count($models);
        
        return [
            'relationship_coverage' => $totalModels > 0 ? 
                (count(array_filter($models, fn($m) => !empty($m['relationships']))) / $totalModels) * 100 : 0,
            'observer_coverage' => $totalModels > 0 ? 
                (count(array_filter($models, fn($m) => !empty($m['observers']))) / $totalModels) * 100 : 0,
            'factory_coverage' => $totalModels > 0 ? 
                (count(array_filter($models, fn($m) => !empty($m['factory']))) / $totalModels) * 100 : 0,
        ];
    }

    private function calculateComplexity(array $models): array
    {
        $complexityScores = [];
        
        foreach ($models as $model) {
            $score = 1; // Base complexity
            
            // Add complexity for relationships
            if (isset($model['relationships'])) {
                $score += count($model['relationships']) * 2;
            }
            
            // Add complexity for observers
            if (isset($model['observers'])) {
                $score += count($model['observers']);
            }
            
            // Add complexity for scopes
            if (isset($model['scopes'])) {
                $score += count($model['scopes']);
            }
            
            $complexityScores[$model['name']] = $score;
        }
        
        return [
            'model_complexity' => $complexityScores,
            'average_complexity' => count($complexityScores) > 0 ? array_sum($complexityScores) / count($complexityScores) : 0,
            'most_complex' => !empty($complexityScores) ? array_keys($complexityScores, max($complexityScores)) : [],
        ];
    }
}

// Generate and display report
$generator = new ModelStatisticsGenerator();
$report = $generator->generateReport();

echo "=== Model Statistics Report ===\n\n";

echo "Summary:\n";
foreach ($report['summary'] as $key => $value) {
    echo "  " . str_replace('_', ' ', ucfirst($key)) . ": {$value}\n";
}

echo "\nRelationship Analysis:\n";
foreach ($report['relationship_analysis'] as $key => $value) {
    if (is_array($value)) {
        echo "  " . str_replace('_', ' ', ucfirst($key)) . ":\n";
        foreach ($value as $subKey => $subValue) {
            echo "    {$subKey}: {$subValue}\n";
        }
    } else {
        echo "  " . str_replace('_', ' ', ucfirst($key)) . ": " . (is_numeric($value) ? round($value, 2) : $value) . "\n";
    }
}

echo "\nCoverage Analysis:\n";
foreach ($report['coverage_analysis'] as $key => $value) {
    echo "  " . str_replace('_', ' ', ucfirst($key)) . ": " . round($value, 1) . "%\n";
}
```

## 🔍 Finding Specific Model Patterns

### Find Models Without Relationships

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Find models without relationships (potential issues or simple models)
$modelData = Atlas::scan('models', ['include_relationships' => true]);

$orphanedModels = array_filter($modelData['data'], function($model) {
    return empty($model['relationships']);
});

echo "Models without relationships:\n";
foreach ($orphanedModels as $model) {
    echo "  - {$model['name']} ({$model['path']})\n";
}
```

### Find Models with Many Relationships

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Find models with many relationships (potential complexity)
$modelData = Atlas::scan('models', ['include_relationships' => true]);

$complexModels = array_filter($modelData['data'], function($model) {
    return isset($model['relationships']) && count($model['relationships']) >= 5;
});

echo "Models with many relationships (>=5):\n";
foreach ($complexModels as $model) {
    $relationshipCount = count($model['relationships']);
    echo "  - {$model['name']}: {$relationshipCount} relationships\n";
}
```

### Find Circular Relationships

```php
<?php

use LaravelAtlas\Facades\Atlas;

class CircularRelationshipDetector
{
    public function findCircularRelationships(): array
    {
        $modelData = Atlas::scan('models', ['include_relationships' => true]);
        $relationships = $this->buildRelationshipGraph($modelData['data']);
        
        return $this->detectCycles($relationships);
    }
    
    private function buildRelationshipGraph(array $models): array
    {
        $graph = [];
        
        foreach ($models as $model) {
            $modelName = $model['name'];
            $graph[$modelName] = [];
            
            if (isset($model['relationships'])) {
                foreach ($model['relationships'] as $relationship) {
                    $relatedModel = basename(str_replace('\\', '/', $relationship['related']));
                    $graph[$modelName][] = $relatedModel;
                }
            }
        }
        
        return $graph;
    }
    
    private function detectCycles(array $graph): array
    {
        $cycles = [];
        $visited = [];
        $recursionStack = [];
        
        foreach ($graph as $node => $edges) {
            if (!isset($visited[$node])) {
                $path = [];
                $this->dfs($node, $graph, $visited, $recursionStack, $cycles, $path);
            }
        }
        
        return $cycles;
    }
    
    private function dfs(string $node, array $graph, array &$visited, array &$recursionStack, array &$cycles, array $path): void
    {
        $visited[$node] = true;
        $recursionStack[$node] = true;
        $path[] = $node;
        
        if (isset($graph[$node])) {
            foreach ($graph[$node] as $neighbor) {
                if (!isset($visited[$neighbor])) {
                    $this->dfs($neighbor, $graph, $visited, $recursionStack, $cycles, $path);
                } elseif (isset($recursionStack[$neighbor]) && $recursionStack[$neighbor]) {
                    // Found a cycle
                    $cycleStart = array_search($neighbor, $path);
                    if ($cycleStart !== false) {
                        $cycles[] = array_slice($path, $cycleStart);
                    }
                }
            }
        }
        
        $recursionStack[$node] = false;
    }
}

// Usage
$detector = new CircularRelationshipDetector();
$cycles = $detector->findCircularRelationships();

if (empty($cycles)) {
    echo "No circular relationships detected.\n";
} else {
    echo "Circular relationships found:\n";
    foreach ($cycles as $i => $cycle) {
        echo "  Cycle " . ($i + 1) . ": " . implode(' -> ', $cycle) . " -> " . $cycle[0] . "\n";
    }
}
```

## 📈 Model Evolution Tracking

### Track Model Changes Over Time

```bash
#!/bin/bash
# Script to track model changes over time

# Create timestamp for this analysis
timestamp=$(date +"%Y%m%d_%H%M%S")

# Generate model analysis
php artisan atlas:generate --type=models --format=json --output="analysis/models_${timestamp}.json"

# Compare with previous analysis if exists
latest_file=$(ls -t analysis/models_*.json 2>/dev/null | head -n 1)
previous_file=$(ls -t analysis/models_*.json 2>/dev/null | head -n 2 | tail -n 1)

if [[ "$latest_file" != "$previous_file" && -f "$previous_file" ]]; then
    echo "Changes detected between $previous_file and $latest_file"
    
    # You could add custom comparison logic here
    # For example, using jq to compare model counts
    current_count=$(jq '.data | length' "$latest_file")
    previous_count=$(jq '.data | length' "$previous_file")
    
    echo "Model count changed from $previous_count to $current_count"
    
    if (( current_count > previous_count )); then
        echo "✅ Models added: $((current_count - previous_count))"
    elif (( current_count < previous_count )); then
        echo "❌ Models removed: $((previous_count - current_count))"
    else
        echo "📊 Model count unchanged, but structure may have changed"
    fi
fi
```

## 🎯 Best Practices for Model Analysis

### 1. Regular Architecture Reviews

```bash
# Weekly model analysis
php artisan atlas:generate --type=models --detailed --format=markdown --output="docs/models_$(date +%Y%m%d).md"
```

### 2. Integration with CI/CD

```yaml
# GitHub Actions example
- name: Analyze Models
  run: |
    php artisan atlas:generate --type=models --format=json --output=analysis/models.json
    # Add validation logic here
```

### 3. Model Complexity Monitoring

```php
// Set up alerts for complex models
$modelData = Atlas::scan('models', ['include_relationships' => true]);

foreach ($modelData['data'] as $model) {
    $relationshipCount = isset($model['relationships']) ? count($model['relationships']) : 0;
    
    if ($relationshipCount > 10) {
        echo "⚠️  Warning: {$model['name']} has {$relationshipCount} relationships\n";
        echo "   Consider breaking down into smaller models or using composition\n";
    }
}
```

### 4. Documentation Generation

```bash
# Generate comprehensive model documentation
php artisan atlas:generate --type=models --detailed --format=markdown --output=docs/DATA_MODEL.md
php artisan atlas:generate --type=models --format=mermaid --output=diagrams/models.mmd

# Include in your documentation site
echo "![Model Relationships](diagrams/models.mmd)" >> docs/ARCHITECTURE.md
```

## 🔗 Related Examples

- [Basic Usage](basic-usage.md) - Getting started with Laravel Atlas
- [Route Mapping](routes.md) - Analyzing application routes
- [Export Formats](exports/) - Different export format examples
- [Advanced Analysis](advanced/) - Complex architectural analysis