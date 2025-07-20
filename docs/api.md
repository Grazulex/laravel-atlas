# Programmatic API

Laravel Atlas provides a comprehensive programmatic API through the Atlas facade and AtlasManager class. This allows you to integrate Laravel Atlas functionality directly into your applications.

## 🚀 Quick Start

```php
use LaravelAtlas\Facades\Atlas;

// Scan all components
$allData = Atlas::scan('all');

// Scan specific component type
$modelData = Atlas::scan('models');

// Export to different formats
$jsonOutput = Atlas::export('models', 'json');
$markdownOutput = Atlas::export('routes', 'markdown');
```

## 📖 Atlas Facade Methods

### `scan(string $type, array $options = []): array`

Scans a specific component type and returns raw data.

```php
// Basic scanning
$modelData = Atlas::scan('models');
$routeData = Atlas::scan('routes');

// Scanning with options
$detailedModels = Atlas::scan('models', [
    'include_relationships' => true,
    'include_observers' => true,
    'detailed' => true
]);

$groupedRoutes = Atlas::scan('routes', [
    'group_by_prefix' => true,
    'include_middleware' => true
]);
```

**Parameters:**
- `$type` - Component type to scan (models, routes, jobs, etc.)
- `$options` - Array of scanning options (mapper-specific)

**Returns:** Array with scanned data structure

### `export(string $type, string $format, array $options = []): string`

Exports a component type to a specific format.

```php
// Basic export
$jsonData = Atlas::export('models', 'json');
$markdownDocs = Atlas::export('routes', 'markdown');

// Export with options
$prettyJson = Atlas::export('models', 'json', [
    'pretty_print' => true
]);

$detailedMarkdown = Atlas::export('controllers', 'markdown', [
    'include_toc' => true,
    'include_stats' => true,
    'detailed_sections' => true
]);

$customMermaid = Atlas::export('models', 'mermaid', [
    'direction' => 'LR',
    'theme' => 'dark',
    'include_relationships' => true
]);
```

**Parameters:**
- `$type` - Component type to export
- `$format` - Export format (json, markdown, mermaid, html, pdf)
- `$options` - Array of export options (format-specific)

**Returns:** String with exported content

### `generate(array|string $types, string $format, array $options = []): string`

Generates exports for multiple component types.

```php
// Generate for multiple types
$multiTypeOutput = Atlas::generate(['models', 'routes', 'controllers'], 'json');

// Generate for all types
$completeMap = Atlas::generate('all', 'markdown');

// Generate with custom options
$htmlReport = Atlas::generate(['models', 'services'], 'html', [
    'theme' => 'dark',
    'include_search' => true,
    'interactive_diagrams' => true
]);
```

**Parameters:**
- `$types` - Array of types or 'all' for all types
- `$format` - Export format
- `$options` - Array of generation options

**Returns:** String with generated content

### `mapper(string $type): MapperInterface`

Gets a mapper instance for a specific component type.

```php
// Get mapper instances
$modelMapper = Atlas::mapper('models');
$routeMapper = Atlas::mapper('routes');

// Use mapper directly
$customScan = $modelMapper->scan([
    'include_relationships' => false,
    'custom_option' => true
]);

// Check mapper type
$type = $modelMapper->getType(); // 'models'
```

### `exporter(string $format): ExporterInterface`

Gets an exporter instance for a specific format.

```php
// Get exporter instances
$jsonExporter = Atlas::exporter('json');
$markdownExporter = Atlas::exporter('markdown');

// Use exporter directly
$data = Atlas::scan('models');
$output = $jsonExporter->export($data);

// Get file extension
$extension = $jsonExporter->getExtension(); // 'json'
$mimeType = $jsonExporter->getMimeType(); // 'application/json'
```

### `getAvailableTypes(): array`

Returns array of available component types.

```php
$types = Atlas::getAvailableTypes();
// ['models', 'routes', 'jobs', 'services', 'controllers', ...]
```

### `getAvailableFormats(): array`

