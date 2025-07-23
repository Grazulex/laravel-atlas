# Troubleshooting Guide

This guide helps you resolve common issues when using Laravel Atlas.

## 🔍 Common Issues

### Installation Issues

#### Composer Installation Fails

**Problem**: `composer require grazulex/laravel-atlas` fails with memory error.

**Solution**:
```bash
# Increase memory limit for composer
php -d memory_limit=-1 /usr/local/bin/composer require grazulex/laravel-atlas

# Or set COMPOSER_MEMORY_LIMIT
export COMPOSER_MEMORY_LIMIT=-1
composer require grazulex/laravel-atlas
```

#### Missing PHP Extensions

**Problem**: `Extension gd is missing from your system`

**Solutions**:
```bash
# Ubuntu/Debian
sudo apt-get install php8.3-gd php8.3-json

# CentOS/RHEL
sudo yum install php-gd php-json

# macOS with Homebrew
brew install php@8.3

# Windows with XAMPP
# Enable extensions in php.ini:
# extension=gd
# extension=json
```

#### Permission Errors

**Problem**: `Permission denied` when writing output files.

**Solution**:
```bash
# Set proper permissions
chmod -R 755 storage/
chown -R www-data:www-data storage/

# Or create atlas directory manually
mkdir -p storage/atlas
chmod 755 storage/atlas
```

### Generation Issues

#### Command Not Found

**Problem**: `Command "atlas:generate" is not defined`

**Solutions**:
1. **Check if package is installed**:
   ```bash
   composer show grazulex/laravel-atlas
   ```

2. **Clear Laravel caches**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   ```

3. **Check service provider registration**:
   ```php
   // In config/app.php (if auto-discovery is disabled)
   'providers' => [
       LaravelAtlas\LaravelAtlasServiceProvider::class,
   ],
   ```

#### Memory Limit Errors

**Problem**: `Fatal error: Allowed memory size exhausted`

**Solutions**:
1. **Increase PHP memory limit**:
   ```bash
   php -d memory_limit=1G artisan atlas:generate
   ```

2. **Set in configuration**:
   ```php
   // config/atlas.php
   'analysis' => [
       'memory_limit' => '1G',
   ],
   ```

3. **Use shallow analysis**:
   ```bash
   php artisan atlas:generate --depth=shallow
   ```

4. **Enable parallel processing**:
   ```bash
   php artisan atlas:generate --parallel=4
   ```

#### Timeout Issues

**Problem**: Atlas generation times out on large applications.

**Solutions**:
1. **Increase timeout**:
   ```bash
   php -d max_execution_time=600 artisan atlas:generate
   ```

2. **Use component-specific scanning**:
   ```bash
   php artisan atlas:generate --type=models
   php artisan atlas:generate --type=routes
   ```

3. **Enable caching**:
   ```bash
   php artisan atlas:generate --cache
   ```

### Export Issues

#### HTML Export Problems

**Problem**: HTML export is blank or malformed.

**Solutions**:
1. **Check output directory permissions**:
   ```bash
   chmod 755 public/atlas/
   ```

2. **Verify theme exists**:
   ```php
   // config/atlas.php
   'formats' => [
       'html' => [
           'theme' => 'default', // Use default theme
       ],
   ],
   ```

3. **Check for JavaScript errors**:
   - Open browser developer tools
   - Look for console errors
   - Ensure jQuery/TailwindCSS are loading

#### PDF Export Issues  

**Problem**: PDF export fails or produces empty files.

**Solutions**:
1. **Install PDF dependencies**:
   ```bash
   composer require dompdf/dompdf
   ```

2. **Check memory and timeout settings**:
   ```bash
   php -d memory_limit=512M -d max_execution_time=300 artisan atlas:generate --format=pdf
   ```

3. **Use simpler template**:
   ```php
   // config/atlas.php
   'formats' => [
       'pdf' => [
           'template' => 'minimal',
       ],
   ],
   ```

#### Image Export Problems

**Problem**: Image export fails with GD errors.

**Solutions**:
1. **Verify GD extension**:
   ```bash
   php -m | grep gd
   ```

2. **Check image format support**:
   ```php
   // Test GD capabilities
   var_dump(gd_info());
   ```

3. **Use supported format**:
   ```bash
   php artisan atlas:generate --format=image --type=png
   ```

### Performance Issues

#### Slow Analysis

**Problem**: Atlas takes too long to analyze large applications.

**Solutions**:
1. **Use shallow analysis**:
   ```bash
   php artisan atlas:generate --depth=shallow --max-depth=3
   ```

2. **Exclude vendor files**:
   ```bash
   php artisan atlas:generate --exclude-vendors
   ```

3. **Enable parallel processing**:
   ```php
   // config/atlas.php
   'performance' => [
       'parallel_processing' => true,
       'max_parallel_processes' => 8,
   ],
   ```

4. **Use component-specific analysis**:
   ```bash
   php artisan atlas:generate --type=models,routes
   ```

#### High Memory Usage

**Problem**: Atlas consumes too much memory.

**Solutions**:
1. **Reduce analysis depth**:
   ```php
   'analysis' => [
       'max_depth' => 5,
   ],
   ```

2. **Exclude unnecessary paths**:
   ```php
   'analysis' => [
       'exclude_paths' => [
           base_path('vendor'),
           base_path('node_modules'),
           base_path('tests'),
       ],
   ],
   ```

3. **Use streaming for large datasets**:
   ```bash
   php artisan atlas:generate --stream --chunk-size=50
   ```

### Cache Issues

#### Stale Cache Data

**Problem**: Atlas returns outdated results despite code changes.

**Solutions**:
1. **Clear Atlas cache**:
   ```bash
   php artisan atlas:clear-cache
   ```

2. **Force fresh analysis**:
   ```bash
   php artisan atlas:generate --fresh
   ```

3. **Disable cache temporarily**:
   ```bash
   php artisan atlas:generate --no-cache
   ```

#### Cache Permission Errors

**Problem**: Cannot write to cache directory.

**Solutions**:
```bash
# Set cache permissions
chmod -R 755 storage/framework/cache/
chown -R www-data:www-data storage/framework/cache/

