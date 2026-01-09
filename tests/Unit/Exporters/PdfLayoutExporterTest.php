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

it('renders all component types without errors', function (): void {
    $exporter = new PdfLayoutExporter();
    $pdf = $exporter->render([
        'models' => ['count' => 0, 'data' => []],
        'commands' => ['count' => 0, 'data' => []],
        'routes' => ['count' => 0, 'data' => []],
        'services' => ['count' => 0, 'data' => []],
        'events' => ['count' => 0, 'data' => []],
        'notifications' => ['count' => 0, 'data' => []],
        'middlewares' => ['count' => 0, 'data' => []],
        'form_requests' => ['count' => 0, 'data' => []],
        'controllers' => ['count' => 0, 'data' => []],
        'resources' => ['count' => 0, 'data' => []],
        'jobs' => ['count' => 0, 'data' => []],
        'actions' => ['count' => 0, 'data' => []],
        'policies' => ['count' => 0, 'data' => []],
        'rules' => ['count' => 0, 'data' => []],
        'listeners' => ['count' => 0, 'data' => []],
        'observers' => ['count' => 0, 'data' => []],
    ]);

    expect($pdf)->toBeString()
        ->and($pdf)->toStartWith('%PDF')
        ->and(strlen($pdf))->toBeGreaterThan(1000);
});

it('produces pdf with reasonable file size', function (): void {
    $exporter = new PdfLayoutExporter();
    $pdf = $exporter->render([]);

    // PDF should be at least 1KB
    expect(strlen($pdf))->toBeGreaterThan(1000);
});

it('pdf ends with eof marker', function (): void {
    $exporter = new PdfLayoutExporter();
    $pdf = $exporter->render([]);

    // PDF files typically end with %%EOF
    expect($pdf)->toContain('%%EOF');
});
