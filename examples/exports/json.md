# JSON Export Examples

Examples demonstrating how to work with JSON exports from Laravel Atlas.

## 📋 Prerequisites

- Laravel Atlas installed
- Basic understanding of JSON structure
- Laravel application with some components to analyze

## 🚀 Basic JSON Export

### Simple JSON Export

```bash
# Generate JSON for all components
php artisan atlas:generate --format=json

# Generate JSON for specific component
php artisan atlas:generate --type=models --format=json

# Save to file
php artisan atlas:generate --format=json --output=atlas.json
```

### Understanding JSON Structure

Every Laravel Atlas JSON export follows this structure:

```json
{
  "atlas_version": "1.0.0",
  "generated_at": "2024-01-01T12:00:00.000000Z",
  "generation_time_ms": 245.67,
  "type": "models",
  "format": "json",
  "options": {
    "detailed": false,
    "include_relationships": true
  },
  "summary": {
    "total_components": 5,
    "types": {
      "models": 5
    }
  },
  "data": {
    "models": {
      "type": "models",
      "scan_path": "/app/Models",
      "options": {},
      "data": [
        {
          "name": "User",
          "namespace": "App\\Models",
          "path": "/app/Models/User.php",
          // ... component-specific data
        }
      ]
    }
  }
}
```

## 💻 Programmatic JSON Usage

### Reading JSON Data

```php
<?php

use LaravelAtlas\Facades\Atlas;

class JsonAnalyzer
{
    public function analyzeFromJson(string $jsonPath): array
    {
        // Read JSON file
        $jsonContent = file_get_contents($jsonPath);
        $data = json_decode($jsonContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }
        
        return $this->extractMetrics($data);
    }
    
    public function analyzeFromAtlas(): array
    {
        // Generate JSON directly
        $jsonString = Atlas::export('all', 'json');
        $data = json_decode($jsonString, true);
        
        return $this->extractMetrics($data);
    }
    
    private function extractMetrics(array $data): array
    {
        $metrics = [
            'generation_info' => [
                'atlas_version' => $data['atlas_version'] ?? 'unknown',
                'generated_at' => $data['generated_at'] ?? 'unknown',
                'generation_time_ms' => $data['generation_time_ms'] ?? 0,
            ],
            'component_counts' => []
        ];
        
        if (isset($data['data'])) {
            foreach ($data['data'] as $type => $typeData) {
                $count = isset($typeData['data']) ? count($typeData['data']) : 0;
                $metrics['component_counts'][$type] = $count;
            }
        }
        
        return $metrics;
    }
}

// Usage examples
$analyzer = new JsonAnalyzer();

// From existing file
$fileMetrics = $analyzer->analyzeFromJson('atlas.json');

// Generated on demand
$liveMetrics = $analyzer->analyzeFromAtlas();

print_r($fileMetrics);
```

### JSON Data Processing

```php
<?php

use LaravelAtlas\Facades\Atlas;

class JsonProcessor
{
    public function processModelData(): array
    {
        $json = Atlas::export('models', 'json');
        $data = json_decode($json, true);
        
        $processed = [
            'models' => [],
            'relationships' => [],
            'statistics' => []
        ];
        
        if (isset($data['data']['models']['data'])) {
            foreach ($data['data']['models']['data'] as $model) {
                // Extract model info
                $processed['models'][] = [
                    'name' => $model['name'],
                    'namespace' => $model['namespace'],
                    'file_size' => $model['size'] ?? 0,
                    'relationship_count' => count($model['relationships'] ?? [])
                ];
                
                // Extract relationships
                if (isset($model['relationships'])) {
                    foreach ($model['relationships'] as $relationship) {
                        $processed['relationships'][] = [
                            'from' => $model['name'],
                            'to' => basename(str_replace('\\', '/', $relationship['related'])),
                            'type' => $relationship['type'],
                            'method' => $relationship['method'] ?? null
                        ];
                    }
                }
            }
        }
        
        // Calculate statistics
        $processed['statistics'] = [
            'total_models' => count($processed['models']),
            'total_relationships' => count($processed['relationships']),
            'average_relationships_per_model' => count($processed['models']) > 0 
                ? count($processed['relationships']) / count($processed['models']) : 0
        ];
        
        return $processed;
    }
    
    public function saveProcessedData(array $data, string $filename): void
    {
        file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

// Usage
$processor = new JsonProcessor();
$processedData = $processor->processModelData();
$processor->saveProcessedData($processedData, 'processed-models.json');

echo "Processed data saved to processed-models.json\n";
print_r($processedData['statistics']);
```

## 🔄 JSON Manipulation and Filtering

### Filter Specific Components

