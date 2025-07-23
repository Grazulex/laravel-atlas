# Laravel Atlas Documentation

Welcome to the Laravel Atlas documentation. This directory contains comprehensive guides, API references, and examples for using Laravel Atlas.

## 📚 Documentation Structure

```
docs/
├── README.md              # This file - Documentation index
├── installation.md        # Installation and setup guide
├── configuration.md       # Configuration options
├── api.md                # API reference
├── troubleshooting.md    # Common issues and solutions
├── examples/             # Detailed examples and tutorials
└── contributing.md       # Contributing to documentation
```

## 🚀 Quick Start

1. **[Installation Guide](installation.md)** - Get Laravel Atlas installed and configured
2. **[Configuration](configuration.md)** - Customize Atlas for your needs
3. **[Examples](../examples/)** - Practical usage examples
4. **[API Reference](api.md)** - Complete API documentation

## 📖 Main Documentation Sections

### Getting Started
- [Installation and Setup](installation.md)
- [Basic Configuration](configuration.md)
- [Your First Atlas Map](../examples/basic-usage/)

### Core Features
- [Component Mapping](api.md#component-mapping)
- [Export Formats](../examples/export-formats/)
- [Advanced Analysis](../examples/advanced-scanning/)

### Integration
- [CI/CD Integration](../examples/integration/)
- [Custom Extensions](../examples/custom-analysis/)
- [Performance Optimization](troubleshooting.md#performance)

### Reference
- [Complete API Reference](api.md)
- [Configuration Options](configuration.md)
- [Troubleshooting Guide](troubleshooting.md)

## 🔍 Key Concepts

### Component Types
Laravel Atlas can analyze various Laravel components:
- **Models** - Eloquent models with relationships and scopes
- **Routes** - Application routes with middleware and controllers
- **Services** - Service classes and their dependencies
- **Commands** - Artisan commands and jobs
- **Events** - Event classes and listeners
- **Controllers** - Controller classes and their methods

### Export Formats
Multiple export formats for different use cases:
- **JSON** - Machine-readable data for APIs and processing
- **Markdown** - Human-readable documentation
- **HTML** - Interactive web reports with navigation
- **PDF** - Professional reports for documentation
- **Image** - Visual diagrams and architecture maps

### Analysis Depth
Configure how deeply Atlas analyzes your application:
- **Shallow** - Basic structure and relationships
- **Medium** - Includes methods, properties, and dependencies
- **Deep** - Comprehensive analysis including code complexity

## 💡 Usage Patterns

### Development Workflow
```bash
# Quick architecture check during development
php artisan atlas:generate --type=models --format=json --cache

# Generate documentation for code review
php artisan atlas:generate --type=all --format=html --output=public/review.html

# Update project documentation
php artisan atlas:generate --type=all --format=markdown --output=docs/ARCHITECTURE.md
```

### CI/CD Integration
```bash
# Automated documentation generation
php artisan atlas:generate --type=all --format=json --output=docs/architecture.json
php artisan atlas:generate --type=all --format=html --output=public/docs/atlas.html
```

### Team Onboarding
```bash
# Generate comprehensive overview for new team members
php artisan atlas:generate --type=all --format=html --detailed --output=public/onboarding.html
```

## 🔧 Configuration Overview

Laravel Atlas can be configured through:

1. **Configuration File** - `config/atlas.php`
2. **Environment Variables** - `.env` file settings
3. **Command Arguments** - Runtime configuration
4. **Programmatic API** - Direct configuration in code

Key configuration areas:
- **Scanning Options** - What components to analyze
- **Export Settings** - Output formats and locations
- **Performance Tuning** - Caching and optimization
- **Security** - Access control and sensitive data handling

## 📊 Performance Considerations

### Large Applications
- Use component-specific scanning (`--type=models`)
- Enable caching (`--cache`)
- Use shallow analysis for quick checks
- Consider parallel processing for CI/CD

### Memory Usage
- Set appropriate memory limits
- Use streaming for large datasets
- Cache intermediate results
- Clean up temporary files

## 🤝 Contributing

We welcome contributions to Laravel Atlas documentation:

1. **Improve Existing Docs** - Fix typos, clarify explanations
2. **Add Examples** - Share your use cases and patterns
3. **Create Tutorials** - Help others learn Atlas
4. **Report Issues** - Help us identify documentation gaps

See [Contributing Guide](contributing.md) for detailed instructions.

## 🔗 External Resources

- **Package Repository**: [GitHub](https://github.com/Grazulex/laravel-atlas)
- **Packagist**: [Laravel Atlas Package](https://packagist.org/packages/grazulex/laravel-atlas)
- **Discussions**: [GitHub Discussions](https://github.com/Grazulex/laravel-atlas/discussions)
- **Issues**: [Bug Reports & Feature Requests](https://github.com/Grazulex/laravel-atlas/issues)

## 📝 Documentation Status

| Section | Status | Last Updated |
|---------|--------|--------------|
| Installation | ✅ Complete | 2024-07-23 |
| Configuration | ✅ Complete | 2024-07-23 |
| API Reference | ✅ Complete | 2024-07-23 |
| Examples | ✅ Complete | 2024-07-23 |
| Troubleshooting | ✅ Complete | 2024-07-23 |

---

**Need Help?** Check the [Troubleshooting Guide](troubleshooting.md) or [open an issue](https://github.com/Grazulex/laravel-atlas/issues).