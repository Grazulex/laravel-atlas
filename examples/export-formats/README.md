# Export Formats Examples

This directory demonstrates the various export formats available in Laravel Atlas and how to use them effectively.

## 📋 Available Export Formats

- **JSON** - Machine-readable data for APIs and processing
- **Markdown** - Human-readable documentation
- **HTML** - Interactive web reports
- **PDF** - Professional reports and documentation
- **Image** - Visual diagrams (PNG, JPG, SVG)
- **PHP** - Raw PHP arrays for custom processing

## 📁 Directory Structure

```
export-formats/
├── README.md                 # This file
├── json-exports.php          # JSON export examples
├── markdown-exports.php      # Markdown documentation generation
├── html-exports.php          # Interactive HTML reports
├── pdf-exports.php           # PDF report generation
├── image-exports.php         # Visual diagram creation
├── php-exports.php           # Raw PHP data exports
└── sample-outputs/           # Example output files
    ├── sample-models.json
    ├── sample-routes.md
    ├── sample-interactive.html
    ├── sample-report.pdf
    └── sample-diagram.png
```

## 🚀 Quick Examples

### JSON Export
```bash
php artisan atlas:generate --format=json --output=storage/atlas.json
```

### Markdown Documentation
```bash
php artisan atlas:generate --format=markdown --output=docs/ARCHITECTURE.md
```

### Interactive HTML Report
```bash
php artisan atlas:generate --format=html --output=public/atlas.html
```

### PDF Report
```bash
php artisan atlas:generate --format=pdf --output=reports/architecture.pdf
```

### Visual Diagram
```bash
php artisan atlas:generate --format=image --output=docs/architecture.png
```

## 💡 Use Cases by Format

### JSON Format
- **API Integration**: Consume Atlas data in other applications
- **Data Processing**: Further analysis with custom scripts
- **CI/CD**: Automated documentation pipelines
- **Version Control**: Track architectural changes over time

### Markdown Format
- **Documentation**: Generate comprehensive project documentation
- **README Files**: Create architectural overviews
- **Wiki Pages**: Integrate with documentation systems
- **Code Reviews**: Architectural change documentation

### HTML Format
- **Team Reviews**: Interactive exploration of architecture
- **Onboarding**: Visual guides for new developers
- **Presentations**: Web-based architectural presentations
- **Dashboard**: Live architecture monitoring

### PDF Format  
- **Reports**: Professional architectural documentation
- **Compliance**: Formal documentation requirements
- **Archives**: Long-term documentation storage
- **Presentations**: Printable architectural overviews

### Image Format
- **Diagrams**: Visual architecture representations
- **Presentations**: Embed in slides and documents
- **Social Media**: Share architectural insights
- **Documentation**: Visual aids in written documentation

## ⚙️ Format-Specific Options

### JSON Options
```bash
# Pretty-printed JSON
php artisan atlas:generate --format=json --pretty

# Minified JSON
php artisan atlas:generate --format=json --minify

# Include metadata
php artisan atlas:generate --format=json --include-metadata
```

### Markdown Options
```bash
# Include table of contents
php artisan atlas:generate --format=markdown --toc

# Detailed sections
php artisan atlas:generate --format=markdown --detailed

# Custom template
php artisan atlas:generate --format=markdown --template=custom
```

### HTML Options
```bash
# Modern theme
php artisan atlas:generate --format=html --theme=modern

# Include search functionality
php artisan atlas:generate --format=html --searchable

# Responsive design
php artisan atlas:generate --format=html --responsive
```

### PDF Options
```bash
# Professional template
php artisan atlas:generate --format=pdf --template=professional

# Custom page size
php artisan atlas:generate --format=pdf --page-size=A4

# Include cover page
php artisan atlas:generate --format=pdf --cover-page
```

### Image Options
```bash
# PNG format with high resolution
php artisan atlas:generate --format=image --type=png --dpi=300

# SVG for scalable diagrams
php artisan atlas:generate --format=image --type=svg

# Custom dimensions
php artisan atlas:generate --format=image --width=1920 --height=1080
```

## 🔧 Advanced Usage

### Multiple Format Export
```php
use LaravelAtlas\Facades\Atlas;

// Export to multiple formats simultaneously
$data = Atlas::scan('models');

file_put_contents('docs/models.json', Atlas::export('models', 'json'));
file_put_contents('docs/models.md', Atlas::export('models', 'markdown'));
file_put_contents('docs/models.html', Atlas::export('models', 'html'));
```

### Custom Processing Pipeline
```php
// JSON for processing, HTML for viewing
$jsonData = Atlas::export('all', 'json');
$processedData = processArchitectureData(json_decode($jsonData, true));

// Generate custom HTML with processed data
$customHtml = generateCustomReport($processedData);
file_put_contents('public/custom-atlas.html', $customHtml);
```

## 📊 Format Comparison

| Format | Best For | File Size | Processing | Interactivity |
|--------|----------|-----------|------------|---------------|
| JSON | APIs, Processing | Small | High | None |
| Markdown | Documentation | Small | Medium | None |
| HTML | Web Viewing | Medium | Low | High |
| PDF | Reports | Large | Low | None |
| Image | Diagrams | Medium | Low | None |
| PHP | Custom Processing | Small | High | None |

## 🎯 Best Practices

### Performance
- Use JSON for data processing
- Cache HTML reports for repeated viewing
- Generate images at appropriate resolution
- Use PDF for final documentation only

### Maintenance
- Automate generation in CI/CD pipelines
- Version control generated documentation
- Update formats when architecture changes
- Test format compatibility across systems

### Accessibility
- Provide multiple format options
- Include metadata in all formats
- Use semantic markup in HTML exports
- Ensure PDF reports are searchable

---

**Next Steps:** Explore the individual format examples in this directory to see detailed implementations.