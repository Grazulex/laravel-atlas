# PHP Data Export Examples

These examples demonstrate how to use Laravel Atlas to export raw PHP data for advanced processing.

## 📋 Prerequisites

- Laravel Atlas installed: `composer require grazulex/laravel-atlas --dev`

## 🐘 Basic PHP Export

### 1. Generate PHP Data Files

```bash
# Generate basic PHP data export
php artisan atlas:generate --format=php

# Export specific component data
php artisan atlas:generate --type=models --format=php --output=storage/models-data.php
php artisan atlas:generate --type=routes --format=php --output=storage/routes-data.php
php artisan atlas:generate --type=services --format=php --output=storage/services-data.php

# Export all components
php artisan atlas:generate --type=all --format=php --output=storage/complete-atlas.php
```

### 2. Programmatic PHP Export

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Export data as PHP code
$phpData = Atlas::export('models', 'php');
file_put_contents('storage/models.php', $phpData);

// Export with custom configuration
$customPhpData = Atlas::export('routes', 'php', [
    'format_code' => true,
    'include_metadata' => true,
    'variable_name' => 'routeData',
    'export_as_array' => false,
]);

file_put_contents('storage/routes-custom.php', $customPhpData);

echo "PHP data files generated\n";
```

## 📊 Working with Exported PHP Data

### 1. Loading and Using Exported Data

```php
<?php

// Load exported model data
$modelData = include 'storage/models-data.php';

echo "Loaded data for " . count($modelData['data']['models']['data']) . " models\n";

// Process model relationships
foreach ($modelData['data']['models']['data'] as $model) {
    echo "Model: {$model['name']}\n";
    
    if (isset($model['relationships'])) {
        echo "  Relationships:\n";
        foreach ($model['relationships'] as $relationship) {
            echo "    - {$relationship['type']}: {$relationship['related']}\n";
        }
    }
    echo "\n";
}
```

### 2. Custom Data Processing

```php
<?php

// Load multiple component data files
$components = [
    'models' => include 'storage/models-data.php',
    'controllers' => include 'storage/controllers-data.php', 
    'routes' => include 'storage/routes-data.php',
];

// Build dependency graph
$dependencyGraph = [];

// Process model relationships
foreach ($components['models']['data']['models']['data'] as $model) {
    $modelName = $model['name'];
    $dependencyGraph[$modelName] = [];
    
    if (isset($model['relationships'])) {
        foreach ($model['relationships'] as $relationship) {
            $dependencyGraph[$modelName][] = basename($relationship['related']);
        }
    }
}

// Process controller dependencies
foreach ($components['controllers']['data']['controllers']['data'] as $controller) {
    $controllerName = $controller['name'];
    $dependencyGraph[$controllerName] = [];
    
    if (isset($controller['dependencies'])) {
        foreach ($controller['dependencies'] as $dependency) {
            $dependencyGraph[$controllerName][] = basename($dependency);
        }
    }
}

echo "Dependency Graph:\n";
foreach ($dependencyGraph as $component => $dependencies) {
    if (!empty($dependencies)) {
        echo "{$component} depends on: " . implode(', ', $dependencies) . "\n";
    }
}
```

## 🔧 Advanced PHP Data Processing

### 1. Building Custom Reports

```php
<?php

// Load comprehensive application data
$atlasData = include 'storage/complete-atlas.php';

// Generate architecture report
class ArchitectureAnalyzer
{
    private $data;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function generateReport(): array
    {
        return [
            'summary' => $this->getSummary(),
            'complexity_analysis' => $this->analyzeComplexity(),
            'dependency_analysis' => $this->analyzeDependencies(),
            'recommendations' => $this->getRecommendations(),
        ];
    }
    
    private function getSummary(): array
    {
        $summary = [];
        
        foreach ($this->data['data'] as $type => $componentData) {
            $summary[$type] = [
                'count' => count($componentData['data'] ?? []),
                'type' => $type,
            ];
        }
        
        return $summary;
    }
    
    private function analyzeComplexity(): array
    {
        $complexity = [];
        
        // Analyze model complexity
        if (isset($this->data['data']['models'])) {
            foreach ($this->data['data']['models']['data'] as $model) {
                $score = 0;
                
                // Relationship complexity
                if (isset($model['relationships'])) {
                    $score += count($model['relationships']) * 2;
                }
                
                // Observer complexity
                if (isset($model['observers'])) {
                    $score += count($model['observers']) * 1;
                }
                
                $complexity['models'][$model['name']] = $score;
            }
        }
        
        return $complexity;
    }
    
