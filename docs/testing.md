# Testing

This document covers testing Laravel Atlas functionality and using Atlas in your test suite.

## 📋 Prerequisites

Laravel Atlas includes testing utilities and can be integrated into your application's test suite.

```bash
composer require grazulex/laravel-atlas --dev
```

## 🧪 Basic Testing

### Testing Atlas Commands

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AtlasCommandTest extends TestCase
{
    public function test_atlas_generate_command_exists(): void
    {
        $result = $this->artisan('list');
        $result->assertExitCode(0);
        
        // Check if atlas:generate command is available
        $this->artisan('atlas:generate --help')
             ->assertExitCode(0);
    }
    
    public function test_atlas_generate_basic_functionality(): void
    {
        $this->artisan('atlas:generate')
             ->expectsOutput('🗺️  Generating Laravel Atlas map...')
             ->expectsOutput('✔ Map generated successfully!')
             ->assertExitCode(0);
    }
    
    public function test_atlas_generate_with_specific_type(): void
    {
        $this->artisan('atlas:generate --type=models')
             ->assertExitCode(0);
             
        $this->artisan('atlas:generate --type=routes')
             ->assertExitCode(0);
    }
    
    public function test_atlas_generate_with_different_formats(): void
    {
        $formats = ['json', 'markdown', 'html', 'image', 'pdf', 'php'];
        
        foreach ($formats as $format) {
            $this->artisan("atlas:generate --format={$format}")
                 ->assertExitCode(0);
        }
    }
}
```

### Testing Atlas Facade

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use LaravelAtlas\Facades\Atlas;

class AtlasFacadeTest extends TestCase
{
    public function test_atlas_facade_can_scan_models(): void
    {
        $data = Atlas::scan('models');
        
        $this->assertIsArray($data);
        $this->assertArrayHasKey('type', $data);
        $this->assertEquals('models', $data['type']);
        $this->assertArrayHasKey('data', $data);
    }
    
    public function test_atlas_facade_can_export_json(): void
    {
        $json = Atlas::export('models', 'json');
        
        $decoded = json_decode($json, true);
        $this->assertIsArray($decoded);
        $this->assertJson($json);
    }
    
    public function test_atlas_facade_can_export_markdown(): void
    {
        $markdown = Atlas::export('routes', 'markdown');
        
        $this->assertIsString($markdown);
        $this->assertStringContainsString('#', $markdown); // Markdown headers
    }
    
    public function test_available_types_and_formats(): void
    {
        $types = Atlas::getAvailableTypes();
        $formats = Atlas::getAvailableFormats();
        
        $this->assertIsArray($types);
        $this->assertIsArray($formats);
        
        // Check for expected types
        $expectedTypes = ['models', 'routes', 'services', 'controllers', 'events', 'listeners'];
        foreach ($expectedTypes as $type) {
            $this->assertContains($type, $types);
        }
        
        // Check for expected formats
        $expectedFormats = ['json', 'markdown', 'html', 'image', 'pdf', 'php'];
        foreach ($expectedFormats as $format) {
            $this->assertContains($format, $formats);
        }
    }
}
```

## 🔍 Testing Individual Mappers

### Model Mapper Tests

```php
<?php

namespace Tests\Unit\Mappers;

use Tests\TestCase;
use LaravelAtlas\Mappers\ModelMapper;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelMapperTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_model_mapper_discovers_models(): void
    {
        $mapper = new ModelMapper();
        $result = $mapper->scan();
        
        $this->assertArrayHasKey('type', $result);
        $this->assertEquals('models', $result['type']);
        $this->assertArrayHasKey('data', $result);
        
        // Check if User model is found (default Laravel model)
        $modelNames = array_column($result['data'], 'name');
        $this->assertContains('User', $modelNames);
    }
    
    public function test_model_mapper_includes_relationships(): void
    {
        $mapper = new ModelMapper();
        $result = $mapper->scan(['include_relationships' => true]);
        
        foreach ($result['data'] as $model) {
            if ($model['name'] === 'User') {
                // Check if relationships are included
                $this->assertArrayHasKey('relationships', $model);
                break;
            }
        }
    }
    
    public function test_model_mapper_includes_observers(): void
    {
        $mapper = new ModelMapper();
        $result = $mapper->scan(['include_observers' => true]);
        
        foreach ($result['data'] as $model) {
            $this->assertArrayHasKey('observers', $model);
        }
    }
}
```

