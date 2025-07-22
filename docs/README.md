# Laravel Atlas Documentation

Laravel Atlas is a powerful Laravel package that scans your application to generate comprehensive architectural documentation. It analyzes various Laravel components and exports the results in multiple formats.

## 📑 Documentation Index

- [Installation & Configuration](installation.md) - Installation instructions and configuration options
- [Usage Guide](usage.md) - Basic usage and command-line interface  
- [Mappers](mappers.md) - Available component mappers and their options
- [Exporters](exporters.md) - Available export formats and customization
- [Programmatic API](api.md) - Using Laravel Atlas programmatically
- [Examples](../examples/README.md) - Practical examples and use cases

## 🚀 Quick Start

1. Install the package:
```bash
composer require grazulex/laravel-atlas --dev
```

2. Generate your first map:
```bash
php artisan atlas:generate
```

3. Explore the generated JSON output to understand your application structure.

## 💡 What Can Laravel Atlas Do?

Laravel Atlas can analyze and document **17 different component types**:

- **models** - Eloquent models with relationships, observers, and factories
- **routes** - Application routes with middleware and controllers
- **jobs** - Queued jobs and their properties
- **services** - Service classes and their dependencies  
- **controllers** - Controllers and their methods
- **events** - Application events and listeners
- **commands** - Artisan commands
- **middleware** - HTTP middleware
- **policies** - Authorization policies
- **resources** - API resources
- **notifications** - Notification classes
- **requests** - Form request classes
- **rules** - Custom validation rules
- **observers** - Eloquent model observers
- **listeners** - Event listeners
- **actions** - Action classes

## 📊 Export Formats

Generate documentation in multiple formats:

- **JSON** - Machine-readable data structure
- **Markdown** - Human-readable documentation
- **HTML** - Interactive web-based documentation with intelligent workflow
- **Image** - Visual diagrams and charts (PNG/JPG)
- **PDF** - Printable documentation reports
- **PHP** - Raw PHP data for advanced processing

## 🎯 Use Cases

- **Code Documentation** - Generate comprehensive application maps
- **Team Onboarding** - Visual architecture overviews for new developers  
- **Architecture Reviews** - Analyze application structure and dependencies
- **Legacy Code Analysis** - Understand complex existing applications
- **Compliance Reporting** - Generate architectural documentation

## 🤝 Contributing

See the main [CONTRIBUTING.md](../CONTRIBUTING.md) for guidelines on contributing to Laravel Atlas.

## 📄 License

Laravel Atlas is open-sourced software licensed under the [MIT license](../LICENSE.md).