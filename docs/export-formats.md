# Export Formats

Laravel Atlas supports 3 export formats for different use cases and workflows.

## Available Formats

### 1. JSON Export
**Best for**: Data processing, API integration, programmatic analysis

```bash
# Generate JSON export
php artisan atlas:export --format=json --output=storage/atlas/map.json

# Programmatic usage
$jsonData = Atlas::export('models', 'json');
$data = json_decode($jsonData, true);
```

**Features:**
- Machine-readable format
- Complete data structure
- Perfect for API consumption
- Suitable for custom processing

### 2. HTML Export ⭐
**Best for**: Interactive documentation, team reviews, visual presentations

```bash
# Generate complete HTML dashboard
php artisan atlas:export --format=html --output=public/docs/architecture.html

# Component-specific HTML reports
php artisan atlas:export --type=models --format=html --output=public/docs/models.html
```

**Features:**
- 🌓 **Dark Mode Support** - Toggle between light and dark themes
- 📱 **Responsive Design** - Works on desktop, tablet, and mobile devices
- 🧭 **Interactive Navigation** - Sidebar navigation with live component counts
- 📊 **Rich Component Cards** - Detailed visual component information
- 🎨 **Modern UI** - Professional Tailwind CSS styling
- 🔍 **Collapsible Sections** - Expandable details for complex information
- 💻 **Self-contained** - No external dependencies required

**Sample HTML Output:**
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Project Name – Atlas – Architecture Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Dark mode and responsive configuration -->
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <!-- Navigation with dark mode toggle -->
    <!-- Sidebar with component navigation -->
    <!-- Main content with component cards -->
</body>
</html>
```

### 3. PDF Export 📄 **NEW**
**Best for**: Professional documentation, presentations, compliance reports

```bash
# Generate PDF documentation
php artisan atlas:export --format=pdf --output=docs/ARCHITECTURE.pdf

# Programmatic usage
$pdfDoc = Atlas::export('routes', 'pdf');
file_put_contents('docs/routes.pdf', $pdfDoc);
```

**Features:**
- 📄 **Professional Layout** - Clean, enterprise-ready document formatting
- 📊 **Comprehensive Coverage** - All 16 component types in structured sections
- 🎨 **Optimized for Print** - A4 format with proper page breaks and typography
- 📝 **Complete Documentation** - Detailed component information with metadata
- 🔧 **Self-contained** - Complete PDF files ready for sharing and archiving

**Requirements:**
- `dompdf/dompdf` package (included as suggested dependency)
- `ext-gd` PHP extension for image processing

## Export Examples

### Complete Application Export

```bash
# Export all components to different formats
php artisan atlas:export --format=json --output=reports/architecture.json
php artisan atlas:export --format=html --output=reports/architecture.html
php artisan atlas:export --format=pdf --output=reports/ARCHITECTURE.pdf
```

### Component-Specific Exports

```bash
# Models documentation
php artisan atlas:export --type=models --format=html --output=docs/models.html
php artisan atlas:export --type=models --format=pdf --output=docs/models.pdf

# Routes mapping
php artisan atlas:export --type=routes --format=html --output=docs/routes.html
php artisan atlas:export --type=routes --format=json --output=api/routes.json

# Services documentation
php artisan atlas:export --type=services --format=html --output=docs/services.html
php artisan atlas:export --type=services --format=pdf --output=docs/services.pdf

# All 16 component types
php artisan atlas:export --type=events --format=html --output=docs/events.html
php artisan atlas:export --type=controllers --format=pdf --output=docs/controllers.pdf
php artisan atlas:export --type=jobs --format=json --output=api/jobs.json
php artisan atlas:export --type=actions --format=html --output=docs/actions.html
php artisan atlas:export --type=policies --format=pdf --output=docs/policies.pdf
php artisan atlas:export --type=rules --format=html --output=docs/rules.html
php artisan atlas:export --type=listeners --format=json --output=api/listeners.json
php artisan atlas:export --type=observers --format=pdf --output=docs/observers.pdf
```

### Programmatic Export

```php
use LaravelAtlas\Facades\Atlas;

