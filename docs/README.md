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

Laravel Atlas can analyze and document:

- **Models** - Eloquent models with relationships, observers, and factories
- **Routes** - Application routes with middleware and controllers
- **Jobs** - Queued jobs and their properties
- **Services** - Service classes and their dependencies  
- **Controllers** - Controllers and their methods
- **Events** - Application events and listeners
- **Commands** - Artisan commands
- **Middleware** - HTTP middleware
- **Policies** - Authorization policies
- **Resources** - API resources
- **Notifications** - Notification classes
- **Requests** - Form request classes
- **Rules** - Custom validation rules

## 📊 Export Formats

Generate documentation in multiple formats:

- **JSON** - Machine-readable data structure
- **Markdown** - Human-readable documentation
- **Mermaid** - Visual diagrams and flowcharts
- **HTML** - Interactive web-based documentation
- **PDF** - Printable documentation reports

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