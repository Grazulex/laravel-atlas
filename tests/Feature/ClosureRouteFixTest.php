<?php

use Illuminate\Support\Facades\Route;
use LaravelAtlas\Mappers\RouteMapper;

test('can handle closure routes without htmlspecialchars error', function (): void {
    // Add a closure route to the application
    Route::get('/test-closure', fn (): string => 'Hello from closure')->name('test.closure');

    // Ensure public/atlas directory exists
    $atlasDir = $this->app->publicPath('atlas');
    if (! is_dir($atlasDir)) {
        mkdir($atlasDir, 0755, true);
    }

    // This should not throw htmlspecialchars error anymore
    $this->artisan('atlas:export', [
        '--type' => 'routes',
        '--format' => 'html',
        '--output' => $this->app->publicPath('atlas/closure-test.html'),
    ])->assertSuccessful();

    // Verify the export file exists and contains our closure route
    $exportFile = $this->app->publicPath('atlas/closure-test.html');
    $this->assertFileExists($exportFile);

    $content = file_get_contents($exportFile);
    $this->assertStringContainsString('test-closure', $content);
    $this->assertStringContainsString('test.closure', $content);

    // Clean up
    if (file_exists($exportFile)) {
        unlink($exportFile);
    }
});

test('route mapper properly converts closure objects to strings', function (): void {
    // Add a closure route to test
    Route::get('/another-closure', fn (): string => 'Another closure');

    $routeMapper = new RouteMapper;
    $result = $routeMapper->scan();

    // Find our closure route in the results
    $closureRoute = collect($result['data'])->first(fn ($route): bool => $route['uri'] === 'another-closure');

    expect($closureRoute)->not->toBeNull();
    expect($closureRoute['action'])->toBe('Closure');
    expect($closureRoute['is_closure'])->toBeTrue();
    expect($closureRoute['uses'])->toBeNull(); // Should be null for closures
    expect($closureRoute['controller'])->toBeNull(); // Should be null for closures
});