# Clear existing cache
php artisan cache:clear
```

## 🐛 Debugging

### Enable Debug Mode

```php
// config/atlas.php
'debug' => env('ATLAS_DEBUG', false),
```

```bash
# Run with debug output
ATLAS_DEBUG=true php artisan atlas:generate --verbose
```

### Log Analysis

```php
// Check Laravel logs
tail -f storage/logs/laravel.log

// Atlas-specific logging
Log::channel('atlas')->info('Custom debug message');
```

### Test Individual Components

```bash
# Test model scanning only
php artisan atlas:generate --type=models --verbose

# Test specific model
php artisan atlas:test --model=User

# Validate configuration
php artisan atlas:config --validate
```

## 📊 Performance Monitoring

### Memory Usage Monitoring

```bash
# Monitor memory during generation
/usr/bin/time -v php artisan atlas:generate

# PHP memory usage
php -d memory_limit=1G artisan atlas:generate --verbose
```

### Profiling

```php
// Enable profiling in config
'performance' => [
    'profiling' => env('ATLAS_PROFILING', false),
    'profile_output' => storage_path('atlas/profiles'),
],
```

## 🔧 Configuration Validation

### Check Current Settings

```bash
# Show all configuration
php artisan atlas:config

# Show specific section
php artisan atlas:config --section=analysis

# Validate configuration
php artisan atlas:config --validate
```

### Test Configuration

```bash
# Dry run to test configuration
php artisan atlas:generate --dry-run

# Test specific export format
php artisan atlas:generate --format=html --test
```

## 📝 Getting Help

### Diagnostic Information

When reporting issues, include this information:

```bash
# System information
php --version
php artisan --version

# Atlas configuration
php artisan atlas:config

# Laravel configuration
php artisan about

# Package version
composer show grazulex/laravel-atlas
```

### Debug Output Example

```bash
# Generate comprehensive debug information
php artisan atlas:generate --verbose --debug > atlas-debug.log 2>&1
```

### Common Error Patterns

#### "Class not found" Errors

```php
// Usually indicates autoloader issues
composer dump-autoload
php artisan config:clear
```

#### "Method does not exist" Errors

```php
// Check Laravel version compatibility
php artisan --version
// Ensure Laravel 12.x is installed
```

#### "Service provider not found" Errors

```php
// Re-publish service provider
php artisan vendor:publish --provider="LaravelAtlas\LaravelAtlasServiceProvider" --force
```

## 🚨 Emergency Recovery

### Reset Atlas Completely

```bash
# Remove all Atlas files
rm -rf storage/atlas/
rm -rf public/atlas/
rm -f config/atlas.php

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Reinstall package
composer remove grazulex/laravel-atlas
composer require grazulex/laravel-atlas
```

### Minimal Working Configuration

```php
// config/atlas.php - minimal config
<?php
return [
    'enabled' => true,
    'output' => [
        'path' => storage_path('atlas'),
    ],
    'analysis' => [
        'max_depth' => 5,
        'include_vendors' => false,
    ],
    'formats' => [
        'json' => ['enabled' => true],
    ],
    'cache' => [
        'enabled' => false,
    ],
];
```

---

**Still Having Issues?** 

1. Check the [GitHub Issues](https://github.com/Grazulex/laravel-atlas/issues)
2. Join the [Discussions](https://github.com/Grazulex/laravel-atlas/discussions)
3. Create a [new issue](https://github.com/Grazulex/laravel-atlas/issues/new) with detailed information