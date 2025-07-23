# Basic Usage Examples

This directory contains basic examples to get you started with Laravel Atlas.

## 📋 Examples in this Directory

- `01-simple-scan.php` - Basic component scanning
- `02-model-analysis.php` - Model relationship analysis
- `03-route-mapping.php` - Route structure mapping
- `04-command-line.md` - Command-line usage examples
- `sample-outputs/` - Sample outputs for reference

## 🚀 Getting Started

### Prerequisites

Make sure Laravel Atlas is installed in your Laravel project:

```bash
composer require grazulex/laravel-atlas
```

### Basic Command Usage

```bash
# Generate a complete application map (JSON format)
php artisan atlas:generate

# Generate model map in markdown format
php artisan atlas:generate --type=models --format=markdown

# Generate route map and save to file
php artisan atlas:generate --type=routes --format=json --output=storage/routes-map.json

# Generate interactive HTML report
php artisan atlas:generate --type=all --format=html --output=public/atlas-report.html
```

### Basic Programmatic Usage

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Scan all models in your application
$models = Atlas::scan('models');

// Scan routes with middleware information
$routes = Atlas::scan('routes', [
    'include_middleware' => true,
    'include_controllers' => true
]);

// Export models to JSON
$jsonOutput = Atlas::export('models', 'json');
file_put_contents('storage/models.json', $jsonOutput);

// Export routes to markdown documentation
$markdownDocs = Atlas::export('routes', 'markdown');
file_put_contents('docs/routes.md', $markdownDocs);
```

## 📁 Example Files

### 01-simple-scan.php

Basic scanning example showing how to get started with Atlas:

```php
<?php

require_once 'vendor/autoload.php';

use LaravelAtlas\Facades\Atlas;

echo "🔍 Laravel Atlas - Simple Scan Example\n\n";

// Scan all models
echo "📊 Scanning Models...\n";
$models = Atlas::scan('models');
echo "Found " . count($models['data']) . " models\n\n";

// Scan all routes  
echo "🛣️ Scanning Routes...\n";
$routes = Atlas::scan('routes');
echo "Found " . count($routes['data']) . " routes\n\n";

// Export to JSON
echo "💾 Exporting to JSON...\n";
$json = Atlas::export('models', 'json');
file_put_contents('storage/atlas-models.json', $json);
echo "✅ Models exported to storage/atlas-models.json\n";
```

### 02-model-analysis.php

Advanced model analysis with relationships:

```php
<?php

use LaravelAtlas\Facades\Atlas;

echo "🧱 Laravel Atlas - Model Analysis Example\n\n";

// Scan models with detailed relationship information
$models = Atlas::scan('models', [
    'include_relationships' => true,
    'include_observers' => true,
    'include_scopes' => true,
    'include_casts' => true
]);

echo "📊 Analysis Results:\n";
echo "- Models found: " . count($models['data']) . "\n";

foreach ($models['data'] as $model) {
    echo "\n🏷️ Model: " . $model['class'] . "\n";
    echo "  📋 Table: " . $model['table'] . "\n";
    echo "  🔑 Primary Key: " . $model['primary_key'] . "\n";
    echo "  📝 Fillable: " . implode(', ', $model['fillable']) . "\n";
    
    if (!empty($model['relationships'])) {
        echo "  🔗 Relationships: " . count($model['relationships']) . "\n";
        foreach ($model['relationships'] as $name => $rel) {
            echo "    - {$name}: {$rel['type']} -> {$rel['related']}\n";
        }
    }
    
    if (!empty($model['scopes'])) {
        echo "  🎯 Scopes: " . implode(', ', array_column($model['scopes'], 'name')) . "\n";
    }
}

// Export detailed analysis to markdown
$markdown = Atlas::export('models', 'markdown');
file_put_contents('docs/model-analysis.md', $markdown);
echo "\n✅ Detailed analysis exported to docs/model-analysis.md\n";
```

### 03-route-mapping.php

Route structure analysis:

```php
<?php

use LaravelAtlas\Facades\Atlas;

echo "🛣️ Laravel Atlas - Route Mapping Example\n\n";

// Scan routes with comprehensive information
$routes = Atlas::scan('routes', [
    'include_middleware' => true,
    'include_controllers' => true,
    'include_parameters' => true,
    'group_by_prefix' => true
]);

echo "📊 Route Analysis Results:\n";
echo "- Total routes: " . count($routes['data']) . "\n";

$methodCounts = [];
$middlewareCounts = [];

foreach ($routes['data'] as $route) {
    // Count HTTP methods
    foreach ($route['methods'] as $method) {
        $methodCounts[$method] = ($methodCounts[$method] ?? 0) + 1;
    }
    
    // Count middleware usage
    foreach ($route['middleware'] ?? [] as $middleware) {
        $middlewareCounts[$middleware] = ($middlewareCounts[$middleware] ?? 0) + 1;
    }
}

echo "\n📈 Method Distribution:\n";
foreach ($methodCounts as $method => $count) {
    echo "  - {$method}: {$count} routes\n";
}

echo "\n🛡️ Most Used Middleware:\n";
arsort($middlewareCounts);
foreach (array_slice($middlewareCounts, 0, 5, true) as $middleware => $count) {
    echo "  - {$middleware}: {$count} routes\n";
}

// Export route map to different formats
$htmlReport = Atlas::export('routes', 'html');
file_put_contents('public/route-map.html', $htmlReport);

$jsonData = Atlas::export('routes', 'json');
file_put_contents('storage/route-map.json', $jsonData);

echo "\n✅ Route maps exported:\n";
echo "  - HTML: public/route-map.html\n";
echo "  - JSON: storage/route-map.json\n";
```

## 🎯 Expected Outcomes

After running these examples, you should have:

1. **Understanding** of basic Atlas scanning capabilities
2. **JSON/Markdown files** with your application's architecture
3. **Interactive HTML reports** for visual exploration
4. **Knowledge** of programmatic API usage

## 🔍 Sample Outputs

Check the `sample-outputs/` directory for example outputs from a typical Laravel application.

## ⚡ Next Steps

Once you're comfortable with basic usage:

1. Explore [Advanced Scanning](../advanced-scanning/) examples
2. Learn about different [Export Formats](../export-formats/)
3. Set up [CI/CD Integration](../integration/)

## 💡 Tips

- Start with small scans on specific component types
- Use JSON exports for further data processing
- HTML exports are great for team reviews
- Markdown exports integrate well with documentation workflows

---

**Questions?** Check the [main documentation](../../README.md) or [open an issue](https://github.com/Grazulex/laravel-atlas/issues).