```php
<?php

use LaravelAtlas\Facades\Atlas;

class JsonFilter
{
    public function filterModelsByNamespace(string $namespace): string
    {
        $json = Atlas::export('models', 'json');
        $data = json_decode($json, true);
        
        if (isset($data['data']['models']['data'])) {
            $filteredModels = array_filter($data['data']['models']['data'], function($model) use ($namespace) {
                return strpos($model['namespace'], $namespace) === 0;
            });
            
            $data['data']['models']['data'] = array_values($filteredModels);
            $data['summary']['total_components'] = count($filteredModels);
        }
        
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    public function filterModelsWithRelationships(): string
    {
        $json = Atlas::export('models', 'json');
        $data = json_decode($json, true);
        
        if (isset($data['data']['models']['data'])) {
            $filteredModels = array_filter($data['data']['models']['data'], function($model) {
                return !empty($model['relationships']);
            });
            
            $data['data']['models']['data'] = array_values($filteredModels);
            $data['summary']['total_components'] = count($filteredModels);
        }
        
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    public function extractComponentNames(string $type): array
    {
        $json = Atlas::export($type, 'json');
        $data = json_decode($json, true);
        
        $names = [];
        if (isset($data['data'][$type]['data'])) {
            foreach ($data['data'][$type]['data'] as $component) {
                $names[] = $component['name'] ?? 'Unknown';
            }
        }
        
        return $names;
    }
}

// Usage examples
$filter = new JsonFilter();

// Filter models by namespace
$domainModels = $filter->filterModelsByNamespace('App\\Domain');
file_put_contents('domain-models.json', $domainModels);

// Filter models with relationships
$relatedModels = $filter->filterModelsWithRelationships();
file_put_contents('related-models.json', $relatedModels);

// Extract component names
$modelNames = $filter->extractComponentNames('models');
$controllerNames = $filter->extractComponentNames('controllers');

echo "Models: " . implode(', ', $modelNames) . "\n";
echo "Controllers: " . implode(', ', $controllerNames) . "\n";
```

## 📊 JSON Data Analysis

### Component Statistics

```php
<?php

use LaravelAtlas\Facades\Atlas;

class JsonAnalytics
{
    public function generateComponentReport(): array
    {
        $json = Atlas::export('all', 'json');
        $data = json_decode($json, true);
        
        $report = [
            'overview' => [
                'atlas_version' => $data['atlas_version'] ?? 'unknown',
                'generated_at' => $data['generated_at'] ?? 'unknown',
                'generation_time_ms' => $data['generation_time_ms'] ?? 0,
            ],
            'components' => [],
            'totals' => [
                'all_components' => 0,
                'files_analyzed' => 0,
                'total_file_size' => 0
            ]
        ];
        
        if (isset($data['data'])) {
            foreach ($data['data'] as $type => $typeData) {
                $componentCount = 0;
                $totalSize = 0;
                
                if (isset($typeData['data']) && is_array($typeData['data'])) {
                    $componentCount = count($typeData['data']);
                    
                    foreach ($typeData['data'] as $component) {
                        $totalSize += $component['size'] ?? 0;
                    }
                }
                
                $report['components'][$type] = [
                    'count' => $componentCount,
                    'total_size_bytes' => $totalSize,
                    'average_size_bytes' => $componentCount > 0 ? $totalSize / $componentCount : 0,
                    'scan_path' => $typeData['scan_path'] ?? 'unknown'
                ];
                
                $report['totals']['all_components'] += $componentCount;
                $report['totals']['files_analyzed'] += $componentCount;
                $report['totals']['total_file_size'] += $totalSize;
            }
        }
        
        return $report;
    }
    
    public function findLargestComponents(int $limit = 10): array
    {
        $json = Atlas::export('all', 'json');
        $data = json_decode($json, true);
        
        $allComponents = [];
        
        if (isset($data['data'])) {
            foreach ($data['data'] as $type => $typeData) {
                if (isset($typeData['data']) && is_array($typeData['data'])) {
                    foreach ($typeData['data'] as $component) {
                        $allComponents[] = [
                            'type' => $type,
                            'name' => $component['name'] ?? 'unknown',
                            'size' => $component['size'] ?? 0,
                            'path' => $component['path'] ?? 'unknown'
                        ];
                    }
                }
            }
        }
        
        // Sort by size descending
        usort($allComponents, function($a, $b) {
            return $b['size'] - $a['size'];
        });
        
        return array_slice($allComponents, 0, $limit);
    }
    
    public function analyzeRelationshipComplexity(): array
    {
        $json = Atlas::export('models', 'json');
        $data = json_decode($json, true);
        
        $complexity = [];
        
        if (isset($data['data']['models']['data'])) {
            foreach ($data['data']['models']['data'] as $model) {
                $relationshipCount = isset($model['relationships']) ? count($model['relationships']) : 0;
                
                $complexityScore = $this->calculateComplexityScore($model);
                
                $complexity[] = [
                    'name' => $model['name'],
                    'relationship_count' => $relationshipCount,
                    'complexity_score' => $complexityScore,
                    'has_observers' => !empty($model['observers']),
                    'has_factory' => !empty($model['factory'])
                ];
            }
        }
        
        // Sort by complexity score descending
        usort($complexity, function($a, $b) {
            return $b['complexity_score'] - $a['complexity_score'];
        });
        
        return $complexity;
    }
    
    private function calculateComplexityScore(array $model): int
    {
        $score = 1; // Base score
        
        // Add points for relationships
        if (isset($model['relationships'])) {
            $score += count($model['relationships']) * 2;
        }
        
        // Add points for observers
        if (isset($model['observers'])) {
            $score += count($model['observers']);
        }
        
        // Add points for scopes
        if (isset($model['scopes'])) {
            $score += count($model['scopes']);
        }
        
        return $score;
    }
}

// Usage
$analytics = new JsonAnalytics();

// Generate component report
$report = $analytics->generateComponentReport();
echo "=== Component Report ===\n";
echo "Total Components: " . $report['totals']['all_components'] . "\n";
echo "Total File Size: " . number_format($report['totals']['total_file_size']) . " bytes\n";
echo "Generation Time: " . $report['overview']['generation_time_ms'] . "ms\n\n";

foreach ($report['components'] as $type => $info) {
    echo "{$type}: {$info['count']} components, avg size: " . 
         number_format($info['average_size_bytes']) . " bytes\n";
}

// Find largest components
echo "\n=== Largest Components ===\n";
$largest = $analytics->findLargestComponents(5);
foreach ($largest as $component) {
    echo "{$component['type']}: {$component['name']} - " . 
         number_format($component['size']) . " bytes\n";
}

// Analyze model complexity
echo "\n=== Most Complex Models ===\n";
$complexity = $analytics->analyzeRelationshipComplexity();
foreach (array_slice($complexity, 0, 5) as $model) {
    echo "{$model['name']}: score {$model['complexity_score']}, " .
         "{$model['relationship_count']} relationships\n";
}
```

