# Testing Your Architecture with Laravel Atlas

Laravel Atlas is a powerful tool for **testing and analyzing your Laravel application's architecture**. This guide demonstrates how to use Atlas as a testing tool to validate your application's structure, identify architectural issues, and ensure your codebase follows best practices.

## 🧪 Architecture Testing Overview

Atlas provides comprehensive scanning capabilities that can be integrated into your testing workflow to:

- **Validate Architecture Patterns** - Ensure your application follows intended architectural patterns
- **Detect Dependencies** - Identify and validate component dependencies
- **Monitor Complexity** - Track architectural complexity over time
- **Enforce Standards** - Validate coding standards and conventions
- **Document Changes** - Generate documentation for architecture changes

## 🔍 Using Atlas Facade for Testing

The Atlas facade provides powerful methods for programmatic analysis in your tests:

### Basic Architecture Testing

```php
use Tests\TestCase;
use LaravelAtlas\Facades\Atlas;

class ArchitectureTest extends TestCase
{
    /** @test */
    public function application_has_expected_models(): void
    {
        $modelsData = Atlas::scan('models');
        
        // Verify minimum number of models
        $this->assertGreaterThan(0, $modelsData['count']);
        
        // Verify specific models exist
        $modelNames = collect($modelsData['data'])->pluck('name');
        $this->assertTrue($modelNames->contains('User'));
        $this->assertTrue($modelNames->contains('Post'));
    }

    /** @test */
    public function routes_follow_restful_conventions(): void
    {
        $routesData = Atlas::scan('routes', [
            'include_middleware' => true,
            'include_controllers' => true,
        ]);
        
        $routes = collect($routesData['data']);
        
        // Verify all API routes have proper middleware
        $apiRoutes = $routes->filter(fn($route) => str_starts_with($route['uri'], 'api/'));
        
        foreach ($apiRoutes as $route) {
            $this->assertContains('api', $route['middleware'], 
                "Route {$route['uri']} should have 'api' middleware");
        }
    }

    /** @test */
    public function controllers_follow_naming_conventions(): void
    {
        $controllersData = Atlas::scan('controllers');
        
        foreach ($controllersData['data'] as $controller) {
            // Verify controller names end with 'Controller'
            $this->assertStringEndsWith('Controller', $controller['name'],
                "Controller {$controller['name']} should end with 'Controller'");
                
            // Verify controllers are in the correct namespace
            $this->assertStringStartsWith('App\\Http\\Controllers', $controller['namespace'],
                "Controller {$controller['name']} should be in App\\Http\\Controllers namespace");
        }
    }
}
```

### Advanced Architecture Validation

```php
class AdvancedArchitectureTest extends TestCase
{
    /** @test */
    public function services_have_proper_dependencies(): void
    {
        $servicesData = Atlas::scan('services', [
            'include_dependencies' => true,
            'include_methods' => true,
        ]);
        
        foreach ($servicesData['data'] as $service) {
            // Verify services don't depend on controllers
            $dependencies = $service['dependencies'] ?? [];
            
            foreach ($dependencies as $dependency) {
                $this->assertStringNotContainsString('Controller', $dependency,
                    "Service {$service['name']} should not depend on controllers");
            }
            
            // Verify services have meaningful public methods
            $methods = $service['methods'] ?? [];
            $publicMethods = array_filter($methods, fn($method) => $method['visibility'] === 'public');
            
            $this->assertGreaterThan(0, count($publicMethods),
                "Service {$service['name']} should have at least one public method");
        }
    }

    /** @test */
    public function models_have_proper_relationships(): void
    {
        $modelsData = Atlas::scan('models', [
            'include_relationships' => true,
        ]);
        
        foreach ($modelsData['data'] as $model) {
            $relationships = $model['relationships'] ?? [];
            
            // Verify relationship methods are properly named
            foreach ($relationships as $relationship) {
                if ($relationship['type'] === 'hasMany') {
                    $this->assertTrue(str_ends_with($relationship['method'], 's'),
                        "hasMany relationship {$relationship['method']} should be plural");
                }
                
                if ($relationship['type'] === 'belongsTo') {
                    $this->assertFalse(str_ends_with($relationship['method'], 's'),
                        "belongsTo relationship {$relationship['method']} should be singular");
                }
            }
        }
    }

    /** @test */
    public function form_requests_have_validation_rules(): void
    {
        $formRequestsData = Atlas::scan('form_requests', [
            'include_rules' => true,
            'include_authorization' => true,
        ]);
        
        foreach ($formRequestsData['data'] as $request) {
            // Verify form requests have rules
            $this->assertArrayHasKey('rules', $request,
                "Form request {$request['name']} should have validation rules");
                
            $this->assertNotEmpty($request['rules'],
                "Form request {$request['name']} should have non-empty validation rules");
                
            // Verify authorization method exists
            $this->assertArrayHasKey('authorization', $request,
                "Form request {$request['name']} should have authorization method");
        }
    }
}
```

