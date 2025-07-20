# Usage Guide

Laravel Atlas provides both command-line and programmatic interfaces for generating application maps.

## 🖥️ Command Line Usage

The main command is `atlas:generate` with various options for customization.

### Basic Usage

```bash
# Generate a complete application map (default: JSON format)
php artisan atlas:generate

# Generate with specific format
php artisan atlas:generate --format=mermaid
php artisan atlas:generate --format=markdown
php artisan atlas:generate --format=html
php artisan atlas:generate --format=pdf
```

### Component-Specific Generation

```bash
# Generate map for specific component types
php artisan atlas:generate --type=models
php artisan atlas:generate --type=routes
php artisan atlas:generate --type=jobs
php artisan atlas:generate --type=services
php artisan atlas:generate --type=controllers
php artisan atlas:generate --type=events
php artisan atlas:generate --type=commands
php artisan atlas:generate --type=middleware
php artisan atlas:generate --type=policies
php artisan atlas:generate --type=resources
php artisan atlas:generate --type=notifications
php artisan atlas:generate --type=requests
php artisan atlas:generate --type=rules
```

### Output Options

```bash
# Save to specific file
php artisan atlas:generate --output=docs/architecture.json
php artisan atlas:generate --format=mermaid --output=docs/diagram.mmd
php artisan atlas:generate --format=markdown --output=docs/ARCHITECTURE.md

# Include detailed information
php artisan atlas:generate --detailed
php artisan atlas:generate --type=models --detailed --format=markdown
```

### Combined Examples

```bash
# Generate detailed model map as Markdown
php artisan atlas:generate --type=models --format=markdown --detailed --output=docs/models.md

# Generate complete application map with all details as HTML
php artisan atlas:generate --format=html --detailed --output=public/atlas.html

# Generate routes map as Mermaid diagram
php artisan atlas:generate --type=routes --format=mermaid --output=docs/routes.mmd
```

## ⚙️ Command Options

| Option | Short | Description | Default |
|--------|-------|-------------|---------|
| `--type` | `-t` | Component type to map | `all` |
| `--format` | `-f` | Output format | `json` |
| `--output` | `-o` | Output file path | Console output |
| `--detailed` | `-d` | Include detailed information | `false` |

### Available Types

- `all` - All component types (default)
- `models` - Eloquent models
- `routes` - Application routes  
- `jobs` - Queued jobs
- `services` - Service classes
- `controllers` - Controllers
- `events` - Application events
- `commands` - Artisan commands
- `middleware` - HTTP middleware
- `policies` - Authorization policies
- `resources` - API resources
- `notifications` - Notification classes
- `requests` - Form request classes
- `rules` - Custom validation rules

### Available Formats

- `json` - Machine-readable JSON (default)
- `markdown` - Human-readable Markdown
- `mermaid` - Mermaid diagram syntax
- `html` - Interactive HTML documentation
- `pdf` - Printable PDF report

## 💻 Programmatic Usage

Use the Atlas facade for programmatic access:

### Basic Scanning

```php
use LaravelAtlas\Facades\Atlas;

// Scan specific component type
$modelData = Atlas::scan('models');
$routeData = Atlas::scan('routes');

// Scan with options
$detailedModels = Atlas::scan('models', ['include_detailed' => true]);
```

### Export to Formats

```php
// Export single type
$jsonOutput = Atlas::export('models', 'json');
$markdownOutput = Atlas::export('routes', 'markdown');
$mermaidDiagram = Atlas::export('models', 'mermaid');

// Export with options
$htmlOutput = Atlas::export('models', 'html', [
    'include_detailed' => true,
    'theme' => 'dark'
]);
```

### Generate Multiple Types

```php
// Generate multiple types at once
$output = Atlas::generate(['models', 'routes'], 'json');
$htmlMap = Atlas::generate(['models', 'services', 'controllers'], 'html');

// Generate all types
$completeMap = Atlas::generate('all', 'json');
```

### Working with Mappers

```php
// Get specific mapper instance
$modelMapper = Atlas::mapper('models');
$routeMapper = Atlas::mapper('routes');

// Use mapper directly
$modelData = $modelMapper->scan(['include_relationships' => true]);
```

### Working with Exporters

```php
// Get specific exporter instance
$jsonExporter = Atlas::exporter('json');
$markdownExporter = Atlas::exporter('markdown');

// Use exporter directly
$data = Atlas::scan('models');
$output = $jsonExporter->export($data);
```

## 🔍 Understanding Output

### JSON Structure

```json
{
  "atlas_version": "1.0.0",
  "generated_at": "2024-01-01T12:00:00.000000Z",
  "generation_time_ms": 150.25,
  "type": "models",
  "format": "json",
  "options": {
    "detailed": true
  },
  "summary": {
    "total_components": 15,
    "types": {
      "models": 5,
      "relationships": 10
    }
  },
  "data": {
    "models": {
      "type": "models",
      "scan_path": "/app/Models",
      "options": {},
      "data": [
        // ... component data
      ]
    }
  }
}
```

### Individual Component Data

Each component includes:

- **Basic Info**: Name, path, namespace
- **Metadata**: File size, creation date, modification date
- **Relationships**: Dependencies and connections
- **Details**: Methods, properties, configuration (if `--detailed`)

## 🎯 Best Practices

### Performance Tips

```bash
# For large applications, scan specific types instead of all
php artisan atlas:generate --type=models

# Use detailed mode only when necessary
php artisan atlas:generate --detailed --type=routes

# Save to files to avoid console output overhead
php artisan atlas:generate --output=storage/atlas/map.json
```

### Automation

```bash
# In CI/CD pipelines
php artisan atlas:generate --format=json --output=docs/architecture.json
php artisan atlas:generate --format=markdown --output=docs/ARCHITECTURE.md

# Generate multiple formats
php artisan atlas:generate --format=json --output=docs/atlas.json
php artisan atlas:generate --format=html --output=public/atlas.html
php artisan atlas:generate --format=mermaid --output=docs/diagram.mmd
```

### Version Control

Add to `.gitignore` if you don't want to commit generated files:

```gitignore
# Laravel Atlas generated files
/atlas/
/docs/atlas.*
/storage/atlas/
```

Or commit them for team visibility:

```bash
# Generate and commit documentation
php artisan atlas:generate --format=markdown --output=docs/ARCHITECTURE.md
git add docs/ARCHITECTURE.md
git commit -m "Update architecture documentation"
```