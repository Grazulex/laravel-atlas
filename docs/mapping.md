# Architecture Mapping

This document covers the architectural mapping capabilities of Laravel Atlas.

## 🗺️ Overview

Laravel Atlas provides comprehensive architecture mapping by analyzing your Laravel application's components and their relationships. It uses specialized mappers to scan different parts of your application and generate detailed architectural documentation.

## 🔍 Component Analysis

### What Gets Mapped

Laravel Atlas analyzes **17 different component types**:

- **Models** - Eloquent models with relationships, observers, and factories
- **Routes** - Application routes with middleware and controllers
- **Jobs** - Queued jobs and their properties
- **Services** - Service classes and their dependencies
- **Controllers** - Controllers and their methods
- **Events** - Application events and listeners
- **Commands** - Artisan commands
- **Middleware** - HTTP middleware
- **Policies** - Authorization policies
- **Resources** - API resources
- **Notifications** - Notification classes
- **Requests** - Form request classes
- **Rules** - Custom validation rules
- **Observers** - Eloquent model observers
- **Listeners** - Event listeners
- **Actions** - Action classes

### Relationship Discovery

Atlas automatically discovers and maps relationships between components:

```php
// Example: Model relationships discovered
$modelData = Atlas::scan('models');

foreach ($modelData['data'] as $model) {
    echo "Model: {$model['name']}\n";
    
    if (isset($model['relationships'])) {
        foreach ($model['relationships'] as $relationship) {
            echo "  - {$relationship['type']}: {$relationship['related']}\n";
        }
    }
}
```

## 🏗️ Architecture Patterns

### Layered Architecture Analysis

Atlas can identify and map common Laravel architecture patterns:

```php
// Analyze layered architecture
$layers = [
    'presentation' => Atlas::scan('controllers'),
    'application' => Atlas::scan('services'),
    'domain' => Atlas::scan('models'),
    'infrastructure' => [
        'jobs' => Atlas::scan('jobs'),
        'events' => Atlas::scan('events'),
        'listeners' => Atlas::scan('listeners'),
    ],
];

// Generate layered architecture documentation
foreach ($layers as $layer => $components) {
    echo "=== {$layer} Layer ===\n";
    // Process layer components...
}
```

### Domain-Driven Design (DDD)

For DDD architectures, Atlas can map bounded contexts:

```php
// Map DDD bounded contexts
$boundedContexts = Atlas::scan('services', [
    'detect_patterns' => true,
    'group_by_namespace' => true,
    'scan_paths' => [
        app_path('Domain/User'),
        app_path('Domain/Order'),
        app_path('Domain/Payment'),
    ],
]);
```

### Event-Driven Architecture

Analyze event flows and listener patterns:

```php
// Map event-driven architecture
$eventArchitecture = [
    'events' => Atlas::scan('events'),
    'listeners' => Atlas::scan('listeners'),
    'observers' => Atlas::scan('observers'),
];

// Build event flow diagram
$eventFlows = [];
foreach ($eventArchitecture['listeners']['data'] as $listener) {
    foreach ($listener['handled_events'] ?? [] as $event) {
        $eventFlows[$event][] = $listener['name'];
    }
}
```

## 📊 Dependency Analysis

### Component Dependencies

Atlas tracks dependencies between components:

```php
// Analyze component dependencies
$dependencyMap = [];

$components = ['models', 'controllers', 'services'];
foreach ($components as $type) {
    $data = Atlas::scan($type, ['include_dependencies' => true]);
    
    foreach ($data['data'] as $component) {
        if (isset($component['dependencies'])) {
            $dependencyMap[$component['name']] = $component['dependencies'];
        }
    }
}

// Find circular dependencies
$circularDeps = findCircularDependencies($dependencyMap);
```

### Coupling Analysis

Measure coupling between components:

```php
class CouplingAnalyzer
{
    public function analyzeAfferentCoupling($componentData)
    {
        $afferentCoupling = [];
        
        foreach ($componentData as $type => $components) {
            foreach ($components['data'] as $component) {
                $incoming = 0;
                
                // Count incoming dependencies
                foreach ($componentData as $otherType => $otherComponents) {
                    foreach ($otherComponents['data'] as $otherComponent) {
                        if (isset($otherComponent['dependencies'])) {
                            if (in_array($component['name'], $otherComponent['dependencies'])) {
                                $incoming++;
                            }
                        }
                    }
                }
                
                $afferentCoupling[$component['name']] = $incoming;
            }
        }
        
        return $afferentCoupling;
    }
    
    public function analyzeEfferentCoupling($componentData)
    {
        $efferentCoupling = [];
        
        foreach ($componentData as $type => $components) {
            foreach ($components['data'] as $component) {
                $outgoing = count($component['dependencies'] ?? []);
                $efferentCoupling[$component['name']] = $outgoing;
            }
        }
        
        return $efferentCoupling;
    }
}

$analyzer = new CouplingAnalyzer();
$afferent = $analyzer->analyzeAfferentCoupling($allComponents);
$efferent = $analyzer->analyzeEfferentCoupling($allComponents);
```

