# Installation Guide

This guide walks you through installing and setting up Laravel Atlas in your Laravel application.

## 📋 Requirements

- **PHP**: ^8.3
- **Laravel**: ^12.0
- **Composer**: Latest version recommended
- **Extensions**: 
  - `ext-gd` (for image export functionality)
  - `ext-json` (typically included)

## 🚀 Installation

### Via Composer (Recommended)

```bash
composer require grazulex/laravel-atlas
```

### Development Installation

If you want to contribute or use the latest development version:

```bash
composer require grazulex/laravel-atlas:dev-main
```

## ⚙️ Package Discovery

Laravel Atlas uses automatic package discovery. The service provider and facades will be registered automatically.

If you have disabled package discovery, manually add the service provider to `config/app.php`:

```php
'providers' => [
    // Other providers...
    LaravelAtlas\LaravelAtlasServiceProvider::class,
],

'aliases' => [
    // Other aliases...
    'Atlas' => LaravelAtlas\Facades\Atlas::class,
],
```

## 📝 Configuration

### Publish Configuration (Optional)

Laravel Atlas works out-of-the-box with sensible defaults. To customize the configuration:

```bash
php artisan vendor:publish --provider="LaravelAtlas\LaravelAtlasServiceProvider" --tag="config"
```

This creates `config/atlas.php` with all available options.

### Environment Variables

Add these optional environment variables to your `.env` file:

```env
# Atlas Configuration
ATLAS_ENABLED=true
ATLAS_OUTPUT_PATH=storage/atlas
ATLAS_CACHE_ENABLED=true
ATLAS_MAX_DEPTH=10

# Export Formats
ATLAS_FORMAT_JSON=true
ATLAS_FORMAT_MARKDOWN=true
ATLAS_FORMAT_HTML=true
ATLAS_FORMAT_PDF=true
ATLAS_FORMAT_IMAGE=true

# Performance
ATLAS_MEMORY_LIMIT=256M
ATLAS_PARALLEL_PROCESSING=true
ATLAS_INCLUDE_VENDORS=false
```

## 🔧 Additional Dependencies

### For PDF Export

If you plan to use PDF export functionality:

```bash
composer require dompdf/dompdf
```

### For Advanced Markdown

For enhanced markdown formatting:

```bash
composer require league/html-to-markdown
```

### For Testing Integration

If you want to use Atlas in your tests:

```bash
composer require --dev pestphp/pest
```

## ✅ Verify Installation

Test that Laravel Atlas is properly installed:

```bash
# Check if the Atlas command is available
php artisan list atlas

# Generate a basic map to test functionality
php artisan atlas:generate --type=models --format=json
```

Expected output:
```
atlas:generate  Generate Laravel Atlas architecture maps
```

## 🎯 First Usage

Generate your first architecture map:

```bash
# Generate complete application map
php artisan atlas:generate

# Generate model map with relationships
php artisan atlas:generate --type=models --include-relationships

# Generate interactive HTML report
php artisan atlas:generate --format=html --output=public/atlas.html
```

## 🗂️ Directory Structure

After installation, Laravel Atlas will create the following structure when first used:

```
your-laravel-app/
├── config/
│   └── atlas.php              # Configuration file (if published)
├── storage/
│   └── atlas/                 # Default output directory
│       ├── cache/             # Analysis cache
│       └── exports/           # Generated exports
└── public/
    └── atlas/                 # Public HTML exports
```

## 🔍 Troubleshooting Installation

### Common Issues

#### Composer Memory Limit
```bash
# If composer runs out of memory
php -d memory_limit=-1 /usr/local/bin/composer require grazulex/laravel-atlas
```

#### Missing Extensions
```bash
# Ubuntu/Debian
sudo apt-get install php8.3-gd php8.3-json

# CentOS/RHEL
sudo yum install php-gd php-json

# macOS with Homebrew
brew install php@8.3
```

#### Permission Issues
```bash
# Set proper permissions for storage directory
chmod -R 755 storage/
chown -R www-data:www-data storage/
```

### Verification Steps

1. **Check PHP Version**:
   ```bash
   php --version
   # Should show PHP 8.3 or higher
   ```

2. **Check Laravel Version**:
   ```bash
   php artisan --version
   # Should show Laravel 12.x
   ```

3. **Check Extensions**:
   ```bash
   php -m | grep -E "(gd|json)"
   # Should show both gd and json
   ```

4. **Test Atlas Command**:
   ```bash
   php artisan atlas:generate --help
   # Should show command help
   ```

## 🚀 Next Steps

After successful installation:

1. **Read the [Configuration Guide](configuration.md)** - Customize Atlas for your needs
2. **Try the [Basic Examples](../examples/basic-usage/)** - Learn core functionality
3. **Explore [Export Formats](../examples/export-formats/)** - Different output options
4. **Set up [CI/CD Integration](../examples/integration/)** - Automate documentation

## 📚 Additional Resources

- **[Configuration Reference](configuration.md)** - All configuration options
- **[API Documentation](api.md)** - Programmatic usage
- **[Examples](../examples/)** - Practical usage examples
- **[Troubleshooting](troubleshooting.md)** - Common issues and solutions

---

**Having Installation Issues?** [Open an issue](https://github.com/Grazulex/laravel-atlas/issues) with your system details and error messages.