## 🔄 JSON Comparison and Diff

### Compare JSON Exports

```php
<?php

class JsonComparator
{
    public function compareAtlasExports(string $oldJsonPath, string $newJsonPath): array
    {
        $oldData = json_decode(file_get_contents($oldJsonPath), true);
        $newData = json_decode(file_get_contents($newJsonPath), true);
        
        $comparison = [
            'metadata' => [
                'old_version' => $oldData['atlas_version'] ?? 'unknown',
                'new_version' => $newData['atlas_version'] ?? 'unknown',
                'old_generated' => $oldData['generated_at'] ?? 'unknown',
                'new_generated' => $newData['generated_at'] ?? 'unknown'
            ],
            'component_changes' => $this->compareComponents($oldData, $newData),
            'summary' => []
        ];
        
        $comparison['summary'] = $this->generateChangeSummary($comparison['component_changes']);
        
        return $comparison;
    }
    
    private function compareComponents(array $oldData, array $newData): array
    {
        $changes = [];
        
        $oldComponents = $this->extractComponentNames($oldData);
        $newComponents = $this->extractComponentNames($newData);
        
        foreach ($oldComponents as $type => $oldNames) {
            $newNames = $newComponents[$type] ?? [];
            
            $added = array_diff($newNames, $oldNames);
            $removed = array_diff($oldNames, $newNames);
            $unchanged = array_intersect($oldNames, $newNames);
            
            $changes[$type] = [
                'added' => array_values($added),
                'removed' => array_values($removed),
                'unchanged' => count($unchanged),
                'total_old' => count($oldNames),
                'total_new' => count($newNames)
            ];
        }
        
        return $changes;
    }
    
    private function extractComponentNames(array $data): array
    {
        $components = [];
        
        if (isset($data['data'])) {
            foreach ($data['data'] as $type => $typeData) {
                $components[$type] = [];
                
                if (isset($typeData['data']) && is_array($typeData['data'])) {
                    foreach ($typeData['data'] as $component) {
                        $components[$type][] = $component['name'] ?? 'unknown';
                    }
                }
            }
        }
        
        return $components;
    }
    
    private function generateChangeSummary(array $componentChanges): array
    {
        $totalAdded = 0;
        $totalRemoved = 0;
        $changesDetected = false;
        
        foreach ($componentChanges as $type => $changes) {
            $totalAdded += count($changes['added']);
            $totalRemoved += count($changes['removed']);
            
            if (!empty($changes['added']) || !empty($changes['removed'])) {
                $changesDetected = true;
            }
        }
        
        return [
            'total_added' => $totalAdded,
            'total_removed' => $totalRemoved,
            'changes_detected' => $changesDetected,
            'net_change' => $totalAdded - $totalRemoved
        ];
    }
}

// Usage
$comparator = new JsonComparator();
$comparison = $comparator->compareAtlasExports('old-atlas.json', 'new-atlas.json');

echo "=== Atlas Export Comparison ===\n";
echo "Changes Detected: " . ($comparison['summary']['changes_detected'] ? 'Yes' : 'No') . "\n";
echo "Total Added: " . $comparison['summary']['total_added'] . "\n";
echo "Total Removed: " . $comparison['summary']['total_removed'] . "\n";
echo "Net Change: " . $comparison['summary']['net_change'] . "\n\n";

foreach ($comparison['component_changes'] as $type => $changes) {
    if (!empty($changes['added']) || !empty($changes['removed'])) {
        echo "{$type}:\n";
        
        if (!empty($changes['added'])) {
            echo "  Added: " . implode(', ', $changes['added']) . "\n";
        }
        
        if (!empty($changes['removed'])) {
            echo "  Removed: " . implode(', ', $changes['removed']) . "\n";
        }
        
        echo "  Count: {$changes['total_old']} -> {$changes['total_new']}\n\n";
    }
}
```