    private function analyzeDependencies(): array
    {
        $dependencies = [];
        
        foreach ($this->data['data'] as $type => $componentData) {
            foreach ($componentData['data'] ?? [] as $component) {
                if (isset($component['dependencies'])) {
                    $dependencies[$type][$component['name']] = count($component['dependencies']);
                }
            }
        }
        
        return $dependencies;
    }
    
    private function getRecommendations(): array
    {
        $recommendations = [];
        
        // Check for models with too many relationships
        if (isset($this->data['data']['models'])) {
            foreach ($this->data['data']['models']['data'] as $model) {
                if (isset($model['relationships']) && count($model['relationships']) > 8) {
                    $recommendations[] = "Model {$model['name']} has " . count($model['relationships']) . " relationships - consider splitting";
                }
            }
        }
        
        return $recommendations;
    }
}

$analyzer = new ArchitectureAnalyzer($atlasData);
$report = $analyzer->generateReport();

// Save analysis report
file_put_contents('reports/architecture-analysis.php', '<?php return ' . var_export($report, true) . ';');

echo "Architecture analysis complete!\n";
print_r($report['summary']);
```

### 2. Data Transformation and Migration

```php
<?php

// Load current architecture data
$currentData = include 'storage/complete-atlas.php';

// Transform data for external systems
class DataTransformer
{
    public static function toGraphML(array $atlasData): string
    {
        $graphml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $graphml .= '<graphml xmlns="http://graphml.graphdrawing.org/xmlns">' . "\n";
        $graphml .= '<graph id="ApplicationArchitecture" edgedefault="directed">' . "\n";
        
        $nodeId = 0;
        $nodeMap = [];
        
        // Add nodes
        foreach ($atlasData['data'] as $type => $componentData) {
            foreach ($componentData['data'] ?? [] as $component) {
                $id = 'n' . $nodeId++;
                $nodeMap[$component['name']] = $id;
                
                $graphml .= '<node id="' . $id . '">' . "\n";
                $graphml .= '  <data key="name">' . htmlspecialchars($component['name']) . '</data>' . "\n";
                $graphml .= '  <data key="type">' . $type . '</data>' . "\n";
                $graphml .= '</node>' . "\n";
            }
        }
        
        // Add edges (relationships)
        $edgeId = 0;
        foreach ($atlasData['data'] as $type => $componentData) {
            foreach ($componentData['data'] ?? [] as $component) {
                if (isset($component['relationships'])) {
                    foreach ($component['relationships'] as $relationship) {
                        $targetName = basename($relationship['related']);
                        if (isset($nodeMap[$targetName])) {
                            $graphml .= '<edge id="e' . $edgeId++ . '" source="' . $nodeMap[$component['name']] . '" target="' . $nodeMap[$targetName] . '">' . "\n";
                            $graphml .= '  <data key="type">' . htmlspecialchars($relationship['type']) . '</data>' . "\n";
                            $graphml .= '</edge>' . "\n";
                        }
                    }
                }
            }
        }
        
        $graphml .= '</graph>' . "\n";
        $graphml .= '</graphml>' . "\n";
        
        return $graphml;
    }
    
    public static function toCytoscape(array $atlasData): array
    {
        $elements = [];
        
        // Add nodes
        foreach ($atlasData['data'] as $type => $componentData) {
            foreach ($componentData['data'] ?? [] as $component) {
                $elements[] = [
                    'data' => [
                        'id' => $component['name'],
                        'label' => $component['name'],
                        'type' => $type,
                        'namespace' => $component['namespace'] ?? '',
                    ]
                ];
            }
        }
        
        // Add edges
        foreach ($atlasData['data'] as $type => $componentData) {
            foreach ($componentData['data'] ?? [] as $component) {
                if (isset($component['relationships'])) {
                    foreach ($component['relationships'] as $relationship) {
                        $target = basename($relationship['related']);
                        $elements[] = [
                            'data' => [
                                'source' => $component['name'],
                                'target' => $target,
                                'type' => $relationship['type'],
                            ]
                        ];
                    }
                }
            }
        }
        
        return $elements;
    }
}

// Export to GraphML format
$graphml = DataTransformer::toGraphML($currentData);
file_put_contents('exports/architecture.graphml', $graphml);