## 🔄 Data Flow Mapping

### Request-Response Flow

Map how requests flow through your application:

```php
// Map request flow from routes to models
$requestFlow = [
    'entry_points' => Atlas::scan('routes'),
    'controllers' => Atlas::scan('controllers'),
    'services' => Atlas::scan('services'),
    'models' => Atlas::scan('models'),
];

// Build flow connections
function buildRequestFlow($flowData) {
    $connections = [];
    
    foreach ($flowData['entry_points']['data'] as $route) {
        if (isset($route['controller'])) {
            $connections[] = [
                'from' => $route['uri'],
                'to' => $route['controller'],
                'type' => 'route-to-controller',
            ];
        }
    }
    
    // Add more connections...
    return $connections;
}
```

### Event Flow Analysis

Track event propagation through the system:

```php
// Map event flows
$eventFlow = [
    'triggers' => [], // What triggers events
    'events' => Atlas::scan('events'),
    'listeners' => Atlas::scan('listeners'),
    'effects' => [], // What listeners do
];

foreach ($eventFlow['listeners']['data'] as $listener) {
    foreach ($listener['handled_events'] ?? [] as $eventName) {
        $eventFlow['connections'][] = [
            'event' => $eventName,
            'listener' => $listener['name'],
            'queued' => $listener['is_queued'] ?? false,
        ];
    }
}
```

## 📈 Architecture Metrics

### Complexity Metrics

Calculate architectural complexity:

```php
class ArchitecturalMetrics
{
    public function calculateComplexity($componentData)
    {
        $metrics = [];
        
        foreach ($componentData as $type => $components) {
            $typeMetrics = [
                'component_count' => count($components['data']),
                'avg_dependencies' => 0,
                'max_dependencies' => 0,
                'complexity_score' => 0,
            ];
            
            $totalDeps = 0;
            foreach ($components['data'] as $component) {
                $depCount = count($component['dependencies'] ?? []);
                $totalDeps += $depCount;
                $typeMetrics['max_dependencies'] = max($typeMetrics['max_dependencies'], $depCount);
            }
            
            if ($typeMetrics['component_count'] > 0) {
                $typeMetrics['avg_dependencies'] = $totalDeps / $typeMetrics['component_count'];
                $typeMetrics['complexity_score'] = $this->calculateComplexityScore($typeMetrics);
            }
            
            $metrics[$type] = $typeMetrics;
        }
        
        return $metrics;
    }
    
    private function calculateComplexityScore($typeMetrics)
    {
        // Simplified complexity calculation
        return ($typeMetrics['component_count'] * 0.1) + 
               ($typeMetrics['avg_dependencies'] * 2) + 
               ($typeMetrics['max_dependencies'] * 0.5);
    }
}
```

### Quality Metrics

Assess architectural quality:

```php
class ArchitecturalQuality
{
    public function assessQuality($componentData)
    {
        $qualityMetrics = [
            'cohesion' => $this->measureCohesion($componentData),
            'coupling' => $this->measureCoupling($componentData),
            'maintainability' => $this->measureMaintainability($componentData),
            'testability' => $this->measureTestability($componentData),
        ];
        
        return $qualityMetrics;
    }
    
    private function measureCohesion($componentData)
    {
        // Measure how closely related components within a module are
        $cohesionScores = [];
        
        foreach ($componentData as $type => $components) {
            $internalRefs = 0;
            $totalRefs = 0;
            
            foreach ($components['data'] as $component) {
                foreach ($component['dependencies'] ?? [] as $dependency) {
                    $totalRefs++;
                    
                    // Check if dependency is within same namespace/module
                    if (strpos($dependency, $component['namespace']) === 0) {
                        $internalRefs++;
                    }
                }
            }
            
            $cohesionScores[$type] = $totalRefs > 0 ? $internalRefs / $totalRefs : 0;
        }
        
        return $cohesionScores;
    }
    
    private function measureCoupling($componentData)
    {
        // Measure dependencies between different modules
        $couplingScores = [];
        
        foreach ($componentData as $type => $components) {
            $externalDeps = 0;
            $totalDeps = 0;
            
            foreach ($components['data'] as $component) {
                foreach ($component['dependencies'] ?? [] as $dependency) {
                    $totalDeps++;
                    
                    // Check if dependency is external
                    if (strpos($dependency, $component['namespace']) !== 0) {
                        $externalDeps++;
                    }
                }
            }
            
            $couplingScores[$type] = $totalDeps > 0 ? $externalDeps / $totalDeps : 0;
        }
        
        return $couplingScores;
    }
}
```