Returns array of available export formats.

```php
$formats = Atlas::getAvailableFormats();
// ['json', 'markdown', 'mermaid', 'html', 'pdf']
```

## 🔧 AtlasManager Direct Usage

For more advanced usage, you can inject the AtlasManager directly:

```php
use LaravelAtlas\AtlasManager;

class ArchitectureService
{
    public function __construct(
        private AtlasManager $atlasManager
    ) {}

    public function generateReport(): array
    {
        $models = $this->atlasManager->scan('models');
        $routes = $this->atlasManager->scan('routes');
        
        return [
            'models' => $models,
            'routes' => $routes,
            'generated_at' => now()
        ];
    }

    public function exportToMultipleFormats(string $type): array
    {
        $data = $this->atlasManager->scan($type);
        
        return [
            'json' => $this->atlasManager->exporter('json')->export($data),
            'markdown' => $this->atlasManager->exporter('markdown')->export($data),
            'mermaid' => $this->atlasManager->exporter('mermaid')->export($data)
        ];
    }
}
```

## 📊 Working with Data Structures

### Understanding Output Format

All scan operations return data in this structure:

```php
$result = [
    'type' => 'models',
    'scan_path' => '/app/Models',
    'options' => [
        'include_relationships' => true,
        // ... other options
    ],
    'data' => [
        // Array of scanned components
        [
            'name' => 'User',
            'namespace' => 'App\\Models',
            'path' => '/app/Models/User.php',
            // ... component-specific data
        ]
    ]
];
```

### Processing Scanned Data

```php
// Get models with relationships
$modelData = Atlas::scan('models', ['include_relationships' => true]);

foreach ($modelData['data'] as $model) {
    echo "Model: {$model['name']}\n";
    
    if (isset($model['relationships'])) {
        foreach ($model['relationships'] as $relationship) {
            echo "  - {$relationship['type']}: {$relationship['related']}\n";
        }
    }
}

// Extract specific information
$modelNames = collect($modelData['data'])->pluck('name')->toArray();
$modelsWithFactories = collect($modelData['data'])
    ->filter(fn($model) => isset($model['factory']))
    ->toArray();
```

### Custom Data Analysis

```php
class ArchitectureAnalyzer
{
    public function analyzeModels(): array
    {
        $models = Atlas::scan('models', [
            'include_relationships' => true,
            'include_observers' => true
        ]);

        return [
            'total_models' => count($models['data']),
            'models_with_relationships' => $this->countModelsWithRelationships($models['data']),
            'models_with_observers' => $this->countModelsWithObservers($models['data']),
            'relationship_types' => $this->getRelationshipTypes($models['data'])
        ];
    }

    private function countModelsWithRelationships(array $models): int
    {
        return collect($models)
            ->filter(fn($model) => !empty($model['relationships']))
            ->count();
    }

    private function countModelsWithObservers(array $models): int
    {
        return collect($models)
            ->filter(fn($model) => !empty($model['observers']))
            ->count();
    }

    private function getRelationshipTypes(array $models): array
    {
        $types = [];
        
        foreach ($models as $model) {
            if (isset($model['relationships'])) {
                foreach ($model['relationships'] as $relationship) {
                    $type = $relationship['type'];
                    $types[$type] = ($types[$type] ?? 0) + 1;
                }
            }
        }

        return $types;
    }
}
```

## 🔄 Integration Patterns

### Service Layer Integration

```php
class DocumentationService
{
    public function generateArchitectureDocumentation(): void
    {
        // Generate comprehensive documentation
        $formats = ['markdown', 'html', 'json'];
        $types = ['models', 'routes', 'controllers', 'services'];

        foreach ($types as $type) {
            foreach ($formats as $format) {
                $content = Atlas::export($type, $format, [
                    'detailed' => true
                ]);

                $filename = "docs/{$type}.{$format}";
                file_put_contents($filename, $content);
            }
        }
    }

    public function getArchitectureStats(): array
    {
        $allData = Atlas::scan('all');
        
        return [
            'components' => $this->countComponents($allData),
            'generated_at' => now(),
            'generation_time' => $this->measureGenerationTime()
        ];
    }
}
```