// Export to Cytoscape format
$cytoscape = DataTransformer::toCytoscape($currentData);
file_put_contents('exports/architecture-cytoscape.json', json_encode($cytoscape, JSON_PRETTY_PRINT));

echo "Data exported to external formats\n";
```

## 📈 Data Analysis and Metrics

### 1. Architecture Metrics Calculator

```php
<?php

class ArchitectureMetrics
{
    private $data;
    
    public function __construct(array $atlasData)
    {
        $this->data = $atlasData;
    }
    
    public function calculateMetrics(): array
    {
        return [
            'component_counts' => $this->getComponentCounts(),
            'dependency_metrics' => $this->getDependencyMetrics(),
            'complexity_score' => $this->calculateComplexityScore(),
            'coupling_metrics' => $this->getCouplingMetrics(),
            'cohesion_metrics' => $this->getCohesionMetrics(),
        ];
    }
    
    private function getComponentCounts(): array
    {
        $counts = [];
        
        foreach ($this->data['data'] as $type => $componentData) {
            $counts[$type] = count($componentData['data'] ?? []);
        }
        
        return $counts;
    }
    
    private function getDependencyMetrics(): array
    {
        $totalDependencies = 0;
        $maxDependencies = 0;
        $componentCount = 0;
        
        foreach ($this->data['data'] as $type => $componentData) {
            foreach ($componentData['data'] ?? [] as $component) {
                $componentCount++;
                
                $depCount = 0;
                if (isset($component['dependencies'])) {
                    $depCount = count($component['dependencies']);
                } elseif (isset($component['relationships'])) {
                    $depCount = count($component['relationships']);
                }
                
                $totalDependencies += $depCount;
                $maxDependencies = max($maxDependencies, $depCount);
            }
        }
        
        return [
            'total_dependencies' => $totalDependencies,
            'average_dependencies' => $componentCount > 0 ? $totalDependencies / $componentCount : 0,
            'max_dependencies' => $maxDependencies,
        ];
    }
    
    private function calculateComplexityScore(): float
    {
        $score = 0;
        $componentCount = 0;
        
        foreach ($this->data['data'] as $type => $componentData) {
            foreach ($componentData['data'] ?? [] as $component) {
                $componentCount++;
                
                // Base complexity
                $componentScore = 1;
                
                // Add complexity for dependencies
                if (isset($component['dependencies'])) {
                    $componentScore += count($component['dependencies']) * 0.5;
                }
                
                // Add complexity for relationships
                if (isset($component['relationships'])) {
                    $componentScore += count($component['relationships']) * 0.3;
                }
                
                // Add complexity for methods (if available)
                if (isset($component['methods'])) {
                    $componentScore += count($component['methods']) * 0.1;
                }
                
                $score += $componentScore;
            }
        }
        
        return $componentCount > 0 ? $score / $componentCount : 0;
    }
    
    private function getCouplingMetrics(): array
    {
        // Calculate coupling between components
        $couplings = [];
        
        foreach ($this->data['data'] as $type => $componentData) {
            foreach ($componentData['data'] ?? [] as $component) {
                $componentCoupling = 0;
                
                // Count outgoing dependencies
                if (isset($component['dependencies'])) {
                    $componentCoupling += count($component['dependencies']);
                }
                
                if (isset($component['relationships'])) {
                    $componentCoupling += count($component['relationships']);
                }
                
                $couplings[$type][$component['name']] = $componentCoupling;
            }
        }
        
        return $couplings;
    }
    
    private function getCohesionMetrics(): array
    {
        // Simplified cohesion calculation
        $cohesion = [];
        
        foreach ($this->data['data'] as $type => $componentData) {
            $totalComponents = count($componentData['data'] ?? []);
            $internalReferences = 0;
            
            foreach ($componentData['data'] ?? [] as $component) {
                if (isset($component['relationships'])) {
                    foreach ($component['relationships'] as $relationship) {
                        // Check if relationship is within same component type
                        if (str_contains($relationship['related'], $component['namespace'] ?? '')) {
                            $internalReferences++;
                        }
                    }
                }
            }
            
            $cohesion[$type] = $totalComponents > 0 ? $internalReferences / $totalComponents : 0;
        }
        
        return $cohesion;
    }
}

// Load data and calculate metrics
$atlasData = include 'storage/complete-atlas.php';
$metrics = new ArchitectureMetrics($atlasData);
$results = $metrics->calculateMetrics();

