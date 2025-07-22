# Laravel Atlas

<img src="new_logo.png" alt="Laravel Atlas" width="200">

Advanced Laravel application mapping and visualization toolkit. Analyze, document, and visualize your Laravel project architecture with comprehensive dependency mapping and multiple export formats.

[![Latest Version](https://img.shields.io/packagist/v/grazulex/laravel-atlas.svg?style=flat-square)](https://packagist.org/packages/grazulex/laravel-atlas)
[![Total Downloads](https://img.shields.io/packagist/dt/grazulex/laravel-atlas.svg?style=flat-square)](https://packagist.org/packages/grazulex/laravel-atlas)
[![License](https://img.shields.io/github/license/grazulex/laravel-atlas.svg?style=flat-square)](https://github.com/Grazulex/laravel-atlas/blob/main/LICENSE.md)
[![PHP Version](https://img.shields.io/packagist/php-v/grazulex/laravel-atlas.svg?style=flat-square)](https://php.net/)
[![Laravel Version](https://img.shields.io/badge/laravel-12.x-ff2d20?style=flat-square&logo=laravel)](https://laravel.com/)
[![Tests](https://img.shields.io/github/actions/workflow/status/grazulex/laravel-atlas/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/Grazulex/laravel-atlas/actions)
[![Code Style](https://img.shields.io/badge/code%20style-pint-000000?style=flat-square&logo=laravel)](https://github.com/laravel/pint)

## 📖 Table of Contents

- [Overview](#overview)
- [✨ Features](#-features)
- [📦 Installation](#-installation)
- [🚀 Quick Start](#-quick-start)
- [🗺️ Architecture Mapping](#️-architecture-mapping)
- [📊 Export Formats](#-export-formats)
- [🔍 Analysis Tools](#-analysis-tools)
- [⚙️ Configuration](#️-configuration)
- [📚 Documentation](#-documentation)
- [💡 Examples](#-examples)
- [🧪 Testing](#-testing)
- [🔧 Requirements](#-requirements)
- [🚀 Performance](#-performance)
- [🤝 Contributing](#-contributing)
- [🔒 Security](#-security)
- [📄 License](#-license)

## Overview

Laravel Atlas is an advanced application mapping and visualization toolkit that scans your Laravel project to generate comprehensive architectural documentation. It analyzes models, services, routes, jobs, events, commands, and their interconnections, then exports visual representations in multiple formats.

**Perfect for code documentation, team onboarding, architecture reviews, and maintaining large enterprise applications.**

### 🎯 Use Cases

Laravel Atlas is perfect for:

- **Code Documentation** - Generate comprehensive application maps
- **Team Onboarding** - Visual architecture overviews for new developers
- **Architecture Reviews** - Analyze application structure and dependencies  
- **Legacy Code Analysis** - Understand complex existing applications
- **Compliance Reporting** - Generate architectural documentation

## ✨ Features

- 🚀 **Comprehensive Scanning** - Analyze models, services, routes, jobs, events, and more
- 🗺️ **Architecture Mapping** - Generate detailed application structure maps
- 📊 **Multiple Export Formats** - Export to Mermaid, Markdown, JSON, PDF, and HTML
- 🔍 **Dependency Analysis** - Track relationships and dependencies between components
- 📋 **Custom Node Types** - Extensible architecture for custom component types
- 🎯 **Smart Detection** - Intelligent component discovery and classification
- ✅ **Validation** - Validate architectural patterns and dependencies
- 📈 **Visual Diagrams** - Generate beautiful Mermaid diagrams and flowcharts
- 🧪 **Analysis Reports** - Comprehensive architectural analysis reports
- ⚡ **CLI Integration** - Powerful Artisan commands for map generation
- 🔄 **Real-time Updates** - Watch mode for continuous map updates
- 📝 **Documentation Generation** - Auto-generate architecture documentation

## 📦 Installation

Install the package via Composer:

```bash
composer require grazulex/laravel-atlas --dev
```

> **💡 Auto-Discovery**  
> The service provider will be automatically registered thanks to Laravel's package auto-discovery.

Publish configuration:

```bash
php artisan vendor:publish --tag=atlas-config
```

## 🚀 Quick Start

### 1. Generate Your First Map

```bash
php artisan atlas:generate
```

This creates a JSON output showing all discovered components in your application.

### 2. Generate Specific Component Maps

```bash
# Generate model architecture map
php artisan atlas:generate --type=models --format=mermaid

# Generate service layer map
php artisan atlas:generate --type=services --format=markdown

# Generate complete application map
php artisan atlas:generate --format=json --output=docs/architecture.json
```

### 3. Customize Export Formats

```bash
# Generate Mermaid diagram
php artisan atlas:generate --format=mermaid

# Generate comprehensive markdown documentation
php artisan atlas:generate --format=markdown

# Generate interactive HTML map
php artisan atlas:generate --format=html
```

### 4. Access Generated Maps Programmatically

```php
use LaravelAtlas\Facades\Atlas;

// Scan specific component types
$modelData = Atlas::scan('models');
$routeData = Atlas::scan('routes');

// Export to different formats
$jsonOutput = Atlas::export('models', 'json');
$markdownDocs = Atlas::export('routes', 'markdown');
$mermaidDiagram = Atlas::export('models', 'mermaid');
```

## 🗺️ Architecture Mapping

Laravel Atlas provides comprehensive architecture mapping capabilities through specialized mappers:

```php
use LaravelAtlas\Facades\Atlas;

// Model architecture mapping
$modelData = Atlas::scan('models', [
    'include_relationships' => true,
    'include_observers' => true,
    'include_factories' => true,
]);

// Service layer mapping
$serviceData = Atlas::scan('services', [
    'include_dependencies' => true,
    'include_interfaces' => true,
]);

// Route mapping
$routeData = Atlas::scan('routes', [
    'include_middleware' => true,
    'include_controllers' => true,
    'group_by_prefix' => true,
]);
```

## 📊 Export Formats

Multiple export formats for different use cases:

```bash
# Mermaid diagrams for documentation
php artisan atlas:generate --format=mermaid --output=docs/architecture.mmd

# JSON for programmatic access
php artisan atlas:generate --format=json --output=storage/atlas/map.json

# Markdown documentation
php artisan atlas:generate --format=markdown --output=docs/ARCHITECTURE.md

# Interactive HTML maps
php artisan atlas:generate --format=html --output=public/atlas/map.html

# PDF reports
php artisan atlas:generate --format=pdf --output=reports/architecture.pdf
```

### Programmatic Export

```php
use LaravelAtlas\Facades\Atlas;

// Export specific types to different formats
$jsonOutput = Atlas::export('models', 'json');
$markdownDocs = Atlas::export('routes', 'markdown');
$mermaidDiagram = Atlas::export('controllers', 'mermaid');
$htmlReport = Atlas::export('services', 'html');
```

## 🔍 Analysis Tools

Laravel Atlas provides comprehensive component analysis:

```php
use LaravelAtlas\Facades\Atlas;

// Analyze specific component types
$modelAnalysis = Atlas::scan('models', [
    'include_relationships' => true,
    'include_observers' => true
]);

$routeAnalysis = Atlas::scan('routes', [
    'include_middleware' => true,
    'include_controllers' => true
]);

// Generate detailed reports
$allComponents = Atlas::scan('all', ['detailed' => true]);

// Export analysis results
$analysisReport = Atlas::export('all', 'markdown', [
    'include_stats' => true,
    'detailed_sections' => true
]);
```

## ⚙️ Configuration

Laravel Atlas provides extensive configuration options:

```php
// config/atlas.php
return [
    'enabled' => env('ATLAS_ENABLED', true),
    
    'status_tracking' => [
        'enabled' => env('ATLAS_STATUS_TRACKING_ENABLED', true),
        'file_path' => env('ATLAS_STATUS_FILE_PATH', storage_path('logs/atlas_status.log')),
        'track_history' => env('ATLAS_TRACK_HISTORY', true),
        'max_entries' => env('ATLAS_MAX_ENTRIES', 1000),
    ],
    
    'generation' => [
        'output_path' => env('ATLAS_OUTPUT_PATH', base_path('atlas')),
        'formats' => [
            'mermaid' => env('ATLAS_FORMAT_MERMAID', true),
            'json' => env('ATLAS_FORMAT_JSON', true),
            'markdown' => env('ATLAS_FORMAT_MARKDOWN', true),
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

## 📚 Documentation

For detailed documentation, examples, and advanced usage:

- 📚 [Full Documentation](docs/README.md)
- 🎯 [Examples](examples/README.md)
- 🔧 [Configuration](docs/configuration.md)
- 🧪 [Testing](docs/testing.md)
- 🗺️ [Architecture Mapping](docs/mapping.md)

## 💡 Examples

### Generate Complete Application Map

```bash
# Generate comprehensive application architecture
php artisan atlas:generate --type=all --format=mermaid --output=docs/

# Generate specific component maps
php artisan atlas:generate --type=models --format=mermaid
php artisan atlas:generate --type=services --format=markdown
php artisan atlas:generate --type=routes --format=mermaid
```

### Custom Architecture Analysis

```php
use LaravelAtlas\Facades\Atlas;

// Custom analysis workflow
$modelData = Atlas::scan('models', ['include_relationships' => true]);
$routeData = Atlas::scan('routes', ['include_middleware' => true]);

$markdownReport = Atlas::export('models', 'markdown', [
    'include_stats' => true,
    'detailed_sections' => true,
]);

file_put_contents('docs/architecture-analysis.md', $markdownReport);
```

### Interactive Architecture Explorer

```php
// Generate interactive HTML map
$htmlOutput = Atlas::export('all', 'html');
file_put_contents('public/atlas/explorer.html', $htmlOutput);
```

### CI/CD Integration

```bash
# In your CI/CD pipeline
php artisan atlas:generate --format=json --output=docs/architecture.json
php artisan atlas:generate --format=markdown --output=docs/ARCHITECTURE.md
```

Check out the [examples directory](examples) for more examples.

## 🧪 Testing

Laravel Atlas includes testing utilities and can be tested in your application:

```php
use Tests\TestCase;
use LaravelAtlas\Facades\Atlas;

class ArchitectureTest extends TestCase
{
    public function test_models_can_be_scanned(): void
    {
        $data = Atlas::scan('models');
        
        $this->assertIsArray($data);
        $this->assertArrayHasKey('type', $data);
        $this->assertEquals('models', $data['type']);
        $this->assertArrayHasKey('data', $data);
    }

    public function test_json_export_is_valid(): void
    {
        $json = Atlas::export('models', 'json');
        
        $decoded = json_decode($json, true);
        $this->assertIsArray($decoded);
    }
}
```

## 🔧 Requirements

- PHP: ^8.3
- Laravel: ^12.0
- Carbon: ^3.10

## 🚀 Performance

Laravel Atlas is optimized for performance:

- **Efficient Scanning**: Optimized file system scanning and parsing
- **Smart Caching**: Intelligent caching of analysis results
- **Memory Management**: Efficient memory usage for large applications
- **Incremental Updates**: Only scan changed files when possible

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

## 🔒 Security

If you discover a security vulnerability, please review our [Security Policy](SECURITY.md) before disclosing it.

## 📄 License

Laravel Atlas is open-sourced software licensed under the [MIT license](LICENSE.md).

---

**Made with ❤️ for the Laravel community**

### Resources

- [📖 Documentation](docs/README.md)
- [💬 Discussions](https://github.com/Grazulex/laravel-atlas/discussions)
- [🐛 Issue Tracker](https://github.com/Grazulex/laravel-atlas/issues)
- [📦 Packagist](https://packagist.org/packages/grazulex/laravel-atlas)

### Community Links

- [CODE_OF_CONDUCT.md](CODE_OF_CONDUCT.md) - Our code of conduct
- [CONTRIBUTING.md](CONTRIBUTING.md) - How to contribute
- [SECURITY.md](SECURITY.md) - Security policy
- [RELEASES.md](RELEASES.md) - Release notes and changelog
