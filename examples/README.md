# Laravel Atlas Examples

This directory contains practical examples demonstrating how to use Laravel Atlas to map and visualize your Laravel application architecture.

## 📁 Directory Structure

```
examples/
├── README.md                    # This file
├── basic-usage/                 # Basic usage examples
├── advanced-scanning/           # Advanced scanning techniques  
├── export-formats/              # Different export format examples
├── integration/                 # CI/CD and integration examples
└── custom-analysis/             # Custom analysis examples
```

## 🚀 Quick Start Examples

### Basic Application Mapping

```bash
# Generate a complete application map
php artisan atlas:generate

# Generate specific component maps
php artisan atlas:generate --type=models --format=markdown
php artisan atlas:generate --type=routes --format=json
php artisan atlas:generate --type=services --format=html
```

### Programmatic Usage

```php
use LaravelAtlas\Facades\Atlas;

// Scan specific component types
$modelData = Atlas::scan('models');
$routeData = Atlas::scan('routes');
$serviceData = Atlas::scan('services');

// Export to different formats
$jsonOutput = Atlas::export('models', 'json');
$markdownDocs = Atlas::export('routes', 'markdown');
$htmlReport = Atlas::export('services', 'html');
```

## 📚 Available Examples

### [Basic Usage](basic-usage/)
- Simple model scanning
- Route mapping
- Basic exports
- Command-line interface examples

### [Advanced Scanning](advanced-scanning/)
- Custom configuration options
- Filtering and analysis
- Performance optimization
- Complex application scenarios

### [Export Formats](export-formats/)
- JSON exports for data processing
- Markdown documentation generation
- Interactive HTML reports
- PDF report generation
- Image diagram creation

### [Integration Examples](integration/)
- CI/CD pipeline integration
- Automated documentation updates
- Testing with Atlas
- Custom workflows

### [Custom Analysis](custom-analysis/)
- Extending Atlas functionality
- Custom mappers and exporters
- Advanced configuration
- Plugin development

## 🔧 Requirements

- PHP ^8.3
- Laravel ^12.0
- Laravel Atlas package installed

## 🏃‍♂️ Running Examples

Each example directory contains its own README with specific instructions. Generally:

1. Navigate to the example directory
2. Follow the setup instructions in the example's README
3. Run the provided commands or scripts

## 💡 Tips for Best Results

- **Start Simple**: Begin with basic-usage examples
- **Understand Your App**: Different examples work better for different application architectures
- **Customize**: Adapt examples to your specific needs
- **Performance**: Use filtering and caching for large applications

## 🤝 Contributing Examples

We welcome contributions of new examples! Please:

1. Create a new directory for your example
2. Include a detailed README with setup and usage instructions
3. Provide sample code and expected outputs
4. Test your example thoroughly
5. Submit a pull request

## 📖 Further Documentation

- [Main Documentation](../README.md)
- [API Reference](../docs/api.md)
- [Configuration Guide](../docs/configuration.md)
- [Troubleshooting](../docs/troubleshooting.md)

---

**Need help?** Check out the [discussions](https://github.com/Grazulex/laravel-atlas/discussions) or open an [issue](https://github.com/Grazulex/laravel-atlas/issues).