// Save metrics report
file_put_contents('reports/architecture-metrics.php', '<?php return ' . var_export($results, true) . ';');

echo "Architecture Metrics:\n";
echo "Complexity Score: " . round($results['complexity_score'], 2) . "\n";
echo "Average Dependencies: " . round($results['dependency_metrics']['average_dependencies'], 2) . "\n";
echo "Total Components: " . array_sum($results['component_counts']) . "\n";
```

## 🔄 Continuous Integration

### 1. Automated Data Export Pipeline

```bash
#!/bin/bash
# export-architecture-data.sh

echo "Starting architecture data export pipeline..."

# Create export directories
mkdir -p exports/php
mkdir -p exports/json
mkdir -p exports/external

# Export PHP data for all components
components=("models" "controllers" "routes" "services" "jobs" "events" "commands" "observers" "listeners" "actions")

for component in "${components[@]}"
do
    echo "Exporting ${component} PHP data..."
    php artisan atlas:generate --type=${component} --format=php --output=exports/php/${component}-data.php
    php artisan atlas:generate --type=${component} --format=json --output=exports/json/${component}-data.json
done

# Export complete application data
echo "Exporting complete application data..."
php artisan atlas:generate --type=all --format=php --output=exports/php/complete-atlas.php

# Process and transform data
echo "Processing architecture data..."
php -r "
// Load complete data
\$data = include 'exports/php/complete-atlas.php';

// Calculate basic metrics
\$metrics = [];
foreach (\$data['data'] as \$type => \$componentData) {
    \$metrics[\$type] = count(\$componentData['data'] ?? []);
}

// Save metrics
file_put_contents('exports/metrics.json', json_encode(\$metrics, JSON_PRETTY_PRINT));

echo 'Metrics saved to exports/metrics.json' . PHP_EOL;
"

echo "Architecture data export pipeline complete!"
```

### 2. Data Validation and Quality Checks

```php
<?php

// Validate exported PHP data integrity
class DataValidator
{
    public static function validate(string $dataFile): array
    {
        $issues = [];
        
        if (!file_exists($dataFile)) {
            $issues[] = "Data file does not exist: {$dataFile}";
            return $issues;
        }
        
        $data = include $dataFile;
        
        if (!is_array($data)) {
            $issues[] = "Data file does not contain valid array";
            return $issues;
        }
        
        // Check required structure
        if (!isset($data['atlas_version'])) {
            $issues[] = "Missing atlas_version";
        }
        
        if (!isset($data['data'])) {
            $issues[] = "Missing data section";
        }
        
        // Validate each component type
        foreach ($data['data'] ?? [] as $type => $componentData) {
            if (!isset($componentData['type'])) {
                $issues[] = "Missing type for component: {$type}";
            }
            
            if (!isset($componentData['data'])) {
                $issues[] = "Missing data for component: {$type}";
            }
            
            // Validate individual components
            foreach ($componentData['data'] ?? [] as $index => $component) {
                if (!isset($component['name'])) {
                    $issues[] = "Missing name for {$type} component at index {$index}";
                }
                
                if (!isset($component['namespace'])) {
                    $issues[] = "Missing namespace for {$type} component: " . ($component['name'] ?? 'unnamed');
                }
            }
        }
        
        return $issues;
    }
}

// Validate all exported files
$exportFiles = [
    'exports/php/models-data.php',
    'exports/php/controllers-data.php',
    'exports/php/routes-data.php',
    'exports/php/complete-atlas.php',
];

$allIssues = [];
foreach ($exportFiles as $file) {
    $issues = DataValidator::validate($file);
    if (!empty($issues)) {
        $allIssues[$file] = $issues;
    }
}

if (empty($allIssues)) {
    echo "✓ All exported PHP data files are valid\n";
} else {
    echo "❌ Data validation issues found:\n";
    foreach ($allIssues as $file => $issues) {
        echo "File: {$file}\n";
        foreach ($issues as $issue) {
            echo "  - {$issue}\n";
        }
    }
}
```

## 🔗 Related Examples

- [JSON Exports](json.md) - Working with JSON data
- [HTML Reports](html.md) - Interactive visualizations  
- [Advanced Analysis](../advanced/custom-analysis.md) - Custom data analysis

---

**Need help?** Check our [documentation](../../docs/) or open an issue on GitHub.