### API Endpoint Integration

```php
class ArchitectureController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');
        $format = $request->get('format', 'json');
        
        try {
            $data = Atlas::scan($type);
            
            if ($format === 'json') {
                return response()->json($data);
            }
            
            $exported = Atlas::export($type, $format);
            $mimeType = Atlas::exporter($format)->getMimeType();
            
            return response($exported)
                ->header('Content-Type', $mimeType);
                
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function download(string $type, string $format)
    {
        $content = Atlas::export($type, $format);
        $extension = Atlas::exporter($format)->getExtension();
        $filename = "architecture-{$type}.{$extension}";
        
        return response($content)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }
}
```

### Console Command Integration

```php
class CustomArchitectureCommand extends Command
{
    protected $signature = 'app:architecture {--type=all} {--save}';
    protected $description = 'Generate custom architecture analysis';

    public function handle(): void
    {
        $type = $this->option('type');
        $save = $this->option('save');
        
        $this->info("Analyzing {$type} components...");
        
        $data = Atlas::scan($type, ['detailed' => true]);
        $analysis = $this->performCustomAnalysis($data);
        
        if ($save) {
            $json = Atlas::exporter('json')->export($analysis);
            file_put_contents('storage/architecture-analysis.json', $json);
            $this->info('Analysis saved to storage/architecture-analysis.json');
        } else {
            $this->table(['Metric', 'Value'], $this->formatAnalysis($analysis));
        }
    }

    private function performCustomAnalysis(array $data): array
    {
        // Custom analysis logic
        return [
            'total_components' => count($data['data']),
            'analysis_time' => now(),
            'complexity_score' => $this->calculateComplexity($data)
        ];
    }
}
```

## 🧪 Testing with Laravel Atlas

```php
use Tests\TestCase;
use LaravelAtlas\Facades\Atlas;

class ArchitectureTest extends TestCase
{
    public function test_models_can_be_scanned(): void
    {
        $data = Atlas::scan('models');
        
        $this->assertIsArray($data);
        $this->assertArrayHasKey('type', $data);
        $this->assertEquals('models', $data['type']);
        $this->assertArrayHasKey('data', $data);
    }

    public function test_json_export_is_valid(): void
    {
        $json = Atlas::export('models', 'json');
        
        $decoded = json_decode($json, true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('data', $decoded);
    }

    public function test_markdown_export_contains_headers(): void
    {
        $markdown = Atlas::export('models', 'markdown');
        
        $this->assertStringContains('# Laravel Atlas Architecture Map', $markdown);
        $this->assertStringContains('## Models', $markdown);
    }
}
```

## 🎯 Best Practices

### Error Handling

```php
try {
    $data = Atlas::scan('models');
} catch (InvalidArgumentException $e) {
    // Handle unknown component type
    Log::error('Invalid Atlas component type', ['error' => $e->getMessage()]);
} catch (Exception $e) {
    // Handle other scanning errors
    Log::error('Atlas scanning failed', ['error' => $e->getMessage()]);
}
```

### Performance Optimization

```php
// Cache expensive operations
$cacheKey = 'atlas_models_' . md5(json_encode($options));
$modelData = Cache::remember($cacheKey, 3600, function () use ($options) {
    return Atlas::scan('models', $options);
});

// Process specific components only
$criticalComponents = ['models', 'routes'];
foreach ($criticalComponents as $component) {
    $data = Atlas::scan($component);
    // Process...
}
```

### Memory Management

```php
// For large applications, process in batches
$types = Atlas::getAvailableTypes();

foreach ($types as $type) {
    $data = Atlas::scan($type);
    $this->processType($type, $data);
    
    // Free memory between iterations
    unset($data);
    
    if (function_exists('gc_collect_cycles')) {
        gc_collect_cycles();
    }
}
```