<?php

declare(strict_types=1);

use LaravelAtlas\Exporters\Html\HtmlLayoutExporter;
use LaravelAtlas\Contracts\AtlasExporter;

it('implements AtlasExporter interface', function (): void {
    $exporter = new HtmlLayoutExporter();

    expect($exporter)->toBeInstanceOf(AtlasExporter::class);
});

it('renders empty data without errors', function (): void {
    $exporter = new HtmlLayoutExporter();
    $html = $exporter->render([]);

    expect($html)->toBeString()
        ->and($html)->toContain('<html')
        ->and($html)->toContain('</html>');
});

it('includes project name from composer.json', function (): void {
    $exporter = new HtmlLayoutExporter();
    $html = $exporter->render([]);

    expect($html)->toBeString()
        ->and($html)->toContain('Atlas');
});

it('renders all component types without errors', function (): void {
    $exporter = new HtmlLayoutExporter();
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

it('generates valid html document', function (): void {
    $exporter = new HtmlLayoutExporter();
    $html = $exporter->render([]);

    expect($html)->toContain('<!DOCTYPE html>')
        ->and($html)->toContain('<head>')
        ->and($html)->toContain('</html>');
});

it('includes styles', function (): void {
    $exporter = new HtmlLayoutExporter();
    $html = $exporter->render([]);

    // Check for either inline styles or stylesheet link
    $hasStyles = str_contains($html, '<style') || str_contains($html, 'stylesheet');
    expect($hasStyles)->toBeTrue();
});

it('includes javascript for interactivity', function (): void {
    $exporter = new HtmlLayoutExporter();
    $html = $exporter->render([]);

    expect($html)->toContain('<script');
});
