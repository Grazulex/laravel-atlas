# API Reference

Complete API reference for Laravel Atlas programmatic usage.

## 📚 Table of Contents

- [Atlas Facade](#atlas-facade)
- [AtlasManager](#atlasmanager)
- [Scanning Methods](#scanning-methods)
- [Export Methods](#export-methods)
- [Configuration Methods](#configuration-methods)
- [Cache Methods](#cache-methods)
- [Helper Methods](#helper-methods)

## 🎯 Atlas Facade

The primary interface for Laravel Atlas functionality.

### Basic Usage

```php
use LaravelAtlas\Facades\Atlas;

// Scan all models
$models = Atlas::scan('models');

// Export to JSON
$json = Atlas::export('models', 'json');
```

## 🔍 Scanning Methods

### `scan(string $type, array $options = [])`

Analyze specific component types in your Laravel application.

**Parameters:**
- `$type` (string): Component type to scan
- `$options` (array): Analysis options

**Component Types:**
- `models` - Eloquent models
- `routes` - Application routes  
- `services` - Service classes
- `commands` - Artisan commands
- `jobs` - Queue jobs
- `events` - Event classes
- `listeners` - Event listeners
- `controllers` - Controller classes
- `middleware` - Middleware classes
- `all` - All components

**Returns:** Array with analysis results

#### Model Scanning

```php
use LaravelAtlas\Facades\Atlas;

// Basic model scan
$models = Atlas::scan('models');

// Detailed model analysis
$models = Atlas::scan('models', [
    'include_relationships' => true,
    'include_scopes' => true,
    'include_observers' => true,
    'include_casts' => true,
    'include_fillable' => true,
    'include_hidden' => true,
]);

// Filter specific models
$models = Atlas::scan('models', [
    'only' => ['User', 'Post', 'Comment'],
    'include_relationships' => true,
]);

// Exclude specific models
$models = Atlas::scan('models', [
    'except' => ['TemporaryModel', 'TestModel'],
]);
```

#### Route Scanning

```php
// Basic route scan
$routes = Atlas::scan('routes');

// Detailed route analysis
$routes = Atlas::scan('routes', [
    'include_middleware' => true,
    'include_controllers' => true,
    'include_parameters' => true,
    'include_names' => true,
    'group_by_prefix' => true,
]);

// Filter by HTTP methods
$routes = Atlas::scan('routes', [
    'methods' => ['GET', 'POST'],
    'include_middleware' => true,
]);

// Filter by prefix
$routes = Atlas::scan('routes', [
    'prefixes' => ['api', 'admin'],
]);
```

#### Service Scanning

```php
// Basic service scan
$services = Atlas::scan('services');

// Detailed service analysis
$services = Atlas::scan('services', [
    'include_dependencies' => true,
    'include_methods' => true,
    'include_interfaces' => true,
    'include_traits' => true,
]);

// Custom service patterns
$services = Atlas::scan('services', [
    'patterns' => [
        'App\\Services\\*',
        'App\\Repositories\\*',
        'App\\Actions\\*',
    ],
]);
```

#### Command Scanning

```php
// Basic command scan
$commands = Atlas::scan('commands');

// Detailed command analysis
$commands = Atlas::scan('commands', [
    'include_signature' => true,
    'include_description' => true,
    'include_flow' => true,
    'include_dependencies' => true,
]);
```

### `scanAll(array $options = [])`

Scan all component types at once.

```php
// Scan everything
$all = Atlas::scanAll();

// Scan with global options
$all = Atlas::scanAll([
    'max_depth' => 5,
    'include_vendors' => false,
    'cache' => true,
]);

// Selective scanning
$all = Atlas::scanAll([
    'components' => ['models', 'routes', 'services'],
    'detailed' => true,
]);
```

## 📤 Export Methods

### `export(string $type, string $format, array $options = [])`

Export analysis results in various formats.

**Parameters:**
- `$type` (string): Component type or 'all'
- `$format` (string): Export format
- `$options` (array): Export options

**Export Formats:**
- `json` - JSON data
- `markdown` - Markdown documentation
- `html` - Interactive HTML report
- `pdf` - PDF report
- `image` - Visual diagram
- `php` - PHP array

#### JSON Export

```php
// Basic JSON export
$json = Atlas::export('models', 'json');

// Pretty-printed JSON
$json = Atlas::export('models', 'json', [
    'pretty' => true,
    'include_metadata' => true,
]);

// Minified JSON
$json = Atlas::export('models', 'json', [
    'minify' => true,
]);
```

#### Markdown Export

```php
// Basic markdown export
$markdown = Atlas::export('models', 'markdown');

// Markdown with table of contents
$markdown = Atlas::export('models', 'markdown', [
    'include_toc' => true,
    'detailed_sections' => true,
]);

// Custom template
$markdown = Atlas::export('models', 'markdown', [
    'template' => 'custom',
    'include_diagrams' => true,
]);
```

#### HTML Export

```php
// Basic HTML export
$html = Atlas::export('models', 'html');

// Interactive HTML with search
$html = Atlas::export('models', 'html', [
    'theme' => 'modern',
    'searchable' => true,
    'responsive' => true,
]);

// Custom styling
$html = Atlas::export('models', 'html', [
    'theme' => 'dark',
    'include_css' => true,
    'include_js' => true,
]);
```

#### PDF Export

```php
// Basic PDF export
$pdf = Atlas::export('models', 'pdf');

// Professional PDF report
$pdf = Atlas::export('models', 'pdf', [
    'template' => 'professional',
    'page_size' => 'A4',
    'orientation' => 'portrait',
    'include_cover' => true,
]);

// Custom margins
$pdf = Atlas::export('models', 'pdf', [
    'margins' => [
        'top' => 20,
        'right' => 20,
        'bottom' => 20,
        'left' => 20,
    ],
]);
```

#### Image Export

```php
// Basic image export
$image = Atlas::export('models', 'image');

// High-resolution PNG
$image = Atlas::export('models', 'image', [
    'type' => 'png',
    'width' => 1920,
    'height' => 1080,
    'dpi' => 300,
]);

// SVG diagram
$image = Atlas::export('models', 'image', [
    'type' => 'svg',
    'scalable' => true,
]);
```

### `exportToFile(string $type, string $format, string $path, array $options = [])`

Export directly to a file.

```php
// Export to specific file
Atlas::exportToFile('models', 'json', 'storage/models.json');

// Export with options
Atlas::exportToFile('models', 'html', 'public/models.html', [
    'theme' => 'modern',
    'searchable' => true,
]);

// Create directory if not exists
Atlas::exportToFile('models', 'pdf', 'reports/models.pdf', [
    'create_directory' => true,
]);
```

## ⚙️ Configuration Methods

### `configure(array $config)`

Set runtime configuration options.

```php
// Set global configuration
Atlas::configure([
    'max_depth' => 10,
    'include_vendors' => false,
    'cache_enabled' => true,
]);

// Component-specific configuration
Atlas::configure([
    'components' => [
        'models' => [
            'include_relationships' => true,
            'include_scopes' => true,
        ],
        'routes' => [
            'include_middleware' => true,
        ],
    ],
]);
```

### `getConfig(string $key = null)`

Get configuration values.

```php
// Get all configuration
$config = Atlas::getConfig();

// Get specific key
$maxDepth = Atlas::getConfig('analysis.max_depth');

// Get with default value
$cacheEnabled = Atlas::getConfig('cache.enabled', true);
```

## 🗄️ Cache Methods

### `cache(string $key, $value = null, int $ttl = null)`

Manage Atlas cache.

```php
// Get cached value
$cached = Atlas::cache('models.scan.result');

// Set cached value
Atlas::cache('models.scan.result', $data, 3600);

// Check if cached
$exists = Atlas::cache()->has('models.scan.result');
```

### `clearCache(string $pattern = null)`

Clear cached data.

```php
// Clear all Atlas cache
Atlas::clearCache();

// Clear specific pattern
Atlas::clearCache('models.*');

// Clear component cache
Atlas::clearCache('routes.scan.*');
```

## 🛠️ Helper Methods

### `validate(string $type = null)`

Validate Atlas configuration and setup.

```php
// Validate all configuration
$validation = Atlas::validate();

// Validate specific component
$validation = Atlas::validate('models');

// Check validation result
if ($validation['valid']) {
    echo "Configuration is valid";
} else {
    foreach ($validation['errors'] as $error) {
        echo "Error: {$error}\n";
    }
}
```

### `status()`

Get Atlas status information.

```php
$status = Atlas::status();

// Status includes:
// - enabled: bool
// - components: array
// - cache_status: array
// - performance: array
// - last_scan: timestamp
```

### `version()`

Get Atlas version information.

```php
$version = Atlas::version();
// Returns: "1.0.0"
```

## 🏗️ AtlasManager

Access the underlying AtlasManager for advanced usage.

```php
use LaravelAtlas\AtlasManager;

$manager = app(AtlasManager::class);

// Advanced scanning with custom configuration
$result = $manager->scanWithConfig('models', [
    'custom_option' => 'value',
]);

// Direct access to mappers
$modelMapper = $manager->getMapper('models');
$routes = $modelMapper->map();

// Custom export pipeline
$exporter = $manager->getExporter('html');
$html = $exporter->export($data, [
    'template' => 'custom',
]);
```

## 🔌 Custom Extensions

### Custom Mappers

```php
use LaravelAtlas\Contracts\MapperInterface;

class CustomMapper implements MapperInterface
{
    public function map(array $options = []): array
    {
        // Custom mapping logic
        return [
            'type' => 'custom',
            'data' => $this->scanCustomComponents($options),
        ];
    }
}

// Register custom mapper
Atlas::extend('custom', CustomMapper::class);

// Use custom mapper
$data = Atlas::scan('custom');
```

### Custom Exporters

```php
use LaravelAtlas\Contracts\ExporterInterface;

class CustomExporter implements ExporterInterface
{
    public function export(array $data, array $options = []): string
    {
        // Custom export logic
        return $this->generateCustomFormat($data, $options);
    }
}

// Register custom exporter
Atlas::exportFormat('xml', CustomExporter::class);

// Use custom exporter
$xml = Atlas::export('models', 'xml');
```

## 📊 Response Format

### Standard Response Structure

```php
// All scan methods return this structure
[
    'type' => 'models',           // Component type
    'timestamp' => '2024-07-23T03:00:00Z',
    'generated_by' => 'Laravel Atlas',
    'application' => [
        'name' => 'Your Laravel App',
        'version' => '1.0.0',
        'environment' => 'local',
    ],
    'summary' => [               // Summary statistics
        'total_models' => 5,
        'total_relationships' => 8,
    ],
    'data' => [                  // Actual component data
        // ... component-specific data
    ],
    'metadata' => [              // Additional metadata
        'scan_duration' => 0.5,
        'memory_usage' => '64MB',
        'cache_used' => true,
    ],
]
```

---

**Need more details?** Check the [examples](../examples/) directory for practical usage patterns.