## 📊 Architectural Metrics and Analysis

Use Atlas to gather metrics about your application architecture:

### Complexity Analysis

```php
class ArchitecturalComplexityTest extends TestCase
{
    /** @test */
    public function application_complexity_is_within_limits(): void
    {
        $allData = Atlas::scan('all');
        
        // Monitor total number of components
        $totalComponents = array_sum(array_column($allData, 'count'));
        $this->assertLessThan(1000, $totalComponents, 
            'Application has too many components - consider refactoring');
        
        // Monitor models complexity
        $modelsData = Atlas::scan('models', ['include_relationships' => true]);
        foreach ($modelsData['data'] as $model) {
            $relationshipCount = count($model['relationships'] ?? []);
            $this->assertLessThan(15, $relationshipCount,
                "Model {$model['name']} has too many relationships ({$relationshipCount})");
        }
        
        // Monitor controller complexity
        $controllersData = Atlas::scan('controllers', ['include_actions' => true]);
        foreach ($controllersData['data'] as $controller) {
            $actionCount = count($controller['actions'] ?? []);
            $this->assertLessThan(10, $actionCount,
                "Controller {$controller['name']} has too many actions ({$actionCount})");
        }
    }

    /** @test */
    public function services_follow_single_responsibility_principle(): void
    {
        $servicesData = Atlas::scan('services', ['include_methods' => true]);
        
        foreach ($servicesData['data'] as $service) {
            $methods = $service['methods'] ?? [];
            $publicMethods = array_filter($methods, fn($method) => $method['visibility'] === 'public');
            
            // Services should have focused responsibilities
            $this->assertLessThan(20, count($publicMethods),
                "Service {$service['name']} has too many public methods - consider splitting");
        }
    }
}
```

### Dependency Analysis

```php
class DependencyAnalysisTest extends TestCase
{
    /** @test */
    public function no_circular_dependencies_in_services(): void
    {
        $servicesData = Atlas::scan('services', ['include_dependencies' => true]);
        
        $dependencies = [];
        foreach ($servicesData['data'] as $service) {
            $serviceName = $service['name'];
            $serviceDeps = $service['dependencies'] ?? [];
            $dependencies[$serviceName] = $serviceDeps;
        }
        
        // Detect circular dependencies (simplified check)
        foreach ($dependencies as $service => $deps) {
            foreach ($deps as $dep) {
                if (isset($dependencies[$dep]) && in_array($service, $dependencies[$dep])) {
                    $this->fail("Circular dependency detected between {$service} and {$dep}");
                }
            }
        }
    }

    /** @test */
    public function controllers_only_depend_on_allowed_components(): void
    {
        $controllersData = Atlas::scan('controllers', ['include_dependencies' => true]);
        
        $allowedDependencies = [
            'App\\Services\\',
            'App\\Http\\Requests\\',
            'App\\Models\\',
            'Illuminate\\',
        ];
        
        foreach ($controllersData['data'] as $controller) {
            $dependencies = $controller['dependencies'] ?? [];
            
            foreach ($dependencies as $dependency) {
                $isAllowed = false;
                foreach ($allowedDependencies as $allowedPrefix) {
                    if (str_starts_with($dependency, $allowedPrefix)) {
                        $isAllowed = true;
                        break;
                    }
                }
                
                $this->assertTrue($isAllowed,
                    "Controller {$controller['name']} has disallowed dependency: {$dependency}");
            }
        }
    }
}
```

## 🚀 CI/CD Integration for Architecture Testing

Integrate Atlas into your CI/CD pipeline for continuous architecture monitoring:

### GitHub Actions Example

```yaml
name: Architecture Tests

on: [push, pull_request]

jobs:
  architecture:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: 8.3
        
    - name: Install Dependencies
      run: composer install --no-progress --prefer-dist --optimize-autoloader
      
    - name: Run Architecture Tests
      run: php artisan test --filter=ArchitectureTest
      
    - name: Generate Architecture Report
      run: |
        php artisan atlas:export --format=html --output=storage/architecture-report.html
        php artisan atlas:export --format=json --output=storage/architecture-data.json
        
    - name: Upload Architecture Report
      uses: actions/upload-artifact@v3
      with:
        name: architecture-report
        path: storage/architecture-*
```

### Laravel Test Command Integration