## 📋 Architecture Documentation Generation

### Automated Documentation

Generate comprehensive architecture documentation:

```bash
#!/bin/bash
# generate-architecture-docs.sh

echo "Generating comprehensive architecture documentation..."

# Create documentation structure
mkdir -p docs/architecture/{components,flows,metrics}

# Generate component documentation
components=("models" "controllers" "services" "events" "listeners")
for component in "${components[@]}"; do
    php artisan atlas:generate --type=${component} --format=markdown --output=docs/architecture/components/${component}.md
done

# Generate flow documentation
php artisan atlas:generate --type=routes --format=markdown --output=docs/architecture/flows/request-flow.md

# Generate architecture overview
php artisan atlas:generate --type=all --format=html --output=docs/architecture/overview.html

echo "Architecture documentation generated!"
```

### Custom Documentation Templates

Create custom templates for different audiences:

```php
class ArchitectureDocumentationGenerator
{
    public function generateExecutiveSummary($componentData)
    {
        $summary = "# Executive Architecture Summary\n\n";
        
        $totalComponents = 0;
        foreach ($componentData as $type => $components) {
            $count = count($components['data']);
            $totalComponents += $count;
            $summary .= "- **{$type}**: {$count} components\n";
        }
        
        $summary .= "\n**Total Components**: {$totalComponents}\n\n";
        
        // Add complexity assessment
        $summary .= $this->generateComplexityAssessment($componentData);
        
        return $summary;
    }
    
    public function generateDeveloperGuide($componentData)
    {
        $guide = "# Developer Architecture Guide\n\n";
        
        foreach ($componentData as $type => $components) {
            $guide .= "## {$type}\n\n";
            
            foreach ($components['data'] as $component) {
                $guide .= "### {$component['name']}\n";
                $guide .= "**Location**: {$component['path']}\n";
                
                if (isset($component['dependencies'])) {
                    $guide .= "**Dependencies**:\n";
                    foreach ($component['dependencies'] as $dep) {
                        $guide .= "- {$dep}\n";
                    }
                }
                
                $guide .= "\n";
            }
        }
        
        return $guide;
    }
}
```

## 🔧 Advanced Mapping Techniques

### Custom Mappers

Create custom mappers for specific architectural patterns:

```php
use LaravelAtlas\Mappers\BaseMapper;

class RepositoryMapper extends BaseMapper
{
    protected string $type = 'repositories';
    
    public function scan(array $options = []): array
    {
        $scanPath = $options['scan_path'] ?? app_path('Repositories');
        
        $repositories = [];
        
        foreach ($this->getFiles($scanPath, '*.php') as $file) {
            if ($this->isRepository($file)) {
                $repositories[] = $this->analyzeRepository($file);
            }
        }
        
        return [
            'type' => $this->type,
            'scan_path' => $scanPath,
            'data' => $repositories,
        ];
    }
    
    private function isRepository($file): bool
    {
        return str_ends_with(basename($file, '.php'), 'Repository');
    }
    
    private function analyzeRepository($file): array
    {
        // Analyze repository implementation
        return [
            'name' => $this->getClassName($file),
            'path' => $file,
            'interface' => $this->getImplementedInterface($file),
            'model' => $this->getAssociatedModel($file),
        ];
    }
}
```

### Integration with External Tools

Export architecture data for external analysis tools:

```php
// Export to PlantUML
class PlantUMLExporter
{
    public function export($componentData): string
    {
        $plantuml = "@startuml\n";
        $plantuml .= "title Application Architecture\n\n";
        
        foreach ($componentData as $type => $components) {
            $plantuml .= "package \"{$type}\" {\n";
            
            foreach ($components['data'] as $component) {
                $plantuml .= "  class {$component['name']}\n";
            }
            
            $plantuml .= "}\n\n";
        }
        
        // Add relationships
        foreach ($componentData as $type => $components) {
            foreach ($components['data'] as $component) {
                foreach ($component['dependencies'] ?? [] as $dependency) {
                    $depName = basename($dependency);
                    $plantuml .= "{$component['name']} --> {$depName}\n";
                }
            }
        }
        
        $plantuml .= "@enduml\n";
        
        return $plantuml;
    }
}
```

## 🔗 Related Documentation

- [Mappers](mappers.md) - Available component mappers
- [Exporters](exporters.md) - Available export formats  
- [Usage Guide](usage.md) - Basic usage instructions
- [Examples](../examples/README.md) - Practical examples

---

For more specific mapping examples and techniques, check the [examples directory](../examples/).