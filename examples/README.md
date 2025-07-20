# Laravel Atlas Examples

This directory contains practical examples demonstrating how to use Laravel Atlas for various architectural analysis and documentation tasks.

## 📁 Example Structure

### Basic Examples
- [Basic Usage](basic-usage.md) - Getting started with Laravel Atlas
- [Command Line Examples](command-line.md) - Using the atlas:generate command
- [Programmatic Usage](programmatic.md) - Using the PHP API

### Component-Specific Examples  
- [Model Analysis](models.md) - Analyzing Eloquent models and relationships
- [Route Mapping](routes.md) - Mapping application routes and middleware
- [Service Discovery](services.md) - Analyzing service layer architecture

### Export Format Examples
- [JSON Exports](exports/json.md) - Working with JSON output
- [Markdown Documentation](exports/markdown.md) - Generating readable documentation
- [Mermaid Diagrams](exports/mermaid.md) - Creating visual architecture diagrams
- [HTML Reports](exports/html.md) - Interactive documentation

### Advanced Examples
- [Custom Analysis](advanced/custom-analysis.md) - Building custom architectural analysis
- [CI/CD Integration](advanced/cicd.md) - Integrating with build pipelines
- [Performance Optimization](advanced/performance.md) - Optimizing for large applications

## 🚀 Quick Start Example

Generate a complete architecture map of your application:

```bash
# Generate JSON map
php artisan atlas:generate --format=json --output=docs/architecture.json

# Generate readable documentation  
php artisan atlas:generate --format=markdown --detailed --output=docs/ARCHITECTURE.md

# Generate visual diagram
php artisan atlas:generate --format=mermaid --output=docs/architecture.mmd
```

## 🔧 Common Use Cases

### 1. Documentation Generation
```bash
# Generate comprehensive documentation
php artisan atlas:generate --type=all --format=markdown --detailed --output=docs/
```

### 2. Architecture Review
```bash
# Focus on key architectural components
php artisan atlas:generate --type=models --format=mermaid --output=diagrams/models.mmd
php artisan atlas:generate --type=services --format=markdown --output=docs/services.md
```

### 3. API Documentation
```php
use LaravelAtlas\Facades\Atlas;

// Generate API endpoint documentation
$routeData = Atlas::scan('routes', [
    'include_controllers' => true,
    'include_middleware' => true
]);

$apiDocs = Atlas::export('routes', 'markdown', [
    'include_toc' => true,
    'detailed_sections' => true
]);
```

### 4. Team Onboarding
```bash
# Create interactive onboarding documentation
php artisan atlas:generate --format=html --output=public/team/architecture.html
```

## 💡 Tips for Effective Usage

### Start Small
Begin with specific component types:
```bash
php artisan atlas:generate --type=models
php artisan atlas:generate --type=routes  
php artisan atlas:generate --type=controllers
```

### Use Appropriate Formats
- **JSON** - For data processing and API integration
- **Markdown** - For documentation and README files
- **Mermaid** - For visual diagrams and presentations
- **HTML** - For interactive exploration and sharing
- **PDF** - For reports and compliance documentation

### Combine with Version Control
```bash
# Generate documentation and commit
php artisan atlas:generate --format=markdown --output=docs/ARCHITECTURE.md
git add docs/ARCHITECTURE.md
git commit -m "Update architecture documentation"
```

### Automate Generation
Add to your deployment scripts:
```bash
#!/bin/bash
# deploy.sh
php artisan atlas:generate --format=json --output=public/api/architecture.json
php artisan atlas:generate --format=html --output=public/docs/architecture.html
```

## 🤝 Contributing Examples

Have a useful Laravel Atlas example? We'd love to include it! 

1. Create your example file in the appropriate subdirectory
2. Follow the existing format and structure
3. Include clear explanations and code comments
4. Test your examples to ensure they work
5. Submit a pull request

## 📄 Example Template

When creating new examples, use this template:

```markdown
# Example Title

Brief description of what this example demonstrates.

## Prerequisites

- List any requirements
- Mention Laravel Atlas setup

## Code Example

\```php
// Your example code here
\```

## Expected Output

\```
Show what the output should look like
\```

## Explanation

Explain how the example works and key concepts.

## Related Examples

- Link to related examples
- Reference relevant documentation
```

## 🔗 External Resources

- [Laravel Atlas Documentation](../docs/)
- [Mermaid Documentation](https://mermaid-js.github.io/mermaid/)
- [Laravel Documentation](https://laravel.com/docs)
- [Markdown Guide](https://www.markdownguide.org/)

---

**Need help?** Check our [documentation](../docs/) or open an issue on GitHub.