# Installation & Configuration

## 📦 Installation

Install Laravel Atlas via Composer:

```bash
composer require grazulex/laravel-atlas --dev
```

> **💡 Auto-Discovery**  
> The service provider is automatically registered thanks to Laravel's package auto-discovery.

## ⚙️ Configuration

### Publish Configuration (Optional)

To customize Laravel Atlas behavior, publish the configuration file:

```bash
php artisan vendor:publish --tag=atlas-config
```

This creates `config/atlas.php` with the following options:

### Configuration Options

```php
<?php

return [
    // Enable or disable Atlas functionality
    'enabled' => env('ATLAS_ENABLED', true),

    // Status tracking configuration
    'status_tracking' => [
        'enabled' => env('ATLAS_STATUS_TRACKING_ENABLED', true),
        'file_path' => env('ATLAS_STATUS_FILE_PATH', storage_path('logs/atlas_status.log')),
        'track_history' => env('ATLAS_TRACK_HISTORY', true),
        'max_entries' => env('ATLAS_MAX_ENTRIES', 1000),
    ],

    // Atlas generation options
    'generation' => [
        'output_path' => env('ATLAS_OUTPUT_PATH', base_path('atlas')),
        'formats' => [
            'mermaid' => env('ATLAS_FORMAT_MERMAID', true),
            'json' => env('ATLAS_FORMAT_JSON', true),
            'markdown' => env('ATLAS_FORMAT_MARKDOWN', true),
        ],
    ],

    // Analysis depth and scope
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

### Environment Variables

You can configure Laravel Atlas using environment variables in your `.env` file:

```env
# Enable/disable Atlas functionality
ATLAS_ENABLED=true

# Status tracking
ATLAS_STATUS_TRACKING_ENABLED=true
ATLAS_STATUS_FILE_PATH=/custom/path/atlas_status.log
ATLAS_TRACK_HISTORY=true
ATLAS_MAX_ENTRIES=1000

# Generation settings
ATLAS_OUTPUT_PATH=/custom/atlas/output
ATLAS_FORMAT_MERMAID=true
ATLAS_FORMAT_JSON=true
ATLAS_FORMAT_MARKDOWN=true

# Analysis settings
ATLAS_INCLUDE_VENDORS=false
ATLAS_MAX_DEPTH=10
```

## 🔧 Requirements

- **PHP**: ^8.3
- **Laravel**: ^12.0
- **Carbon**: ^3.10

### Optional Dependencies

For enhanced functionality, install these optional packages:

```bash
# For PDF export support
composer require dompdf/dompdf --dev

# For advanced Markdown formatting
composer require league/html-to-markdown --dev

# For custom export templates
composer require twig/twig --dev
```

## 🚀 Verification

Verify your installation by running:

```bash
php artisan atlas:generate --format=json
```

This should output a JSON representation of your application's structure.

## 🛠️ Troubleshooting

### Command Not Found

If `php artisan atlas:generate` is not found:

1. Clear your application cache:
```bash
php artisan config:clear
php artisan cache:clear
```

2. Verify the service provider is registered:
```bash
php artisan package:discover
```

### Permission Issues

If you encounter permission issues with output files:

```bash
# Create atlas directory with proper permissions
mkdir -p storage/atlas
chmod 755 storage/atlas
```

### Memory Limits

For large applications, you may need to increase PHP memory limit:

```bash
# Temporarily increase memory limit
php -d memory_limit=512M artisan atlas:generate
```

Or set it permanently in your `php.ini`:
```ini
memory_limit = 512M
```