# Exporters

Laravel Atlas supports multiple export formats to suit different use cases. Each exporter transforms the scanned data into a specific format with customizable options.

## 📊 Available Exporters

### JSON Exporter (`json`)

Exports data as structured JSON, ideal for programmatic consumption.

**Configuration Options:**
```php
[
    'pretty_print' => true,  // Format JSON with indentation
]
```

**Example Output:**
```json
{
  "atlas_version": "1.0.0",
  "generated_at": "2024-01-01T12:00:00.000000Z",
  "generation_time_ms": 150.25,
  "type": "models",
  "format": "json",
  "data": {
    "models": {
      "type": "models",
      "data": [
        {
          "name": "User",
          "namespace": "App\\Models",
          "relationships": [
            {
              "type": "hasMany",
              "related": "App\\Models\\Post"
            }
          ]
        }
      ]
    }
  }
}
```

**Usage:**
```bash
# Basic JSON export
php artisan atlas:generate --format=json

# Compact JSON (no pretty printing)
php artisan atlas:generate --format=json --output=compact.json
```

### Markdown Exporter (`markdown`)

Generates human-readable documentation in Markdown format.

**Configuration Options:**
```php
[
    'include_toc' => true,        // Include table of contents
    'include_timestamp' => true,  // Include generation timestamp
    'include_stats' => true,      // Include summary statistics
    'detailed_sections' => true,  // Add detailed component information
]
```

**Example Output:**
```markdown
# Laravel Atlas Architecture Map

Generated at: 2024-01-01 12:00:00  
Generation time: 150.25ms

## Summary

- **Total Components**: 25
- **Models**: 5
- **Routes**: 15
- **Controllers**: 5

## Table of Contents

- [Models](#models)
- [Routes](#routes)
- [Controllers](#controllers)

## Models

### User
- **Namespace**: App\Models
- **Path**: /app/Models/User.php
- **Relationships**:
  - hasMany: Posts
  - belongsTo: Role
```

**Usage:**
```bash
# Generate markdown documentation
php artisan atlas:generate --format=markdown --output=docs/ARCHITECTURE.md

# Detailed markdown with all sections
php artisan atlas:generate --format=markdown --detailed
```

### HTML Exporter (`html`)

Produces interactive HTML documentation with advanced visualization using intelligent workflow.

**Configuration Options:**
```php
[
    'theme' => 'default',         // UI theme (default, dark, minimal)
    'include_search' => true,     // Add search functionality  
    'include_navigation' => true, // Include navigation sidebar
    'interactive_flows' => true,  // Interactive component flows
    'expand_sections' => false,   // Start with sections expanded
    'use_intelligent_workflow' => true, // Use PHP-to-HTML intelligent template
]
```

**Features:**
- Interactive component exploration with intelligent flows
- Advanced PHP-to-HTML processing workflow
- Search and filter capabilities
- Responsive design
- Complex architectural visualizations
- Export capabilities

**Usage:**
```bash
# Generate interactive HTML documentation
php artisan atlas:generate --format=html --output=public/atlas.html

# Use intelligent workflow for complex applications
php artisan atlas:generate --type=all --format=html --output=docs/architecture.html
```

### Image Exporter (`image`)

Creates visual diagrams and charts as PNG or JPG images.

**Configuration Options:**
```php
[
    'format' => 'png',            // Image format (png, jpg)
    'width' => 1920,              // Image width in pixels
    'height' => 1080,             // Image height in pixels
    'background_color' => 'white', // Background color
    'include_legend' => true,      // Include component legend
    'layout' => 'hierarchical',   // Layout algorithm
]
```

**Features:**
- High-quality architectural diagrams
- Customizable layouts and styling
- Multiple output formats
- Suitable for presentations and documentation
- Automatic component positioning

**Usage:**
```bash
# Generate PNG architectural diagram
php artisan atlas:generate --format=image --output=docs/architecture.png

# Generate JPG with custom dimensions
php artisan atlas:generate --type=models --format=image --output=diagrams/models.jpg
```

### PDF Exporter (`pdf`)

Creates printable PDF reports for documentation and compliance purposes.

**Configuration Options:**
```php
[
    'paper_size' => 'A4',         // Paper size (A4, Letter, etc.)
    'orientation' => 'portrait',   // Page orientation
    'include_diagrams' => true,    // Include visual diagrams
    'font_family' => 'DejaVu Sans', // Font family
    'margins' => [20, 15, 20, 15], // [top, right, bottom, left] in mm
]
```

**Requirements:**
```bash
# Install PDF generation dependencies
composer require dompdf/dompdf --dev
```

**Usage:**
```bash
# Generate PDF documentation
php artisan atlas:generate --format=pdf --output=reports/architecture.pdf

# Landscape orientation for wide diagrams
php artisan atlas:generate --format=pdf --type=routes --output=reports/routes.pdf
```

### PHP Exporter (`php`)

Exports raw PHP data structures for advanced programmatic processing.

**Configuration Options:**
```php
[
    'format_code' => true,        // Format PHP code with indentation
    'include_metadata' => true,   // Include generation metadata
    'variable_name' => 'atlasData', // Variable name for the data
    'export_as_array' => false,   // Export as array or return statement
]
```