```php
// tests/Feature/ArchitectureValidationTest.php
class ArchitectureValidationTest extends TestCase
{
    protected static $architectureData;
    
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        
        // Generate architecture data once for all tests
        self::$architectureData = [
            'models' => Atlas::scan('models', ['include_relationships' => true]),
            'routes' => Atlas::scan('routes', ['include_middleware' => true]),
            'controllers' => Atlas::scan('controllers', ['include_actions' => true]),
            'services' => Atlas::scan('services', ['include_dependencies' => true]),
        ];
    }
    
    /** @test */
    public function validates_complete_architecture(): void
    {
        // Use cached data for faster tests
        $this->assertArchitectureStandards(self::$architectureData);
    }
    
    private function assertArchitectureStandards(array $data): void
    {
        // Your architecture validation logic here
        $this->assertModelStandards($data['models']);
        $this->assertRouteStandards($data['routes']);
        $this->assertControllerStandards($data['controllers']);
        $this->assertServiceStandards($data['services']);
    }
}
```

## 📈 Architecture Monitoring and Reporting

### Generate Architecture Reports

```php
// Generate comprehensive architecture analysis for monitoring
$architectureReport = [
    'timestamp' => now()->toISOString(),
    'components' => [],
    'metrics' => [],
    'issues' => [],
];

// Scan all component types
$componentTypes = ['models', 'routes', 'commands', 'services', 'notifications', 
                  'middlewares', 'form_requests', 'events', 'controllers', 'jobs'];

foreach ($componentTypes as $type) {
    $data = Atlas::scan($type, ['detailed' => true]);
    $architectureReport['components'][$type] = [
        'count' => $data['count'],
        'complexity' => $this->calculateComplexity($data),
        'issues' => $this->detectIssues($data),
    ];
}

// Save report for tracking over time
file_put_contents(
    storage_path('architecture/report-' . date('Y-m-d-H-i-s') . '.json'),
    json_encode($architectureReport, JSON_PRETTY_PRINT)
);
```

## 🎯 Best Practices for Architecture Testing

### 1. Test Automation
- Integrate Atlas scans into your automated test suite
- Run architecture tests on every pull request
- Generate reports for architecture changes

### 2. Continuous Monitoring
- Track architectural metrics over time
- Set thresholds for component complexity
- Monitor dependency relationships

### 3. Documentation Generation
- Generate architecture documentation automatically
- Update documentation on significant changes
- Provide visual representations for team reviews

### 4. Team Integration
- Use HTML exports for team architecture reviews
- Share PDF reports in design documents
- Integrate JSON data with monitoring tools

## 🔧 Custom Architecture Rules

Create custom validation rules for your specific architecture requirements:

```php
class CustomArchitectureValidation
{
    public function validateLayeredArchitecture(): array
    {
        $issues = [];
        
        // Controllers should only use Services and Requests
        $controllers = Atlas::scan('controllers', ['include_dependencies' => true]);
        foreach ($controllers['data'] as $controller) {
            $dependencies = $controller['dependencies'] ?? [];
            foreach ($dependencies as $dep) {
                if (!$this->isAllowedControllerDependency($dep)) {
                    $issues[] = "Controller {$controller['name']} has invalid dependency: {$dep}";
                }
            }
        }
        
        // Services should not depend on Controllers or Requests
        $services = Atlas::scan('services', ['include_dependencies' => true]);
        foreach ($services['data'] as $service) {
            $dependencies = $service['dependencies'] ?? [];
            foreach ($dependencies as $dep) {
                if ($this->isControllerOrRequest($dep)) {
                    $issues[] = "Service {$service['name']} should not depend on: {$dep}";
                }
            }
        }
        
        return $issues;
    }
    
    private function isAllowedControllerDependency(string $dependency): bool
    {
        return str_starts_with($dependency, 'App\\Services\\') ||
               str_starts_with($dependency, 'App\\Http\\Requests\\') ||
               str_starts_with($dependency, 'Illuminate\\');
    }
    
    private function isControllerOrRequest(string $dependency): bool
    {
        return str_contains($dependency, 'Controller') ||
               str_contains($dependency, 'Request');
    }
}
```

## 📚 Additional Resources

- **[Component Types Documentation](components.md)** - Detailed information about all 16 component types
- **[Export Formats Guide](export-formats.md)** - Different export formats for various use cases
- **[Examples Directory](../examples/)** - Working examples demonstrating Atlas functionality
- **[Testing Examples](../examples/testing-example.php)** - Specific examples for testing use cases

---

**💡 Pro Tip**: Use Atlas's facade methods in your PHPUnit tests to create comprehensive architecture validation that runs automatically with your test suite. This ensures your application maintains its intended architectural patterns as it grows and evolves.