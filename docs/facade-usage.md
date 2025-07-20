# Laravel Atlas Facade Usage

The Laravel Atlas package provides a convenient facade for easy access to all Atlas functionality.

## Basic Usage

```php
use Grazulex\LaravelAtlas\Facades\Atlas;

// Scan models and get raw data
$modelData = Atlas::scan('models');

// Export models to JSON
$json = Atlas::export('models', 'json');

// Export routes to Markdown
$markdown = Atlas::export('routes', 'markdown');

// Generate multiple types in HTML format
$html = Atlas::generate(['models', 'routes', 'jobs'], 'html');
```

## Advanced Usage

```php
// Scan with custom options
$modelData = Atlas::scan('models', [
    'include_relationships' => true,
    'include_scopes' => false,
    'scan_path' => app_path('Domain/Models'),
]);

// Export with custom options
$pdf = Atlas::export('routes', 'pdf', [
    'detailed' => true,
    'include_middleware' => true,
]);

// Generate with output file
$content = Atlas::generate(['models', 'routes'], 'html', [
    'output' => storage_path('atlas/complete.html'),
    'detailed' => true,
]);
```

## Direct Access to Components

```php
// Get a specific mapper
$modelMapper = Atlas::mapper('models');
$data = $modelMapper->scan();

// Get a specific exporter
$htmlExporter = Atlas::exporter('html');
$content = $htmlExporter->export($data);

// Get available types and formats
$types = Atlas::getAvailableTypes();     // ['models', 'routes', 'jobs']
$formats = Atlas::getAvailableFormats(); // ['json', 'html', 'markdown', 'mermaid', 'pdf']
```

## Extending Atlas

```php
// Register custom mapper
Atlas::registerMapper('custom', CustomMapper::class);

// Register custom exporter
Atlas::registerExporter('xml', XmlExporter::class);

// Now you can use them
$data = Atlas::scan('custom');
$xml = Atlas::export('models', 'xml');
```

## Artisan Command vs Facade

```php
// Instead of using artisan command:
// php artisan atlas:generate --type=models --format=json --output=storage/models.json

// You can use the facade in your code:
$json = Atlas::export('models', 'json');
file_put_contents(storage_path('models.json'), $json);
```
