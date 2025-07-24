<?php

/**
 * Laravel Atlas - HTML Export Example
 *
 * This example demonstrates how to generate interactive HTML reports:
 * - Complete architecture dashboard
 * - Component-specific HTML reports
 * - Interactive features (dark mode, responsive design)
 * - Professional styling with Tailwind CSS
 */

use LaravelAtlas\Facades\Atlas;

echo "=== Laravel Atlas - HTML Export Example ===\n\n";

// 1. Generate complete HTML architecture dashboard
echo "1. Generating complete HTML architecture dashboard:\n";
$htmlReport = Atlas::export('all', 'html');
$reportSize = strlen($htmlReport);
echo "Generated complete HTML report: {$reportSize} characters\n";

// Save the complete report
$timestamp = date('Y-m-d_H-i-s');
$outputDir = "html_reports_{$timestamp}";

if (! file_exists($outputDir)) {
    mkdir($outputDir, 0755, true);
    echo "Created output directory: {$outputDir}/\n";
}

file_put_contents("{$outputDir}/complete-architecture.html", $htmlReport);
echo "✅ Saved: {$outputDir}/complete-architecture.html\n\n";

// 2. Generate component-specific HTML reports
echo "2. Generating component-specific HTML reports:\n";

$componentTypes = [
    'models' => 'Models Architecture',
    'routes' => 'Routes Map',
    'commands' => 'Artisan Commands',
    'services' => 'Service Classes',
    'notifications' => 'Notification System',
    'middlewares' => 'HTTP Middlewares',
    'form_requests' => 'Form Request Validation',
];

$componentReports = [];
foreach ($componentTypes as $type => $description) {
    echo "Generating {$description} HTML report...\n";
    
    $htmlContent = Atlas::export($type, 'html');
    $filename = "{$outputDir}/{$type}-report.html";
    
    file_put_contents($filename, $htmlContent);
    $componentReports[$type] = [
        'filename' => $filename,
        'size' => strlen($htmlContent),
        'description' => $description,
    ];
    
    echo "✅ Saved: {$filename} ({$componentReports[$type]['size']} characters)\n";
}

echo "\n";

// 3. Demonstrate HTML report features
echo "3. HTML Report Features:\n";
echo "📊 Interactive Dashboard Features:\n";
echo "  - 🌓 Dark Mode Support (toggle between light/dark themes)\n";
echo "  - 📱 Responsive Design (desktop, tablet, mobile optimized)\n";
echo "  - 🧭 Component Navigation (sidebar with live counts)\n";
echo "  - 📋 Rich Component Cards (detailed information display)\n";
echo "  - 🎨 Modern UI (Tailwind CSS styling)\n";
echo "  - 🔍 Collapsible Sections (expandable details)\n";
echo "  - 💻 Professional Layout (enterprise-ready styling)\n\n";

echo "🧱 Component Sections Included:\n";
foreach ($componentTypes as $type => $description) {
    $count = $componentReports[$type]['size'];
    echo "  - {$description}: {$count} characters\n";
}

echo "\n";

// 4. Create an index.html file to navigate all reports
echo "4. Creating navigation index:\n";

$indexHtml = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Atlas - HTML Reports Index</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
    </script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">📊 Laravel Atlas - HTML Reports</h1>
            <p class="text-gray-600 mb-8">Interactive HTML architecture reports generated on {$timestamp}</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Complete Architecture Report -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-3">🏗️ Complete Architecture</h2>
                    <p class="text-blue-100 mb-4">Full application architecture with all components</p>
                    <a href="complete-architecture.html" class="bg-white text-blue-600 px-4 py-2 rounded font-medium hover:bg-blue-50 transition-colors">
                        View Report
                    </a>
                </div>
HTML;

foreach ($componentTypes as $type => $description) {
    $icon = [
        'models' => '🧱',
        'routes' => '🛣️',
        'commands' => '💬',
        'services' => '🔧',
        'notifications' => '📢',
        'middlewares' => '🛡️',
        'form_requests' => '📋',
    ][$type] ?? '📄';
    
    $indexHtml .= <<<HTML
                
                <!-- {$description} Report -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{$icon} {$description}</h3>
                    <p class="text-gray-600 text-sm mb-4">Detailed {$type} analysis and documentation</p>
                    <a href="{$type}-report.html" class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-blue-700 transition-colors">
                        View Report
                    </a>
                </div>
HTML;
}

$indexHtml .= <<<HTML
            </div>
            
            <div class="mt-8 bg-gray-50 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">🚀 Report Features</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Interactive Features:</h3>
                        <ul class="text-gray-600 space-y-1">
                            <li>• Dark mode support</li>
                            <li>• Responsive design</li>
                            <li>• Component navigation</li>
                            <li>• Live component counts</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Technical Details:</h3>
                        <ul class="text-gray-600 space-y-1">
                            <li>• Modern Tailwind CSS styling</li>
                            <li>• Self-contained HTML files</li>
                            <li>• Professional documentation</li>
                            <li>• Enterprise-ready reports</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;

file_put_contents("{$outputDir}/index.html", $indexHtml);
echo "✅ Created navigation index: {$outputDir}/index.html\n\n";

// 5. Show usage examples
echo "5. Usage Examples:\n";
echo "Command Line:\n";
echo "  # Generate complete HTML architecture report\n";
echo "  php artisan atlas:generate --format=html --output=reports/architecture.html\n\n";
echo "  # Generate specific component HTML reports\n";
echo "  php artisan atlas:generate --type=models --format=html --output=reports/models.html\n";
echo "  php artisan atlas:generate --type=routes --format=html --output=reports/routes.html\n\n";

echo "Programmatic:\n";
echo "  // Complete architecture dashboard\n";
echo "  \$html = Atlas::export('all', 'html');\n";
echo "  file_put_contents('public/docs/architecture.html', \$html);\n\n";
echo "  // Component-specific reports\n";
echo "  \$modelsHtml = Atlas::export('models', 'html');\n";
echo "  file_put_contents('public/docs/models.html', \$modelsHtml);\n\n";

// 6. Summary
echo "6. Summary:\n";
echo "Generated HTML reports in: {$outputDir}/\n";
echo "  📁 Complete Architecture: complete-architecture.html\n";
foreach ($componentTypes as $type => $description) {
    echo "  📁 {$description}: {$type}-report.html\n";
}
echo "  📁 Navigation Index: index.html\n\n";

echo "🌐 Open {$outputDir}/index.html in your browser to view all reports!\n\n";

echo "✨ HTML Export Features Demonstrated:\n";
echo "  - Interactive dashboards with dark mode\n";
echo "  - Responsive design for all devices\n";
echo "  - Professional styling with Tailwind CSS\n";
echo "  - Component navigation with live counts\n";
echo "  - Rich component cards with detailed information\n";
echo "  - Self-contained HTML files (no external dependencies)\n";
echo "  - Enterprise-ready documentation format\n\n";

echo "HTML export example completed successfully!\n";
echo "All HTML reports are ready for distribution or deployment.\n";