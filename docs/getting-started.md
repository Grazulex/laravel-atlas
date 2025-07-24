# Getting Started

Welcome to Laravel Atlas! This guide will help you get started with analyzing and documenting your Laravel application architecture.

## Installation

Install Laravel Atlas via Composer:

```bash
composer require grazulex/laravel-atlas --dev
```

The service provider will be automatically registered thanks to Laravel's package auto-discovery.

### Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=atlas-config
```

This creates a `config/atlas.php` file where you can customize Atlas behavior.

## Your First Atlas Report

### 1. Quick Start - JSON Export

Generate a basic JSON report of your application:

```bash
php artisan atlas:generate
```

This scans all available components and outputs JSON data to the console.

### 2. Interactive HTML Dashboard

Generate an interactive HTML dashboard:

```bash
php artisan atlas:generate --format=html --output=public/atlas/architecture.html
```

Open `public/atlas/architecture.html` in your browser to see:
- 🌓 Dark mode toggle
- 📱 Responsive navigation
- 📊 Component cards with details
- 🎨 Professional styling

### 3. Component-Specific Analysis

Analyze specific component types:

```bash
# Models with relationships
php artisan atlas:generate --type=models --format=html --output=docs/models.html

# Routes with middleware
php artisan atlas:generate --type=routes --format=markdown --output=docs/routes.md

# Services with dependencies
php artisan atlas:generate --type=services --format=json --output=api/services.json
```

## Available Component Types

Laravel Atlas analyzes **7 component types**:

| Component | Description | Example Use Case |
|-----------|-------------|------------------|
| **models** | Eloquent models with relationships | Database architecture documentation |
| **routes** | Application routes with middleware | API documentation, route analysis |
| **commands** | Artisan commands | CLI tool documentation |
| **services** | Service classes | Business logic mapping |
| **notifications** | Notification classes | Communication flow analysis |
| **middlewares** | HTTP middleware | Security and filtering analysis |
| **form_requests** | Form request validation | Input validation documentation |

## Export Formats

Choose the right format for your needs:

### HTML - Interactive Documentation
- **Best for**: Team reviews, presentations, interactive exploration
- **Features**: Dark mode, responsive design, navigation, visual cards
- **Use case**: Architecture reviews, onboarding documentation

```bash
php artisan atlas:generate --format=html --output=docs/architecture.html
```

### Markdown - Static Documentation
- **Best for**: README files, static sites, version control
- **Features**: GitHub-compatible, human-readable
- **Use case**: Documentation sites, README inclusion

```bash
php artisan atlas:generate --format=markdown --output=docs/ARCHITECTURE.md
```

### JSON - Data Processing
- **Best for**: API integration, custom processing, automation
- **Features**: Machine-readable, structured data
- **Use case**: CI/CD integration, custom analysis tools

```bash
php artisan atlas:generate --format=json --output=api/architecture.json
```

### PHP - Laravel Integration
- **Best for**: Laravel integration, advanced processing
- **Features**: Native PHP data structures
- **Use case**: Custom Laravel commands, data manipulation

```bash
php artisan atlas:generate --format=php --output=storage/atlas/architecture.php
```

## Basic Usage Examples

### CLI Commands

```bash
# Generate complete architecture analysis
php artisan atlas:generate --type=all --format=html --output=public/docs/architecture.html

# Generate specific component documentation
php artisan atlas:generate --type=models --format=markdown --output=docs/models.md
php artisan atlas:generate --type=routes --format=json --output=api/routes.json

# Generate multiple formats
php artisan atlas:generate --format=html --output=reports/architecture.html
php artisan atlas:generate --format=markdown --output=reports/architecture.md
php artisan atlas:generate --format=json --output=reports/architecture.json
```

### Programmatic Usage

```php
use LaravelAtlas\Facades\Atlas;

// Scan specific component types
$models = Atlas::scan('models');
$routes = Atlas::scan('routes');
$services = Atlas::scan('services');

// Generate reports
$htmlReport = Atlas::export('all', 'html');
$markdownDoc = Atlas::export('models', 'markdown');
$jsonData = Atlas::export('routes', 'json');

// Save to files
file_put_contents('public/atlas/dashboard.html', $htmlReport);
file_put_contents('docs/models.md', $markdownDoc);
file_put_contents('api/routes.json', $jsonData);
```

## Configuration

Basic configuration options in `config/atlas.php`:

```php
return [
    'enabled' => env('ATLAS_ENABLED', true),
    
    'generation' => [
        'output_path' => env('ATLAS_OUTPUT_PATH', base_path('atlas')),
        'formats' => [
            'json' => env('ATLAS_FORMAT_JSON', true),
            'markdown' => env('ATLAS_FORMAT_MARKDOWN', true),
            'html' => env('ATLAS_FORMAT_HTML', true),
        ],
    ],
    
    'analysis' => [
        'include_vendors' => env('ATLAS_INCLUDE_VENDORS', false),
        'max_depth' => env('ATLAS_MAX_DEPTH', 10),
        'scan_paths' => [
            app_path(),
            database_path(),
            config_path(),
        ],
    ],
];
```

## Next Steps

1. **Explore Examples**: Check out [examples/](../examples/) for working code examples
2. **HTML Features**: See [HTML Export Documentation](html-exports/) for interactive features
3. **Export Formats**: Read [Export Formats Guide](export-formats.md) for detailed format comparison
4. **Integration**: Learn about CI/CD integration and automation

## Troubleshooting

### Common Issues

**No components found:**
- Ensure you're running Atlas from your Laravel project root
- Check that your application follows Laravel conventions
- Verify file permissions

**Memory issues:**
- Increase PHP memory limit: `ini_set('memory_limit', '512M')`
- Use component-specific scanning: `--type=models`
- Exclude vendor files in configuration

**File permissions:**
- Ensure output directory is writable
- Check Laravel storage permissions

## Support

- [📖 Full Documentation](README.md)
- [💡 Examples](../examples/)
- [🐛 Issue Tracker](https://github.com/Grazulex/laravel-atlas/issues)
- [💬 Discussions](https://github.com/Grazulex/laravel-atlas/discussions)