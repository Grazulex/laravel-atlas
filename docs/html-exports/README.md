# HTML Export Examples

This directory contains examples and documentation for Laravel Atlas HTML export functionality.

## Overview

Laravel Atlas provides comprehensive HTML export capabilities that generate interactive, responsive dashboards for visualizing your Laravel application architecture.

## HTML Export Features

### 🌓 Interactive Dashboard
- **Dark Mode Support** - Toggle between light and dark themes
- **Responsive Design** - Works seamlessly on desktop, tablet, and mobile devices
- **Component Navigation** - Easy sidebar navigation between component types
- **Live Component Counts** - Real-time component counts in navigation sidebar

### 📊 Visual Components
- **Rich Component Cards** - Detailed cards showing component information
- **Collapsible Sections** - Expandable sections for detailed information
- **Syntax Highlighting** - Code examples with proper highlighting
- **Modern UI** - Built with Tailwind CSS for professional appearance

### 🧱 Component Sections

The HTML export includes the following component types:

- **🧱 Models** - Eloquent models with relationships, attributes, and metadata
- **🛣️ Routes** - Application routes with middleware, controllers, and HTTP methods
- **💬 Commands** - Artisan commands with signatures, arguments, and options
- **🔧 Services** - Service classes with methods, dependencies, and flow analysis
- **📢 Notifications** - Notification classes with channels, methods, and dependencies
- **🛡️ Middlewares** - HTTP middleware with parameters, dependencies, and flow patterns
- **📋 Form Requests** - Form request validation classes with rules, authorization, and attributes

## Examples

### Basic HTML Export

```bash
# Generate HTML export for all components
php artisan atlas:export --format=html --output=reports/architecture.html

# Generate HTML export for specific component type
php artisan atlas:export --type=models --format=html --output=reports/models.html
```

### Programmatic HTML Export

```php
use LaravelAtlas\Facades\Atlas;

// Generate complete HTML architecture report
$htmlReport = Atlas::export('all', 'html');
file_put_contents('public/reports/architecture.html', $htmlReport);

// Generate component-specific HTML reports
$modelsHtml = Atlas::export('models', 'html');
file_put_contents('public/reports/models.html', $modelsHtml);

$routesHtml = Atlas::export('routes', 'html');
file_put_contents('public/reports/routes.html', $routesHtml);
```

## Example HTML Output

The HTML export generates a complete, self-contained HTML file with:

1. **Professional Header** - Project name, description, and generation timestamp
2. **Navigation Sidebar** - Component type navigation with live counts
3. **Component Sections** - Detailed sections for each component type
4. **Interactive Features** - Dark mode toggle, responsive design, collapsible sections
5. **Modern Styling** - Tailwind CSS styling with professional appearance

### Sample HTML Structure

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Project Name – Atlas – Components Overview</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Dark mode and responsive configuration -->
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <!-- Navigation Header with Dark Mode Toggle -->
    <nav class="bg-white dark:bg-gray-800 shadow-lg">
        <!-- Project info and controls -->
    </nav>
    
    <!-- Main Container -->
    <div class="flex min-h-screen">
        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-white dark:bg-gray-800">
            <!-- Component type navigation with counts -->
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 p-6">
            <!-- Component sections with detailed cards -->
        </main>
    </div>
</body>
</html>
```

## File Examples

This directory contains:

- [sample-full-report.html](sample-full-report.html) - Complete application architecture report
- [sample-models-report.html](sample-models-report.html) - Models-only report example
- [sample-routes-report.html](sample-routes-report.html) - Routes-only report example

## Usage in CI/CD

```bash
# Generate HTML reports for documentation
php artisan atlas:export --format=html --output=public/docs/architecture.html

# Generate component-specific reports
php artisan atlas:export --type=models --format=html --output=public/docs/models.html
php artisan atlas:export --type=routes --format=html --output=public/docs/routes.html
php artisan atlas:export --type=commands --format=html --output=public/docs/commands.html
```

## Customization

The HTML export uses Laravel Blade templates that can be customized:

- Layout template: `resources/views/exports/layout.blade.php`
- Component cards: `resources/views/exports/partials/`
- Styling: Tailwind CSS with dark mode support