<?php

declare(strict_types=1);

use LaravelAtlas\Exporters\Pdf\PdfLayoutExporter;
use LaravelAtlas\Contracts\AtlasExporter;

it('implements AtlasExporter interface', function (): void {
    $exporter = new PdfLayoutExporter();

    expect($exporter)->toBeInstanceOf(AtlasExporter::class);
});

it('renders empty data without errors', function (): void {
    $exporter = new PdfLayoutExporter();
    $pdf = $exporter->render([]);

    expect($pdf)->toBeString()
        ->and(strlen($pdf))->toBeGreaterThan(0);
});

it('generates valid PDF content', function (): void {
    $exporter = new PdfLayoutExporter();
    $pdf = $exporter->render([]);

    // PDF files start with %PDF
    expect($pdf)->toStartWith('%PDF');
});

it('renders models data', function (): void {
    $exporter = new PdfLayoutExporter();
    $pdf = $exporter->render([
        'models' => [
            'count' => 1,
            'data' => [
                [
                    'name' => 'User',
                    'namespace' => 'App\\Models\\User',
                ],
            ],
        ],
    ]);

    expect($pdf)->toBeString()
        ->and($pdf)->toStartWith('%PDF');
});

it('renders routes data', function (): void {
    $exporter = new PdfLayoutExporter();
    $pdf = $exporter->render([
        'routes' => [
            'count' => 1,
            'data' => [
                [
                    'uri' => '/api/users',
                    'method' => 'GET',
                ],
            ],
        ],
    ]);

    expect($pdf)->toBeString()
        ->and($pdf)->toStartWith('%PDF');
});

it('renders all component types', function (): void {
    $exporter = new PdfLayoutExporter();
    $pdf = $exporter->render([
        'models' => ['count' => 0, 'data' => []],
        'commands' => ['count' => 0, 'data' => []],
        'routes' => ['count' => 0, 'data' => []],
        'services' => ['count' => 0, 'data' => []],
        'events' => ['count' => 0, 'data' => []],
    ]);

    expect($pdf)->toBeString()
        ->and($pdf)->toStartWith('%PDF')
        ->and(strlen($pdf))->toBeGreaterThan(1000);
});
