<?php

use Illuminate\Support\Facades\Route;
use LaravelAtlas\Mappers\RouteMapper;

test('handles complex localized routes similar to laravel-localized-routes package', function (): void {
    // Simulate what laravel-localized-routes might do:
    // Creating routes with complex closures that could cause problems

    // Define a closure that could be problematic in route actions
    $complexClosure = (fn () => response()->json(['message' => 'Localized response']));

    // Register routes similar to how laravel-localized-routes might do it
    Route::group(['prefix' => 'en'], function () use ($complexClosure): void {
        Route::get('/localized-page', $complexClosure)->name('en.localized.page');
    });

    Route::group(['prefix' => 'fr'], function () use ($complexClosure): void {
        Route::get('/page-localisee', $complexClosure)->name('fr.localized.page');
    });

    // This should work without throwing htmlspecialchars errors
    $this->artisan('atlas:export', [
        '--type' => 'routes',
        '--format' => 'html',
        '--output' => $this->app->publicPath('atlas/localized-test.html'),
    ])->assertSuccessful();

    // Verify the export file was created
    $exportFile = $this->app->publicPath('atlas/localized-test.html');
    $this->assertFileExists($exportFile);

    $content = file_get_contents($exportFile);

    // Verify both localized routes are present
    $this->assertStringContainsString('localized-page', $content);
    $this->assertStringContainsString('page-localisee', $content);
    $this->assertStringContainsString('en.localized.page', $content);
    $this->assertStringContainsString('fr.localized.page', $content);

    // Verify closure handling shows "Closure Function" instead of causing errors
    $this->assertStringContainsString('Closure Function', $content);

    // Clean up
    if (file_exists($exportFile)) {
        unlink($exportFile);
    }
});

test('route mapper handles closure objects without string conversion errors', function (): void {
    // Create a route with a Closure action that would cause the original error
    $problematicClosure = (fn (): string => 'This closure used to cause htmlspecialchars error');

    Route::get('/problematic-route', $problematicClosure)->name('problematic.route');

    $routeMapper = new RouteMapper;
    $result = $routeMapper->scan();

    // Find our problematic route
    $problematicRoute = collect($result['data'])->first(fn ($route): bool => $route['uri'] === 'problematic-route');

    expect($problematicRoute)->not->toBeNull();

    // Verify the action was converted to a string
    expect($problematicRoute['action'])->toBe('Closure');
    expect($problematicRoute['action'])->toBeString();

    // Verify all route data is safe for Blade templates
    expect($problematicRoute['uses'])->toBeNull();
    expect($problematicRoute['controller'])->toBeNull();
    expect($problematicRoute['is_closure'])->toBeTrue();

    // All string fields should be actual strings or null
    expect($problematicRoute['uri'])->toBeString();
    expect($problematicRoute['name'])->toBeString();
    expect($problematicRoute['action'])->toBeString();
});
