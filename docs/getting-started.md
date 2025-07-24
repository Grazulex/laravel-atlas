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

### 1. Quick Start - HTML Interactive Dashboard

Generate an interactive HTML dashboard:

```bash
php artisan atlas:export
```

This scans all available components and generates an HTML dashboard with dark mode support.

### 2. Generate Specific Component Reports

Generate specific component analysis:

```bash
php artisan atlas:export --type=models --format=html --output=docs/models.html
```

### 3. Generate PDF Documentation

Generate professional PDF documentation:

```bash
php artisan atlas:export --format=pdf --output=docs/architecture.pdf
```

## Available Component Types

Laravel Atlas analyzes **16 component types**:

| Component | Description | Example Use Case |
|-----------|-------------|------------------|
| **models** | Eloquent models with relationships | Database architecture documentation |
| **routes** | Application routes with middleware | API documentation, route analysis |
| **commands** | Artisan commands | CLI tool documentation |
| **services** | Service classes | Business logic mapping |
| **notifications** | Notification classes | Communication flow analysis |
| **middlewares** | HTTP middleware | Security and filtering analysis |
| **form_requests** | Form request validation | Input validation documentation |
| **events** | Event classes | Event-driven architecture mapping |
| **controllers** | Controller classes | Request handling documentation |
| **resources** | API resource classes | API response transformation |
| **jobs** | Queue job classes | Background processing documentation |
| **actions** | Single action controllers | Action-oriented architecture |
| **policies** | Authorization policies | Access control documentation |
| **rules** | Custom validation rules | Validation logic documentation |
| **listeners** | Event listeners | Event handling documentation |
| **observers** | Model observers | Model lifecycle documentation |

## Export Formats

Choose the right format for your needs:

### HTML - Interactive Documentation
- **Best for**: Team reviews, presentations, interactive exploration
- **Features**: Dark mode, responsive design, navigation, visual cards
- **Use case**: Architecture reviews, onboarding documentation

```bash
php artisan atlas:export --format=html --output=docs/architecture.html
```

### PDF - Professional Documentation
- **Best for**: Presentations, compliance reports, archival documentation
- **Features**: Professional layout, A4 format, complete documentation
- **Use case**: Business presentations, compliance documentation

```bash
php artisan atlas:export --format=pdf --output=docs/architecture.pdf
```

### JSON - Data Processing
- **Best for**: API integration, custom processing, automation
- **Features**: Machine-readable, structured data
- **Use case**: CI/CD integration, custom analysis tools

```bash
php artisan atlas:export --format=json --output=api/architecture.json
```

## Basic Usage Examples

### CLI Commands

```bash
# Generate complete architecture analysis
php artisan atlas:export --type=all --format=html --output=public/docs/architecture.html

# Generate specific component documentation
php artisan atlas:export --type=models --format=pdf --output=docs/models.pdf
php artisan atlas:export --type=routes --format=json --output=api/routes.json

# Generate multiple formats
php artisan atlas:export --format=html --output=reports/architecture.html
php artisan atlas:export --format=pdf --output=reports/architecture.pdf
php artisan atlas:export --format=json --output=reports/architecture.json
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
$pdfDoc = Atlas::export('models', 'pdf');
$jsonData = Atlas::export('routes', 'json');

// Save to files
file_put_contents('public/atlas/dashboard.html', $htmlReport);
file_put_contents('docs/models.pdf', $pdfDoc);
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
            'html' => env('ATLAS_FORMAT_HTML', true),
            'pdf' => env('ATLAS_FORMAT_PDF', true),
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