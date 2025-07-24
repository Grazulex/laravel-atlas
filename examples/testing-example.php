<?php

/**
 * Laravel Atlas - Testing and Architecture Analysis Example
 *
 * This example demonstrates how to use Laravel Atlas for testing 
 * and analyzing your application's architecture programmatically.
 * Perfect for CI/CD integration and architectural validation.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use LaravelAtlas\Facades\Atlas;

echo "🧪 Laravel Atlas - Testing & Architecture Analysis Example\n";
echo "=========================================================\n\n";

// Example 1: Basic Architecture Validation
echo "1. Basic Architecture Validation\n";
echo "--------------------------------\n";

function validateBasicArchitecture(): array
{
    $issues = [];
    
    // Check if we have essential components
    $models = Atlas::scan('models');
    if ($models['count'] === 0) {
        $issues[] = "⚠️  No models found - ensure your application has Eloquent models";
    } else {
        echo "   ✅ Found {$models['count']} models\n";
    }
    
    $routes = Atlas::scan('routes');
    if ($routes['count'] === 0) {
        $issues[] = "⚠️  No routes found - ensure your application has defined routes";
    } else {
        echo "   ✅ Found {$routes['count']} routes\n";
    }
    
    $controllers = Atlas::scan('controllers');
    if ($controllers['count'] === 0) {
        $issues[] = "⚠️  No controllers found - ensure your application has controllers";
    } else {
        echo "   ✅ Found {$controllers['count']} controllers\n";
    }
    
    return $issues;
}

$basicIssues = validateBasicArchitecture();
if (empty($basicIssues)) {
    echo "   🎉 Basic architecture validation passed!\n\n";
} else {
    echo "   ❌ Basic architecture issues found:\n";
    foreach ($basicIssues as $issue) {
        echo "      {$issue}\n";
    }
    echo "\n";
}

// Example 2: Component Relationship Analysis
echo "2. Component Relationship Analysis\n";
echo "-----------------------------------\n";

function analyzeComponentRelationships(): array
{
    $analysis = [];
    
    // Analyze model relationships
    $modelsData = Atlas::scan('models', ['include_relationships' => true]);
    $totalRelationships = 0;
    
    foreach ($modelsData['data'] as $model) {
        $relationships = $model['relationships'] ?? [];
        $relationshipCount = count($relationships);
        $totalRelationships += $relationshipCount;
        
        if ($relationshipCount > 10) {
            $analysis['warnings'][] = "Model {$model['name']} has many relationships ({$relationshipCount}) - consider refactoring";
        }
    }
    
    $analysis['metrics']['models'] = [
        'total' => $modelsData['count'],
        'total_relationships' => $totalRelationships,
        'avg_relationships' => $modelsData['count'] > 0 ? round($totalRelationships / $modelsData['count'], 2) : 0,
    ];
    
    echo "   📊 Models: {$analysis['metrics']['models']['total']} total, ";
    echo "{$analysis['metrics']['models']['total_relationships']} relationships, ";
    echo "{$analysis['metrics']['models']['avg_relationships']} avg per model\n";
    
    // Analyze route middleware usage
    $routesData = Atlas::scan('routes', ['include_middleware' => true]);
    $routesWithMiddleware = 0;
    $totalMiddleware = 0;
    
    foreach ($routesData['data'] as $route) {
        $middleware = $route['middleware'] ?? [];
        if (!empty($middleware)) {
            $routesWithMiddleware++;
            $totalMiddleware += count($middleware);
        }
    }
    
    $analysis['metrics']['routes'] = [
        'total' => $routesData['count'],
        'with_middleware' => $routesWithMiddleware,
        'middleware_coverage' => $routesData['count'] > 0 ? round(($routesWithMiddleware / $routesData['count']) * 100, 2) : 0,
    ];
    
    echo "   📊 Routes: {$analysis['metrics']['routes']['total']} total, ";
    echo "{$analysis['metrics']['routes']['middleware_coverage']}% have middleware\n";
    
    return $analysis;
}

$relationshipAnalysis = analyzeComponentRelationships();
echo "\n";

// Example 3: Architecture Pattern Validation
echo "3. Architecture Pattern Validation\n";
echo "-----------------------------------\n";

function validateArchitecturePatterns(): array
{
    $issues = [];
    
    // Check controller naming conventions
    $controllersData = Atlas::scan('controllers');
    foreach ($controllersData['data'] as $controller) {
        if (!str_ends_with($controller['name'], 'Controller')) {
            $issues[] = "Controller {$controller['name']} doesn't follow naming convention (should end with 'Controller')";
        }
    }
    
    // Check service layer presence
    $servicesData = Atlas::scan('services');
    if ($servicesData['count'] === 0) {
        $issues[] = "No services found - consider implementing a service layer for business logic";
    }
    
    // Check form request usage for validation
    $formRequestsData = Atlas::scan('form_requests');
    if ($formRequestsData['count'] === 0) {
        $issues[] = "No form requests found - consider using form requests for validation";
    }
    
    // Check middleware usage
    $middlewareData = Atlas::scan('middlewares');
    if ($middlewareData['count'] === 0) {
        $issues[] = "No custom middleware found - consider implementing middleware for cross-cutting concerns";
    }
    
    echo "   📋 Pattern Analysis:\n";
    echo "      Controllers: {$controllersData['count']} (naming check)\n";
    echo "      Services: {$servicesData['count']} (business logic layer)\n";
    echo "      Form Requests: {$formRequestsData['count']} (validation layer)\n";
    echo "      Middleware: {$middlewareData['count']} (cross-cutting concerns)\n";
    
    return $issues;
}

$patternIssues = validateArchitecturePatterns();
if (empty($patternIssues)) {
    echo "   ✅ Architecture pattern validation passed!\n\n";
} else {
    echo "   ⚠️  Architecture pattern issues:\n";
    foreach ($patternIssues as $issue) {
        echo "      - {$issue}\n";
    }
    echo "\n";
}

// Example 4: Complexity Analysis
echo "4. Complexity Analysis\n";
echo "----------------------\n";

function analyzeComplexity(): array
{
    $complexity = [];
    
    // All components overview
    $allData = Atlas::scan('all');
    $totalComponents = array_sum(array_column($allData, 'count'));
    
    $complexity['total_components'] = $totalComponents;
    $complexity['complexity_score'] = calculateComplexityScore($allData);
    
    echo "   📈 Application Complexity:\n";
    echo "      Total Components: {$totalComponents}\n";
    
    // Component breakdown
    foreach ($allData as $type => $data) {
        if (isset($data['count']) && $data['count'] > 0) {
            echo "      {$type}: {$data['count']}\n";
        }
    }
    
    // Complexity recommendations
    if ($totalComponents > 500) {
        echo "   ⚠️  High complexity detected - consider modularization\n";
    } elseif ($totalComponents > 200) {
        echo "   ℹ️  Moderate complexity - monitor growth\n";
    } else {
        echo "   ✅ Manageable complexity level\n";
    }
    
    return $complexity;
}

function calculateComplexityScore(array $allData): float
{
    $score = 0;
    $weights = [
        'models' => 2,      // Models are core complexity
        'controllers' => 1.5, // Controllers add complexity
        'services' => 1,    // Services manage complexity
        'routes' => 0.5,    // Routes are structural
    ];
    
    foreach ($allData as $type => $data) {
        $count = $data['count'] ?? 0;
        $weight = $weights[$type] ?? 1;
        $score += $count * $weight;
    }
    
    return round($score, 2);
}

$complexityAnalysis = analyzeComplexity();
echo "\n";

// Example 5: Generate Testing Report
echo "5. Generate Testing Report\n";
echo "--------------------------\n";

function generateTestingReport(): array
{
    $report = [
        'timestamp' => date('Y-m-d H:i:s'),
        'summary' => [],
        'metrics' => [],
        'recommendations' => [],
    ];
    
    // Scan all available component types
    $componentTypes = [
        'models', 'routes', 'commands', 'services', 'notifications',
        'middlewares', 'form_requests', 'events', 'controllers',
        'resources', 'jobs', 'actions', 'policies', 'rules',
        'listeners', 'observers'
    ];
    
    foreach ($componentTypes as $type) {
        try {
            $data = Atlas::scan($type);
            $report['metrics'][$type] = [
                'count' => $data['count'],
                'status' => $data['count'] > 0 ? 'found' : 'empty',
            ];
            
            if ($data['count'] > 0) {
                $report['summary']['implemented'][] = $type;
            } else {
                $report['summary']['missing'][] = $type;
            }
        } catch (Exception $e) {
            $report['metrics'][$type] = [
                'count' => 0,
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }
    
    // Generate recommendations
    if (empty($report['summary']['implemented'])) {
        $report['recommendations'][] = "No components detected - ensure Atlas is running in a Laravel application";
    }
    
    if (in_array('models', $report['summary']['missing'] ?? [])) {
        $report['recommendations'][] = "Consider adding Eloquent models for data management";
    }
    
    if (in_array('services', $report['summary']['missing'] ?? [])) {
        $report['recommendations'][] = "Consider implementing a service layer for business logic";
    }
    
    return $report;
}

$testingReport = generateTestingReport();

echo "   📊 Testing Report Generated:\n";
echo "      Implemented: " . count($testingReport['summary']['implemented'] ?? []) . " component types\n";
echo "      Missing: " . count($testingReport['summary']['missing'] ?? []) . " component types\n";
echo "      Recommendations: " . count($testingReport['recommendations'] ?? []) . " items\n";

// Save report to file
$reportPath = __DIR__ . '/../storage/atlas/testing-report-' . date('Y-m-d-H-i-s') . '.json';
if (!is_dir(dirname($reportPath))) {
    mkdir(dirname($reportPath), 0755, true);
}
file_put_contents($reportPath, json_encode($testingReport, JSON_PRETTY_PRINT));
echo "      Report saved: {$reportPath}\n\n";

// Example 6: Export Architecture Documentation for Review
echo "6. Export Architecture Documentation\n";
echo "------------------------------------\n";

try {
    // Generate HTML report for team review
    $htmlReport = Atlas::export('all', 'html');
    $htmlPath = __DIR__ . '/../storage/atlas/architecture-review.html';
    file_put_contents($htmlPath, $htmlReport);
    echo "   📄 HTML Report: {$htmlPath}\n";
    echo "      Size: " . number_format(strlen($htmlReport)) . " bytes\n";
    echo "      Features: Dark mode, responsive design, interactive navigation\n";
    
    // Generate JSON data for automated analysis
    $jsonReport = Atlas::export('all', 'json');
    $jsonPath = __DIR__ . '/../storage/atlas/architecture-data.json';
    file_put_contents($jsonPath, $jsonReport);
    echo "   📊 JSON Data: {$jsonPath}\n";
    echo "      Size: " . number_format(strlen($jsonReport)) . " bytes\n";
    echo "      Use: API integration, automated analysis\n";
    
    // Generate PDF for presentations
    $pdfReport = Atlas::export('all', 'pdf');
    $pdfPath = __DIR__ . '/../storage/atlas/architecture-presentation.pdf';
    file_put_contents($pdfPath, $pdfReport);
    echo "   📑 PDF Report: {$pdfPath}\n";
    echo "      Size: " . number_format(strlen($pdfReport)) . " bytes\n";
    echo "      Use: Presentations, documentation packages\n";
    
} catch (Exception $e) {
    echo "   ❌ Error generating reports: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 7: CI/CD Integration Example
echo "7. CI/CD Integration Commands\n";
echo "-----------------------------\n";
echo "   Use these commands in your CI/CD pipeline:\n\n";

echo "   # Run architecture validation tests\n";
echo "   php artisan test --filter=ArchitectureTest\n\n";

echo "   # Generate architecture reports\n";
echo "   php artisan atlas:export --format=html --output=reports/architecture.html\n";
echo "   php artisan atlas:export --format=json --output=reports/architecture.json\n";
echo "   php artisan atlas:export --format=pdf --output=reports/architecture.pdf\n\n";

echo "   # Component-specific analysis\n";
echo "   php artisan atlas:export --type=models --format=json --output=reports/models.json\n";
echo "   php artisan atlas:export --type=routes --format=html --output=reports/routes.html\n";
echo "   php artisan atlas:export --type=services --format=pdf --output=reports/services.pdf\n\n";

echo "✅ Testing and architecture analysis example completed!\n";
echo "💡 Integration tips:\n";
echo "   - Add architecture tests to your PHPUnit test suite\n";
echo "   - Generate reports in CI/CD for architectural oversight\n";
echo "   - Use JSON exports for automated architectural analysis\n";
echo "   - Share HTML reports for team architecture reviews\n";
echo "   - Use PDF exports for stakeholder presentations\n";