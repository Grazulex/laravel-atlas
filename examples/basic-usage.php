<?php

/**
 * Laravel Atlas - Basic Usage Example
 *
 * This example demonstrates the basic functionality of Laravel Atlas:
 * - Scanning different component types
 * - Exporting to different formats
 * - Working with the Atlas facade
 */

use LaravelAtlas\Facades\Atlas;

echo "=== Laravel Atlas - Basic Usage Example ===\n\n";

// 1. Scan all available component types
echo "1. Scanning all available component types:\n";
$allData = Atlas::scan('all');

echo "Available component types:\n";
foreach ($allData as $type => $data) {
    $count = is_array($data) && isset($data['count']) ? $data['count'] : 0;
    echo "- {$type}: {$count} components found\n";
}
echo "\n";

// 2. Scan specific component types
echo "2. Scanning specific component types:\n";

$componentTypes = ['models', 'routes', 'commands', 'services', 'notifications', 'middlewares', 'form_requests'];

foreach ($componentTypes as $type) {
    $data = Atlas::scan($type);
    $count = $data['count'] ?? 0;
    echo "- {$type}: {$count} components\n";
}
echo "\n";

// 3. Export to different formats
echo "3. Exporting to different formats:\n";

// JSON export (default)
$jsonData = Atlas::export('models', 'json');
echo '- JSON export length: ' . strlen($jsonData) . " characters\n";

// Markdown export
$markdownData = Atlas::export('routes', 'markdown');
echo '- Markdown export length: ' . strlen($markdownData) . " characters\n";

// HTML export
$htmlData = Atlas::export('all', 'html');
echo '- HTML export length: ' . strlen($htmlData) . " characters\n";

// PHP export
$phpData = Atlas::export('commands', 'php');
echo '- PHP export length: ' . strlen($phpData) . " characters\n";

echo "\n";

// 4. Working with options
echo "4. Scanning with options:\n";

$modelsWithOptions = Atlas::scan('models', [
    'include_relationships' => true,
    'include_observers' => true,
    'include_factories' => true,
]);
echo '- Models with relationships: ' . ($modelsWithOptions['count'] ?? 0) . " found\n";

$routesWithOptions = Atlas::scan('routes', [
    'include_middleware' => true,
    'include_controllers' => true,
    'group_by_prefix' => true,
]);
echo '- Routes with middleware: ' . ($routesWithOptions['count'] ?? 0) . " found\n";

echo "\n";

// 5. Generate HTML export for interactive documentation
echo "5. Generating HTML export for interactive documentation:\n";

$htmlReport = Atlas::export('all', 'html');
echo '- HTML report generated: ' . strlen($htmlReport) . " characters\n";

// You can save this to a file:
// file_put_contents('atlas-report.html', $htmlReport);
echo "- To save: file_put_contents('atlas-report.html', \$htmlReport)\n";
echo "- The HTML report includes dark mode, responsive design, and interactive navigation\n";

echo "\nBasic usage example completed successfully!\n";
echo "💡 Try the HTML export example for interactive dashboard features!\n";
