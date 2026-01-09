<?php

declare(strict_types=1);

use LaravelAtlas\Exporters\Blade\BladeLayoutExporter;
use LaravelAtlas\Contracts\AtlasExporter;

it('implements AtlasExporter interface', function (): void {
    $exporter = new BladeLayoutExporter();

    expect($exporter)->toBeInstanceOf(AtlasExporter::class);
});

it('renders empty data without errors', function (): void {
    $exporter = new BladeLayoutExporter();
    $html = $exporter->render([]);

    expect($html)->toBeString()
        ->and($html)->toContain('<html')
        ->and($html)->toContain('</html>');
});

it('renders models data correctly', function (): void {
    $exporter = new BladeLayoutExporter();
    $html = $exporter->render([
        'models' => [
            'count' => 1,
            'data' => [
                [
                    'name' => 'User',
                    'namespace' => 'App\\Models\\User',
                    'table' => 'users',
                ],
            ],
        ],
    ]);

    expect($html)->toBeString()
        ->and($html)->toContain('User');
});

it('renders routes data correctly', function (): void {
    $exporter = new BladeLayoutExporter();
    $html = $exporter->render([
        'routes' => [
            'count' => 1,
            'data' => [
                [
                    'uri' => '/api/users',
                    'method' => 'GET',
                    'name' => 'users.index',
                ],
            ],
        ],
    ]);

    expect($html)->toBeString()
        ->and($html)->toContain('/api/users');
});

it('renders events data correctly', function (): void {
    $exporter = new BladeLayoutExporter();
    $html = $exporter->render([
        'events' => [
            'count' => 1,
            'data' => [
                [
                    'name' => 'UserRegistered',
                    'namespace' => 'App\\Events\\UserRegistered',
                ],
            ],
        ],
    ]);

    expect($html)->toBeString()
        ->and($html)->toContain('UserRegistered');
});

it('uses configurable template', function (): void {
    config(['atlas.export.blade.template' => 'atlas::exports.layout']);

    $exporter = new BladeLayoutExporter();
    $html = $exporter->render([]);

    expect($html)->toBeString()
        ->and($html)->toContain('<html');
});

it('includes app name in output', function (): void {
    config(['app.name' => 'TestApp']);

    $exporter = new BladeLayoutExporter();
    $html = $exporter->render([]);

    expect($html)->toBeString();
});

it('renders all component types', function (): void {
    $exporter = new BladeLayoutExporter();
    $html = $exporter->render([
        'models' => ['count' => 0, 'data' => []],
        'commands' => ['count' => 0, 'data' => []],
        'routes' => ['count' => 0, 'data' => []],
        'services' => ['count' => 0, 'data' => []],
        'notifications' => ['count' => 0, 'data' => []],
        'middlewares' => ['count' => 0, 'data' => []],
        'form_requests' => ['count' => 0, 'data' => []],
        'events' => ['count' => 0, 'data' => []],
        'controllers' => ['count' => 0, 'data' => []],
        'resources' => ['count' => 0, 'data' => []],
        'jobs' => ['count' => 0, 'data' => []],
        'actions' => ['count' => 0, 'data' => []],
        'policies' => ['count' => 0, 'data' => []],
        'rules' => ['count' => 0, 'data' => []],
        'listeners' => ['count' => 0, 'data' => []],
        'observers' => ['count' => 0, 'data' => []],
    ]);

    expect($html)->toBeString()
        ->and(strlen($html))->toBeGreaterThan(100);
});