## 🎯 Advanced JSON Usage

### API Integration

```php
<?php

// API endpoint to serve Atlas data
use Illuminate\Http\JsonResponse;
use LaravelAtlas\Facades\Atlas;

class ArchitectureApiController extends Controller
{
    public function getArchitecture(Request $request): JsonResponse
    {
        $type = $request->get('type', 'all');
        $detailed = $request->boolean('detailed');
        
        try {
            $options = $detailed ? ['detailed' => true] : [];
            $data = Atlas::scan($type, $options);
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'generated_at' => now(),
                    'type' => $type,
                    'detailed' => $detailed
                ]
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
    
    public function downloadArchitecture(string $type, string $format = 'json'): Response
    {
        try {
            $content = Atlas::export($type, 'json');
            $filename = "architecture-{$type}-" . date('Y-m-d') . ".json";
            
            return response($content)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', "attachment; filename={$filename}");
                
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
```

### Automated JSON Processing

```bash
#!/bin/bash
# Script to automatically process JSON exports

echo "🔄 Generating Atlas JSON export..."
php artisan atlas:generate --format=json --output=atlas.json

echo "📊 Processing JSON data..."
php -r "
\$data = json_decode(file_get_contents('atlas.json'), true);
\$componentCounts = [];

foreach (\$data['data'] as \$type => \$typeData) {
    \$count = isset(\$typeData['data']) ? count(\$typeData['data']) : 0;
    \$componentCounts[\$type] = \$count;
}

echo 'Component Summary:' . PHP_EOL;
foreach (\$componentCounts as \$type => \$count) {
    echo '  ' . \$type . ': ' . \$count . PHP_EOL;
}

echo 'Total: ' . array_sum(\$componentCounts) . ' components' . PHP_EOL;
echo 'Generation Time: ' . (\$data['generation_time_ms'] ?? 0) . 'ms' . PHP_EOL;
"

echo "✅ JSON processing complete!"
```

## 🎯 Best Practices

### 1. Version Your JSON Exports
```bash
# Include timestamps in filenames
php artisan atlas:generate --format=json --output="atlas-$(date +%Y%m%d-%H%M%S).json"
```

### 2. Validate JSON Structure
```php
// Always validate JSON after generation
$json = Atlas::export('all', 'json');
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    throw new RuntimeException('Invalid JSON generated: ' . json_last_error_msg());
}
```

### 3. Use JSON for Integration
```php
// Perfect for feeding data to other tools
$jsonData = Atlas::export('routes', 'json');
$postmanCollection = convertToPostmanCollection(json_decode($jsonData, true));
```

### 4. Compress Large JSON Files
```bash
# Compress JSON exports for storage
php artisan atlas:generate --format=json --output=atlas.json
gzip atlas.json
```

## 🔗 Related Examples

- [Basic Usage](../basic-usage.md) - Getting started with Laravel Atlas
- [Markdown Export](markdown.md) - Human-readable documentation
- [Mermaid Diagrams](mermaid.md) - Visual architecture diagrams
- [Advanced Analysis](../advanced/) - Complex architectural analysis