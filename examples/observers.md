# Observer Analysis Examples

These examples demonstrate how to use Laravel Atlas to analyze Eloquent model observers.

## 📋 Prerequisites

- Laravel Atlas installed: `composer require grazulex/laravel-atlas --dev`
- Laravel application with model observers

## 🔍 Basic Observer Analysis

### 1. Scan All Observers

```bash
# Generate basic observer analysis
php artisan atlas:generate --type=observers

# Save to JSON file
php artisan atlas:generate --type=observers --format=json --output=docs/observers.json

# Generate detailed markdown documentation
php artisan atlas:generate --type=observers --format=markdown --output=docs/observers.md
```

### 2. Programmatic Observer Analysis

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Basic observer scanning
$observerData = Atlas::scan('observers');

echo "Found " . count($observerData['data']) . " observers\n";

// Detailed observer analysis with options
$detailedObservers = Atlas::scan('observers', [
    'include_observed_models' => true,
    'include_event_methods' => true,
    'analyze_dependencies' => true,
]);

foreach ($detailedObservers['data'] as $observer) {
    echo "Observer: {$observer['name']}\n";
    echo "Path: {$observer['path']}\n";
    
    if (isset($observer['observed_models'])) {
        echo "Observes:\n";
        foreach ($observer['observed_models'] as $model) {
            echo "  - {$model}\n";
        }
    }
    
    if (isset($observer['event_methods'])) {
        echo "Event Methods:\n";
        foreach ($observer['event_methods'] as $method) {
            echo "  - {$method}\n";
        }
    }
    
    echo "\n";
}
```

## 📊 Export Examples

### 1. Generate Observer Documentation

```bash
# Create comprehensive observer documentation
php artisan atlas:generate --type=observers --format=markdown --output=docs/OBSERVERS.md

# Generate visual observer diagram
php artisan atlas:generate --type=observers --format=image --output=diagrams/observers.png

# Create HTML report with intelligent workflow
php artisan atlas:generate --type=observers --format=html --output=public/observers.html
```

### 2. Observer Data Processing

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Export observer data for analysis
$observerJson = Atlas::export('observers', 'json');
file_put_contents('storage/observers-data.json', $observerJson);

// Generate PHP data for custom processing
$observerPhp = Atlas::export('observers', 'php');
file_put_contents('storage/observers-data.php', $observerPhp);

// Include the generated data
$observerData = include 'storage/observers-data.php';

// Process observer relationships
foreach ($observerData['data']['observers']['data'] as $observer) {
    if (isset($observer['observed_models'])) {
        echo "{$observer['name']} observes:\n";
        foreach ($observer['observed_models'] as $model) {
            echo "  - {$model}\n";
        }
    }
}
```

## 🎯 Observer Architecture Analysis

### 1. Observer-Model Relationships

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Analyze observer patterns
$observers = Atlas::scan('observers', [
    'include_observed_models' => true,
    'include_event_methods' => true,
]);

$models = Atlas::scan('models', [
    'include_observers' => true,
]);

// Cross-reference observers with models
$observerModelMap = [];
foreach ($observers['data'] as $observer) {
    foreach ($observer['observed_models'] ?? [] as $model) {
        $observerModelMap[$model][] = $observer['name'];
    }
}

echo "Observer-Model Relationships:\n";
foreach ($observerModelMap as $model => $observerList) {
    echo "{$model} is observed by: " . implode(', ', $observerList) . "\n";
}
```

### 2. Observer Event Coverage Analysis

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Analyze which events are covered by observers
$observerData = Atlas::scan('observers', [
    'include_event_methods' => true,
]);

$eventCoverage = [];
$eloquentEvents = ['creating', 'created', 'updating', 'updated', 'saving', 'saved', 'deleting', 'deleted', 'restoring', 'restored'];

foreach ($observerData['data'] as $observer) {
    foreach ($observer['event_methods'] ?? [] as $method) {
        if (in_array($method, $eloquentEvents)) {
            $eventCoverage[$method][] = $observer['name'];
        }
    }
}

echo "Event Coverage Analysis:\n";
foreach ($eloquentEvents as $event) {
    $observers = $eventCoverage[$event] ?? [];
    $count = count($observers);
    echo "{$event}: {$count} observers (" . implode(', ', array_slice($observers, 0, 3)) . ")\n";
}
```

## 🔧 Advanced Observer Analysis

### 1. Observer Dependency Analysis

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Analyze observer dependencies
$observerData = Atlas::scan('observers', [
    'analyze_dependencies' => true,
]);

echo "Observer Dependencies:\n";
foreach ($observerData['data'] as $observer) {
    if (isset($observer['dependencies']) && !empty($observer['dependencies'])) {
        echo "{$observer['name']}:\n";
        foreach ($observer['dependencies'] as $dependency) {
            echo "  - {$dependency}\n";
        }
    }
}
```

### 2. Combined Analysis with Models

```bash
# Generate combined model and observer analysis
php artisan atlas:generate --type=models --format=json --output=/tmp/models.json
php artisan atlas:generate --type=observers --format=json --output=/tmp/observers.json

# Process both files together for comprehensive analysis
```

```php
<?php

// Load both datasets
$modelData = json_decode(file_get_contents('/tmp/models.json'), true);
$observerData = json_decode(file_get_contents('/tmp/observers.json'), true);

// Generate comprehensive report
$report = "# Model-Observer Architecture Report\n\n";

// Process relationships
$report .= "## Observer Coverage\n\n";
foreach ($modelData['data']['models']['data'] as $model) {
    $report .= "### {$model['name']}\n";
    
    if (isset($model['observers']) && !empty($model['observers'])) {
        $report .= "**Observers:** " . implode(', ', $model['observers']) . "\n\n";
    } else {
        $report .= "**No observers found**\n\n";
    }
}

file_put_contents('docs/MODEL-OBSERVER-ANALYSIS.md', $report);
echo "Combined analysis saved to docs/MODEL-OBSERVER-ANALYSIS.md\n";
```

## 💡 Best Practices

### 1. Regular Observer Audits

```bash
# Create weekly observer audit reports
php artisan atlas:generate --type=observers --format=markdown --output=reports/observers-$(date +%Y%m%d).md
```

### 2. Observer Pattern Validation

```php
<?php

// Validate observer patterns
$observerData = Atlas::scan('observers');
$modelData = Atlas::scan('models');

// Check for models without observers that might need them
foreach ($modelData['data'] as $model) {
    if (empty($model['observers']) && in_array($model['name'], ['User', 'Order', 'Payment'])) {
        echo "Warning: {$model['name']} model has no observers\n";
    }
}
```

## 🔗 Related Examples

- [Model Analysis](models.md) - Analyzing models that observers watch
- [Listener Analysis](listeners.md) - Event listeners vs model observers
- [HTML Reports](exports/html.md) - Interactive observer visualization

---

**Need help?** Check our [documentation](../docs/) or open an issue on GitHub.