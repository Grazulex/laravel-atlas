<?php

/**
 * Laravel Atlas - Controllers Analysis Example
 * 
 * This example demonstrates how to analyze Laravel controller classes
 * using Laravel Atlas.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use LaravelAtlas\Facades\Atlas;

echo "🔍 Laravel Atlas - Controllers Analysis Example\n";
echo "===============================================\n\n";

// Example 1: Basic controllers scanning
echo "1. Scanning controllers in the application...\n";
try {
    $controllersData = Atlas::scan('controllers');
    
    echo "   ✅ Found " . count($controllersData['data']) . " controller classes\n";
    echo "   📊 Controller analysis completed\n\n";
    
    // Display some controller information
    if (!empty($controllersData['data'])) {
        echo "   📋 Controller Classes Found:\n";
        foreach (array_slice($controllersData['data'], 0, 3) as $controller) {
            echo "      • " . ($controller['name'] ?? 'Unknown') . "\n";
            if (isset($controller['actions'])) {
                echo "        Actions: " . count($controller['actions']) . "\n";
            }
            if (isset($controller['middleware'])) {
                echo "        Middleware: " . count($controller['middleware']) . "\n";
            }
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

// Example 2: Advanced controllers scanning with options
echo "2. Advanced controllers scanning with detailed options...\n";
try {
    $detailedControllersData = Atlas::scan('controllers', [
        'include_actions' => true,
        'include_dependencies' => true,
        'include_middleware' => true,
    ]);
    
    echo "   ✅ Detailed controllers analysis completed\n";
    echo "   📊 Total controllers with detailed info: " . count($detailedControllersData['data']) . "\n\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

// Example 3: Export controllers to different formats
echo "3. Exporting controllers to different formats...\n";

// JSON Export
try {
    $jsonOutput = Atlas::export('controllers', 'json');
    $jsonFile = __DIR__ . '/../storage/atlas/controllers-analysis.json';
    
    if (!is_dir(dirname($jsonFile))) {
        mkdir(dirname($jsonFile), 0755, true);
    }
    file_put_contents($jsonFile, $jsonOutput);
    
    echo "   ✅ JSON export saved to: {$jsonFile}\n";
} catch (Exception $e) {
    echo "   ❌ JSON export error: " . $e->getMessage() . "\n";
}

// HTML Export
try {
    $htmlOutput = Atlas::export('controllers', 'html');
    $htmlFile = __DIR__ . '/../storage/atlas/controllers-analysis.html';
    file_put_contents($htmlFile, $htmlOutput);
    
    echo "   ✅ HTML export saved to: {$htmlFile}\n";
} catch (Exception $e) {
    echo "   ❌ HTML export error: " . $e->getMessage() . "\n";
}

// PDF Export
try {
    $pdfOutput = Atlas::export('controllers', 'pdf');
    $pdfFile = __DIR__ . '/../storage/atlas/controllers-analysis.pdf';
    file_put_contents($pdfFile, $pdfOutput);
    
    echo "   ✅ PDF export saved to: {$pdfFile}\n";
} catch (Exception $e) {
    echo "   ❌ PDF export error: " . $e->getMessage() . "\n";
}

echo "\n4. Controllers Analysis Information:\n";
echo "   🎮 Controller actions and method signatures\n";
echo "   🛡️ Applied middleware and route protection\n";
echo "   💉 Dependency injection and service dependencies\n";
echo "   📊 Route bindings and parameter handling\n\n";

echo "5. Command Line Usage:\n";
echo "   # Export controllers as interactive HTML\n";
echo "   php artisan atlas:export --type=controllers --format=html --output=docs/controllers.html\n\n";
echo "   # Export controllers as JSON for API consumption\n";
echo "   php artisan atlas:export --type=controllers --format=json --output=api/controllers.json\n\n";
echo "   # Export controllers as PDF for documentation\n";
echo "   php artisan atlas:export --type=controllers --format=pdf --output=docs/controllers.pdf\n\n";

echo "✅ Controllers analysis example completed!\n";