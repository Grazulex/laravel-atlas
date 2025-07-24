<?php

/**
 * Laravel Atlas - PDF Export Example
 * 
 * This example demonstrates how to generate professional PDF documentation
 * using Laravel Atlas's PDF export functionality.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use LaravelAtlas\Facades\Atlas;

echo "🔍 Laravel Atlas - PDF Export Example\n";
echo "=====================================\n\n";

// Example 1: Generate comprehensive PDF documentation
echo "1. Generating comprehensive PDF documentation...\n";
try {
    $pdfContent = Atlas::export('all', 'pdf');
    
    // Save to file
    $outputPath = __DIR__ . '/../storage/atlas/complete-architecture.pdf';
    if (!is_dir(dirname($outputPath))) {
        mkdir(dirname($outputPath), 0755, true);
    }
    file_put_contents($outputPath, $pdfContent);
    
    echo "   ✅ Complete PDF documentation saved to: {$outputPath}\n";
    echo "   📄 Size: " . number_format(strlen($pdfContent)) . " bytes\n\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

// Example 2: Generate component-specific PDF reports
$components = ['models', 'routes', 'commands', 'services'];

echo "2. Generating component-specific PDF reports...\n";
foreach ($components as $component) {
    try {
        echo "   📊 Generating {$component} PDF report...\n";
        $pdfContent = Atlas::export($component, 'pdf');
        
        $outputPath = __DIR__ . "/../storage/atlas/{$component}-report.pdf";
        file_put_contents($outputPath, $pdfContent);
        
        echo "      ✅ {$component} PDF saved to: {$outputPath}\n";
        echo "      📄 Size: " . number_format(strlen($pdfContent)) . " bytes\n";
    } catch (Exception $e) {
        echo "      ❌ Error generating {$component} PDF: " . $e->getMessage() . "\n";
    }
}

echo "\n3. PDF Export Features:\n";
echo "   🎨 Professional layout optimized for A4 format\n";
echo "   📋 Comprehensive component information with metadata\n";
echo "   🔧 Self-contained documents ready for sharing\n";
echo "   📊 Structured sections with proper typography\n";
echo "   💼 Suitable for presentations and compliance reports\n\n";

echo "4. Command Line Usage:\n";
echo "   # Generate complete PDF documentation\n";
echo "   php artisan atlas:export --format=pdf --output=docs/architecture.pdf\n\n";
echo "   # Generate component-specific PDF reports\n";
echo "   php artisan atlas:export --type=models --format=pdf --output=docs/models.pdf\n";
echo "   php artisan atlas:export --type=routes --format=pdf --output=docs/routes.pdf\n\n";

echo "✅ PDF export examples completed!\n";
echo "📁 Generated files are in the storage/atlas/ directory\n";