### Route Mapper Tests

```php
<?php

namespace Tests\Unit\Mappers;

use Tests\TestCase;
use LaravelAtlas\Mappers\RouteMapper;

class RouteMapperTest extends TestCase
{
    public function test_route_mapper_discovers_routes(): void
    {
        $mapper = new RouteMapper();
        $result = $mapper->scan();
        
        $this->assertArrayHasKey('type', $result);
        $this->assertEquals('routes', $result['type']);
        $this->assertArrayHasKey('data', $result);
        $this->assertNotEmpty($result['data']);
    }
    
    public function test_route_mapper_includes_middleware(): void
    {
        $mapper = new RouteMapper();
        $result = $mapper->scan(['include_middleware' => true]);
        
        foreach ($result['data'] as $route) {
            $this->assertArrayHasKey('middleware', $route);
        }
    }
    
    public function test_route_mapper_includes_controllers(): void
    {
        $mapper = new RouteMapper();
        $result = $mapper->scan(['include_controllers' => true]);
        
        foreach ($result['data'] as $route) {
            if (isset($route['action'])) {
                $this->assertArrayHasKey('controller', $route);
            }
        }
    }
}
```

## 📊 Testing Export Formats

### JSON Exporter Tests

```php
<?php

namespace Tests\Unit\Exporters;

use Tests\TestCase;
use LaravelAtlas\Exporters\JsonExporter;
use LaravelAtlas\Facades\Atlas;

class JsonExporterTest extends TestCase
{
    public function test_json_exporter_produces_valid_json(): void
    {
        $exporter = new JsonExporter();
        $data = Atlas::scan('models');
        
        $json = $exporter->export($data);
        
        $this->assertIsString($json);
        $this->assertJson($json);
        
        $decoded = json_decode($json, true);
        $this->assertIsArray($decoded);
    }
    
    public function test_json_exporter_includes_metadata(): void
    {
        $exporter = new JsonExporter();
        $data = Atlas::scan('routes');
        
        $json = $exporter->export($data);
        $decoded = json_decode($json, true);
        
        $this->assertArrayHasKey('type', $decoded);
        $this->assertArrayHasKey('data', $decoded);
    }
    
    public function test_json_exporter_pretty_print_option(): void
    {
        $exporter = new JsonExporter(['pretty_print' => true]);
        $data = Atlas::scan('models');
        
        $json = $exporter->export($data);
        
        // Pretty printed JSON should contain newlines and indentation
        $this->assertStringContainsString("\n", $json);
        $this->assertStringContainsString("    ", $json);
    }
}
```

### Markdown Exporter Tests

```php
<?php

namespace Tests\Unit\Exporters;

use Tests\TestCase;
use LaravelAtlas\Exporters\MarkdownExporter;
use LaravelAtlas\Facades\Atlas;

class MarkdownExporterTest extends TestCase
{
    public function test_markdown_exporter_produces_valid_markdown(): void
    {
        $exporter = new MarkdownExporter();
        $data = Atlas::scan('models');
        
        $markdown = $exporter->export($data);
        
        $this->assertIsString($markdown);
        $this->assertStringContainsString('#', $markdown); // Headers
        $this->assertStringContainsString('##', $markdown); // Sub-headers
    }
    
    public function test_markdown_exporter_includes_table_of_contents(): void
    {
        $exporter = new MarkdownExporter(['include_toc' => true]);
        $data = Atlas::scan('routes');
        
        $markdown = $exporter->export($data);
        
        $this->assertStringContainsString('Table of Contents', $markdown);
        $this->assertStringContainsString('[', $markdown); // Links
        $this->assertStringContainsString('](#', $markdown); // Internal links
    }
}
```

## 🎯 Integration Testing

### Full Application Architecture Tests

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use LaravelAtlas\Facades\Atlas;
use Illuminate\Support\Facades\File;

class ArchitectureIntegrationTest extends TestCase
{
    public function test_complete_application_scan(): void
    {
        $data = Atlas::scan('all');
        
        $this->assertIsArray($data);
        $this->assertArrayHasKey('data', $data);
        
        // Check that all expected component types are present
        $expectedTypes = ['models', 'routes', 'controllers', 'services'];
        foreach ($expectedTypes as $type) {
            $this->assertArrayHasKey($type, $data['data']);
        }
    }
    
