<?php

declare(strict_types=1);

use LaravelAtlas\Exporters\Json\JsonExporter;
use LaravelAtlas\Contracts\AtlasExporter;

it('implements AtlasExporter interface', function (): void {
    $exporter = new JsonExporter();

    expect($exporter)->toBeInstanceOf(AtlasExporter::class);
});

it('renders empty data as valid JSON', function (): void {
    $exporter = new JsonExporter();
    $json = $exporter->render([]);

    expect($json)->toBeString()
        ->and(json_decode($json, true))->toBeArray();
});

it('renders models data correctly', function (): void {
    $exporter = new JsonExporter();
    $data = [
        'models' => [
            'count' => 1,
            'data' => [
                [
                    'name' => 'User',
                    'namespace' => 'App\\Models\\User',
                ],
            ],
        ],
    ];

    $json = $exporter->render($data);
    $decoded = json_decode($json, true);

    expect($decoded)->toBeArray()
        ->and($decoded)->toHaveKey('models')
        ->and($decoded['models']['count'])->toBe(1)
        ->and($decoded['models']['data'][0]['name'])->toBe('User');
});

it('renders routes data correctly', function (): void {
    $exporter = new JsonExporter();
    $data = [
        'routes' => [
            'count' => 2,
            'data' => [
                ['uri' => '/api/users', 'method' => 'GET'],
                ['uri' => '/api/posts', 'method' => 'POST'],
            ],
        ],
    ];

    $json = $exporter->render($data);
    $decoded = json_decode($json, true);

    expect($decoded)->toBeArray()
        ->and($decoded)->toHaveKey('routes')
        ->and($decoded['routes']['count'])->toBe(2);
});

it('produces pretty printed JSON', function (): void {
    $exporter = new JsonExporter();
    $json = $exporter->render(['test' => 'value']);

    // Pretty printed JSON contains newlines
    expect($json)->toContain("\n");
});

it('preserves all data types correctly', function (): void {
    $exporter = new JsonExporter();
    $data = [
        'string' => 'value',
        'int' => 42,
        'float' => 3.14,
        'bool' => true,
        'null' => null,
        'array' => [1, 2, 3],
    ];

    $json = $exporter->render($data);
    $decoded = json_decode($json, true);

    expect($decoded['string'])->toBe('value')
        ->and($decoded['int'])->toBe(42)
        ->and($decoded['float'])->toBe(3.14)
        ->and($decoded['bool'])->toBe(true)
        ->and($decoded['null'])->toBeNull()
        ->and($decoded['array'])->toBe([1, 2, 3]);
});

it('handles nested data structures', function (): void {
    $exporter = new JsonExporter();
    $data = [
        'models' => [
            'count' => 1,
            'data' => [
                [
                    'name' => 'User',
                    'relations' => [
                        ['name' => 'posts', 'type' => 'hasMany'],
                        ['name' => 'profile', 'type' => 'hasOne'],
                    ],
                ],
            ],
        ],
    ];

    $json = $exporter->render($data);
    $decoded = json_decode($json, true);

    expect($decoded['models']['data'][0]['relations'])->toHaveCount(2)
        ->and($decoded['models']['data'][0]['relations'][0]['type'])->toBe('hasMany');
});
