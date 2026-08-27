<?php

declare(strict_types=1);

use LaravelAtlas\Contracts\AtlasExporter;
use LaravelAtlas\Exporters\Markdown\MarkdownExporter;

it('implements AtlasExporter interface', function (): void {
    $exporter = new MarkdownExporter;

    expect($exporter)->toBeInstanceOf(AtlasExporter::class);
});

it('renders empty data as a document title only', function (): void {
    $exporter = new MarkdownExporter;

    $markdown = $exporter->render([]);

    expect($markdown)->toBeString()
        ->and($markdown)->toContain('# Laravel Atlas');
});

it('renders a single type payload as a section with its count', function (): void {
    $exporter = new MarkdownExporter;

    $markdown = $exporter->render([
        'type' => 'models',
        'count' => 1,
        'data' => [
            ['class' => 'App\\Models\\User', 'table' => 'users'],
        ],
    ]);

    expect($markdown)->toContain('## Models')
        ->and($markdown)->toContain('1 component')
        ->and($markdown)->toContain('### App\\Models\\User')
        ->and($markdown)->toContain('**table:** users');
});

it('renders one section per type for a multi type payload', function (): void {
    $exporter = new MarkdownExporter;

    $markdown = $exporter->render([
        'models' => ['type' => 'models', 'count' => 1, 'data' => [['class' => 'App\\Models\\User']]],
        'form_requests' => ['type' => 'form_requests', 'count' => 1, 'data' => [['class' => 'App\\Http\\Requests\\StoreUser']]],
    ]);

    expect($markdown)->toContain('## Models')
        ->and($markdown)->toContain('## Form Requests')
        ->and($markdown)->toContain('### App\\Models\\User')
        ->and($markdown)->toContain('### App\\Http\\Requests\\StoreUser');
});

it('includes a summary table by default', function (): void {
    $exporter = new MarkdownExporter;

    $markdown = $exporter->render([
        'models' => ['type' => 'models', 'count' => 2, 'data' => [['class' => 'A'], ['class' => 'B']]],
    ]);

    expect($markdown)->toContain('## Summary')
        ->and($markdown)->toContain('| Type | Count |')
        ->and($markdown)->toContain('| models | 2 |');
});

it('omits the summary table when include_stats is false', function (): void {
    $exporter = new MarkdownExporter(['include_stats' => false]);

    $markdown = $exporter->render([
        'models' => ['type' => 'models', 'count' => 1, 'data' => [['class' => 'A']]],
    ]);

    expect($markdown)->not->toContain('## Summary');
});

it('lists component names only when detailed_sections is false', function (): void {
    $exporter = new MarkdownExporter(['detailed_sections' => false]);

    $markdown = $exporter->render([
        'models' => ['type' => 'models', 'count' => 1, 'data' => [['class' => 'App\\Models\\User', 'table' => 'users']]],
    ]);

    expect($markdown)->toContain('- App\\Models\\User')
        ->and($markdown)->not->toContain('### App\\Models\\User')
        ->and($markdown)->not->toContain('users');
});

it('renders nested arrays as nested bullet lists', function (): void {
    $exporter = new MarkdownExporter;

    $markdown = $exporter->render([
        'type' => 'models',
        'count' => 1,
        'data' => [
            [
                'class' => 'App\\Models\\User',
                'fillable' => ['name', 'email'],
                'flow' => ['jobs' => ['App\\Jobs\\SendWelcome']],
            ],
        ],
    ]);

    expect($markdown)->toContain('- **fillable:**')
        ->and($markdown)->toContain('  - name')
        ->and($markdown)->toContain('  - email')
        ->and($markdown)->toContain('- **flow:**')
        ->and($markdown)->toContain('  - **jobs:**')
        ->and($markdown)->toContain('    - App\\Jobs\\SendWelcome');
});

it('renders booleans and empty values readably', function (): void {
    $exporter = new MarkdownExporter;

    $markdown = $exporter->render([
        'type' => 'jobs',
        'count' => 1,
        'data' => [
            ['class' => 'App\\Jobs\\Ping', 'queueable' => true, 'properties' => [], 'file' => null],
        ],
    ]);

    expect($markdown)->toContain('- **queueable:** true')
        ->and($markdown)->toContain('- **properties:** _none_')
        ->and($markdown)->toContain('- **file:** _null_');
});

it('falls back to name or uri when a component has no class', function (): void {
    $exporter = new MarkdownExporter;

    $markdown = $exporter->render([
        'type' => 'routes',
        'count' => 1,
        'data' => [
            ['uri' => 'api/users', 'methods' => ['GET']],
        ],
    ]);

    expect($markdown)->toContain('### api/users');
});

it('reports an empty section instead of skipping it', function (): void {
    $exporter = new MarkdownExporter;

    $markdown = $exporter->render([
        'models' => ['type' => 'models', 'count' => 0, 'data' => []],
    ]);

    expect($markdown)->toContain('## Models')
        ->and($markdown)->toContain('_No components found._');
});