    public function test_export_to_file(): void
    {
        $outputPath = storage_path('testing/atlas-test.json');
        
        // Ensure directory exists
        File::ensureDirectoryExists(dirname($outputPath));
        
        // Generate and save
        $this->artisan("atlas:generate --format=json --output={$outputPath}")
             ->assertExitCode(0);
        
        // Verify file was created
        $this->assertTrue(File::exists($outputPath));
        
        // Verify content is valid JSON
        $content = File::get($outputPath);
        $decoded = json_decode($content, true);
        $this->assertIsArray($decoded);
        
        // Cleanup
        File::delete($outputPath);
    }
    
    public function test_multiple_format_export(): void
    {
        $formats = ['json', 'markdown'];
        $outputs = [];
        
        foreach ($formats as $format) {
            $output = storage_path("testing/atlas-test.{$format}");
            $outputs[] = $output;
            
            File::ensureDirectoryExists(dirname($output));
            
            $this->artisan("atlas:generate --type=models --format={$format} --output={$output}")
                 ->assertExitCode(0);
            
            $this->assertTrue(File::exists($output));
        }
        
        // Cleanup
        foreach ($outputs as $output) {
            File::delete($output);
        }
    }
}
```

## 🔧 Performance Testing

### Load Testing

```php
<?php

namespace Tests\Performance;

use Tests\TestCase;
use LaravelAtlas\Facades\Atlas;

class AtlasPerformanceTest extends TestCase
{
    public function test_large_application_scan_performance(): void
    {
        $startTime = microtime(true);
        
        $data = Atlas::scan('all');
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Should complete within reasonable time (adjust based on your app size)
        $this->assertLessThan(30.0, $executionTime, "Atlas scan took too long: {$executionTime} seconds");
        
        // Should return meaningful data
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('data', $data);
    }
    
    public function test_memory_usage(): void
    {
        $initialMemory = memory_get_usage();
        
        Atlas::scan('all');
        
        $finalMemory = memory_get_usage();
        $memoryUsed = $finalMemory - $initialMemory;
        
        // Should not use excessive memory (adjust threshold as needed)
        $maxMemoryMB = 100;
        $memoryUsedMB = $memoryUsed / 1024 / 1024;
        
        $this->assertLessThan($maxMemoryMB, $memoryUsedMB, "Atlas used too much memory: {$memoryUsedMB}MB");
    }
}
```

### Stress Testing

```php
<?php

namespace Tests\Performance;

use Tests\TestCase;
use LaravelAtlas\Facades\Atlas;

class AtlasStressTest extends TestCase
{
    public function test_multiple_concurrent_scans(): void
    {
        $results = [];
        $types = ['models', 'routes', 'controllers'];
        
        $startTime = microtime(true);
        
        foreach ($types as $type) {
            $results[$type] = Atlas::scan($type);
        }
        
        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;
        
        // All scans should complete
        $this->assertCount(count($types), $results);
        
        foreach ($results as $type => $data) {
            $this->assertArrayHasKey('type', $data);
            $this->assertEquals($type, $data['type']);
        }
        
        // Should complete in reasonable time
        $this->assertLessThan(60.0, $totalTime);
    }
    
    public function test_repeated_scans_consistency(): void
    {
        $firstScan = Atlas::scan('models');
        $secondScan = Atlas::scan('models');
        
        // Results should be consistent
        $this->assertEquals(
            count($firstScan['data']), 
            count($secondScan['data']),
            'Repeated scans should return consistent results'
        );
        
        // Component names should be the same
        $firstNames = array_column($firstScan['data'], 'name');
        $secondNames = array_column($secondScan['data'], 'name');
        
        sort($firstNames);
        sort($secondNames);
        
        $this->assertEquals($firstNames, $secondNames);
    }
}
```

## 🧪 Test Utilities

### Atlas Test Helpers

```php
<?php

namespace Tests;

use LaravelAtlas\Facades\Atlas;

abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    use CreatesApplication;
    
    protected function assertAtlasDataStructure(array $data, string $expectedType = null): void
    {
        $this->assertIsArray($data);
        $this->assertArrayHasKey('type', $data);
        $this->assertArrayHasKey('data', $data);
        
        if ($expectedType) {
            $this->assertEquals($expectedType, $data['type']);
        }
        
        $this->assertIsArray($data['data']);
    }
    
    protected function assertValidJsonExport(string $json): void
    {
        $this->assertJson($json);
        
        $decoded = json_decode($json, true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('type', $decoded);
        $this->assertArrayHasKey('data', $decoded);
    }
    
    protected function assertValidMarkdownExport(string $markdown): void
    {
        $this->assertIsString($markdown);
        $this->assertStringContainsString('#', $markdown);
        $this->assertNotEmpty(trim($markdown));
    }
    
    protected function scanComponent(string $type, array $options = []): array
    {
        $data = Atlas::scan($type, $options);
        $this->assertAtlasDataStructure($data, $type);
        
        return $data;
    }
    
    protected function exportComponent(string $type, string $format, array $options = []): string
    {
        $output = Atlas::export($type, $format, $options);
        $this->assertIsString($output);
        $this->assertNotEmpty($output);
        
        return $output;
    }
}
```

### Custom Assertions

```php
<?php

trait AtlasAssertions
{
    protected function assertHasComponents(array $data, array $expectedComponents): void
    {
        $componentNames = array_column($data['data'], 'name');
        
        foreach ($expectedComponents as $expected) {
            $this->assertContains(
                $expected, 
                $componentNames,
                "Component '{$expected}' not found in scan results"
            );
        }
    }
    
    protected function assertHasRelationships(array $modelData, string $modelName, array $expectedRelationships): void
    {
        $model = $this->findComponentByName($modelData, $modelName);
        $this->assertNotNull($model, "Model '{$modelName}' not found");
        
        $this->assertArrayHasKey('relationships', $model);
        
        $relationshipTypes = array_column($model['relationships'], 'type');
        
        foreach ($expectedRelationships as $expectedType) {
            $this->assertContains(
                $expectedType,
                $relationshipTypes,
                "Relationship type '{$expectedType}' not found for model '{$modelName}'"
            );
        }
    }
    
    protected function assertHasDependencies(array $data, string $componentName, array $expectedDependencies): void
    {
        $component = $this->findComponentByName($data, $componentName);
        $this->assertNotNull($component, "Component '{$componentName}' not found");
        
        $this->assertArrayHasKey('dependencies', $component);
        
        foreach ($expectedDependencies as $dependency) {
            $this->assertContains(
                $dependency,
                $component['dependencies'],
                "Dependency '{$dependency}' not found for component '{$componentName}'"
            );
        }
    }
    
    private function findComponentByName(array $data, string $name): ?array
    {
        foreach ($data['data'] as $component) {
            if ($component['name'] === $name) {
                return $component;
            }
        }
        
        return null;
    }
}
```

## 🚀 Continuous Integration

### GitHub Actions Test Configuration

```yaml
# .github/workflows/atlas-tests.yml
name: Atlas Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  atlas-tests:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: 8.3
        extensions: gd, json
        
    - name: Install dependencies
      run: composer install --prefer-dist --no-progress
      
    - name: Run Atlas Tests
      run: |
        ./vendor/bin/phpunit --testsuite=atlas
        
    - name: Test Atlas Command
      run: |
        php artisan atlas:generate --format=json
        php artisan atlas:generate --type=models --format=markdown
        
    - name: Performance Test
      run: |
        php artisan atlas:generate --type=all --format=json --output=performance-test.json
        ls -la performance-test.json
```

### Test Suite Organization

```xml
<!-- phpunit.xml -->
<phpunit>
    <testsuites>
        <testsuite name="atlas">
            <directory>tests/Unit/Atlas</directory>
            <directory>tests/Feature/Atlas</directory>
        </testsuite>
        <testsuite name="atlas-performance">
            <directory>tests/Performance/Atlas</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

## 📊 Test Coverage

### Measuring Atlas Test Coverage

```bash
# Run tests with coverage
./vendor/bin/phpunit --coverage-html=coverage/atlas --testsuite=atlas

# Generate coverage report
./vendor/bin/phpunit --coverage-text --testsuite=atlas
```

## 🔗 Related Documentation

- [Usage Guide](usage.md) - Basic usage instructions
- [Configuration](configuration.md) - Configuration options
- [Mappers](mappers.md) - Available component mappers
- [Examples](../examples/README.md) - Practical examples

---

For more testing examples and patterns, check the [examples directory](../examples/).