**Example Output:**
```php
<?php
/**
 * Laravel Atlas Data Export
 * Generated at: 2024-01-01T12:00:00.000000Z
 * Generation time: 150.25ms
 */

return [
    'atlas_version' => '1.0.0',
    'generated_at' => '2024-01-01T12:00:00.000000Z',
    'type' => 'models',
    'data' => [
        'models' => [
            'type' => 'models',
            'data' => [
                [
                    'name' => 'User',
                    'namespace' => 'App\\Models',
                    'path' => '/app/Models/User.php',
                    // ... more data
                ],
            ],
        ],
    ],
];
```

**Usage:**
```bash
# Generate PHP data file
php artisan atlas:generate --format=php --output=storage/atlas/data.php

# Use in your application
$atlasData = include storage_path('atlas/data.php');
```

## 🔧 Customizing Export Behavior

### Command-Line Options

```bash
# Export with custom formatting
php artisan atlas:generate --format=markdown --output=docs/detailed-architecture.md

# Multiple component types
php artisan atlas:generate --type=models --format=image --output=diagrams/models.png

# Complex HTML with intelligent workflow
php artisan atlas:generate --type=all --format=html --output=public/architecture.html
```

### Programmatic Customization

```php
use LaravelAtlas\Facades\Atlas;

// Custom JSON export
$jsonOutput = Atlas::export('models', 'json', [
    'pretty_print' => false,
]);

// Custom Markdown export
$markdownOutput = Atlas::export('routes', 'markdown', [
    'include_toc' => true,
    'include_stats' => true,
    'detailed_sections' => true,
]);

// Custom Image generation
$imageData = Atlas::export('models', 'image', [
    'format' => 'png',
    'width' => 1920,
    'include_legend' => true,
]);

// Custom HTML with intelligent workflow
$htmlOutput = Atlas::export('controllers', 'html', [
    'theme' => 'dark',
    'include_search' => true,
    'use_intelligent_workflow' => true,
]);

// PHP data export
$phpData = Atlas::export('services', 'php', [
    'format_code' => true,
    'variable_name' => 'servicesData',
]);
```

### Working with Exporter Instances

```php
// Get exporter instance for advanced customization
$markdownExporter = Atlas::exporter('markdown');
$htmlExporter = Atlas::exporter('html');

// Configure and export
$data = Atlas::scan('models');
$customOutput = $markdownExporter->export($data);
```

## 📁 File Extensions and MIME Types

Each exporter provides appropriate file extensions and MIME types:

| Format   | Extension | MIME Type                 |
|----------|-----------|---------------------------|
| JSON     | `.json`   | `application/json`        |
| Markdown | `.md`     | `text/markdown`           |
| Mermaid  | `.mmd`    | `text/plain`              |
| HTML     | `.html`   | `text/html`               |
| PDF      | `.pdf`    | `application/pdf`         |

## 🎨 Export Themes and Styling

### Mermaid Themes

Available Mermaid themes:
- `default` - Standard Mermaid styling
- `dark` - Dark mode theme  
- `forest` - Green color scheme
- `base` - Minimal styling
- `neutral` - Neutral colors

### HTML Themes

Available HTML themes:
- `default` - Clean, professional look
- `dark` - Dark mode interface
- `minimal` - Simplified layout
- `compact` - Dense information display

## 🔄 Batch Export

Generate multiple formats simultaneously:

```bash
# Generate all formats
php artisan atlas:generate --format=json --output=docs/atlas.json
php artisan atlas:generate --format=markdown --output=docs/ARCHITECTURE.md
php artisan atlas:generate --format=html --output=public/atlas.html
php artisan atlas:generate --format=mermaid --output=diagrams/atlas.mmd
```

Or programmatically:

```php
$data = Atlas::scan('all');
$formats = ['json', 'markdown', 'html', 'mermaid'];

foreach ($formats as $format) {
    $output = Atlas::exporter($format)->export($data);
    file_put_contents("docs/atlas.{$format}", $output);
}
```

## 🎯 Best Practices

### Format Selection Guide

- **JSON** - For programmatic access, API integration, data processing
- **Markdown** - For documentation, README files, wikis
- **Mermaid** - For visual diagrams, architecture presentations
- **HTML** - For interactive exploration, team sharing
- **PDF** - For reports, compliance documentation, printing

### Performance Considerations

```php
// For large applications, export specific types
$modelsOnly = Atlas::export('models', 'json');

// Use compact formatting for smaller files
$compactJson = Atlas::export('routes', 'json', ['pretty_print' => false]);

// Generate static files for better performance
Atlas::export('all', 'html', ['output' => 'public/atlas.html']);
```

### Integration with CI/CD

```bash
# Generate documentation in pipeline
php artisan atlas:generate --format=markdown --output=docs/ARCHITECTURE.md
php artisan atlas:generate --format=html --output=public/docs/atlas.html

# Validate exports
php artisan atlas:generate --format=json --output=/tmp/atlas.json
php -c "json_decode(file_get_contents('/tmp/atlas.json')); echo 'JSON valid';"
```