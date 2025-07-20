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

### Mermaid Exporter (`mermaid`)

Creates visual diagrams using Mermaid syntax for architecture visualization.

**Configuration Options:**
```php
[
    'direction' => 'TD',              // Graph direction (TD, LR, BT, RL)
    'theme' => 'default',             // Diagram theme
    'include_relationships' => true,  // Show component relationships
    'show_methods' => false,          // Include method names in nodes
    'cluster_by_type' => true,       // Group similar components
]
```

**Example Output:**
```mermaid
graph TD
    %% Laravel Atlas Architecture Map
    %% Generated at: 2024-01-01T12:00:00.000000Z

    %% Models section
    User[User Model]
    Post[Post Model]
    Role[Role Model]
    
    %% Relationships
    User --> Post : hasMany
    User --> Role : belongsTo
    Post --> User : belongsTo
    
    %% Controllers section
    UserController[UserController]
    PostController[PostController]
    
    %% Controller-Model relationships
    UserController --> User
    PostController --> Post
```

**Usage:**
```bash
# Generate Mermaid diagram
php artisan atlas:generate --format=mermaid --output=docs/architecture.mmd

# Left-to-right layout
php artisan atlas:generate --format=mermaid --type=models
```

### HTML Exporter (`html`)

Produces interactive HTML documentation with navigation and search capabilities.

**Configuration Options:**
```php
[
    'theme' => 'default',         // UI theme (default, dark, minimal)
    'include_search' => true,     // Add search functionality
    'include_navigation' => true, // Include navigation sidebar
    'interactive_diagrams' => true, // Interactive Mermaid diagrams
    'expand_sections' => false,   // Start with sections expanded
]
```

**Features:**
- Interactive component exploration
- Search and filter capabilities
- Responsive design
- Embedded Mermaid diagrams
- Export to PDF functionality

**Usage:**
```bash
# Generate interactive HTML documentation
php artisan atlas:generate --format=html --output=public/atlas.html

# Dark theme HTML
php artisan atlas:generate --format=html --type=models
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
php artisan atlas:generate --format=pdf --type=routes
```

## 🔧 Customizing Export Behavior

### Command-Line Options

```bash
# Export with custom formatting
php artisan atlas:generate --format=markdown --detailed --output=docs/detailed-architecture.md

# Multiple component types
php artisan atlas:generate --type=models --format=mermaid --output=diagrams/models.mmd
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

// Custom Mermaid diagram
$mermaidDiagram = Atlas::export('models', 'mermaid', [
    'direction' => 'LR',
    'theme' => 'dark',
    'include_relationships' => true,
]);

// Custom HTML with theming
$htmlOutput = Atlas::export('controllers', 'html', [
    'theme' => 'dark',
    'include_search' => true,
    'interactive_diagrams' => true,
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