// Generate comprehensive HTML documentation
$htmlReport = Atlas::export('all', 'html');
file_put_contents('public/atlas/architecture.html', $htmlReport);

// Generate comprehensive PDF documentation
$pdfReport = Atlas::export('all', 'pdf');
file_put_contents('docs/architecture.pdf', $pdfReport);

// Generate component-specific reports for all 16 component types
$components = ['models', 'routes', 'commands', 'services', 'notifications', 'middlewares', 
               'form_requests', 'events', 'controllers', 'resources', 'jobs', 'actions', 
               'policies', 'rules', 'listeners', 'observers'];

foreach ($components as $component) {
    // HTML reports for visual documentation
    $html = Atlas::export($component, 'html');
    file_put_contents("public/docs/{$component}.html", $html);
    
    // PDF for professional documentation
    $pdf = Atlas::export($component, 'pdf');
    file_put_contents("docs/pdf/{$component}.pdf", $pdf);
    
    // JSON for API consumption
    $json = Atlas::export($component, 'json');
    file_put_contents("api/atlas/{$component}.json", $json);
}
```

## HTML Export Features in Detail

### Interactive Dashboard
The HTML export creates a complete interactive dashboard with:

1. **Professional Header**
   - Project name and description
   - Generation timestamp
   - Dark mode toggle

2. **Navigation Sidebar**
   - Component type navigation
   - Live component counts
   - Easy section switching

3. **Component Sections**
   - Detailed component cards
   - Syntax-highlighted code examples
   - Collapsible detailed information
   - Responsive layout

4. **Modern Styling**
   - Tailwind CSS framework
   - Dark mode support
   - Professional appearance
   - Mobile-optimized

### Example HTML Component Card

```html
<div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
    <div class="flex items-center justify-between mb-2">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Model</h3>
        <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded text-sm">
            Eloquent Model
        </span>
    </div>
    <p class="text-gray-600 dark:text-gray-300 text-sm mb-3">User authentication and profile management</p>
    
    <!-- Detailed information sections -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Attributes, relationships, methods, etc. -->
    </div>
</div>
```

## PDF Export Features in Detail

### Professional Documentation
The PDF export creates enterprise-ready documentation with:

1. **Document Structure**
   - Professional header with project information
   - Table of contents with component sections
   - Proper page breaks and formatting
   - A4 optimized layout

2. **Component Documentation**
   - Structured sections for each component type
   - Detailed component information and metadata
   - Code examples and relationships
   - Dependency mapping

3. **Typography and Layout**
   - Clean, readable fonts
   - Professional spacing and margins
   - Consistent styling throughout
   - Print-optimized formatting

4. **Self-contained Document**
   - Complete PDF with all information
   - No external dependencies
   - Suitable for sharing and archiving
   - Compatible with document management systems

## Use Cases by Format

### Development Teams
- **HTML**: Team reviews, architecture presentations, onboarding documentation
- **PDF**: Professional presentations, compliance reports, archived documentation
- **JSON**: API integration, automated analysis, data processing

### CI/CD Integration
```bash
# Generate documentation in CI/CD pipeline
php artisan atlas:export --format=html --output=public/docs/architecture.html
php artisan atlas:export --format=json --output=api/architecture.json
php artisan atlas:export --format=pdf --output=docs/ARCHITECTURE.pdf

# Deploy HTML documentation
cp public/docs/architecture.html /var/www/html/docs/
```

### Enterprise Documentation
```bash
# Generate comprehensive documentation suite
php artisan atlas:export --format=html --output=enterprise/architecture-dashboard.html
php artisan atlas:export --format=pdf --output=enterprise/architecture-report.pdf
php artisan atlas:export --format=json --output=enterprise/architecture-data.json
```

## Customization

All export formats can be customized:

- **HTML**: Modify Blade templates in `resources/views/exports/`
- **PDF**: Custom styling and layout options
- **JSON**: Data structure customization

See the [Testing Architecture Guide](testing-architecture.md) for detailed customization options.