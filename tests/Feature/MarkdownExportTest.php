<?php

declare(strict_types=1);

use LaravelAtlas\Exporters\AtlasExportManager;

beforeEach(function (): void {
    $atlasDir = $this->app->publicPath('atlas');
    if (! is_dir($atlasDir)) {
        mkdir($atlasDir, 0755, true);
    }
});

it('exports a single component type as markdown', function (): void {
    $markdown = AtlasExportManager::export('models', 'markdown');

    expect($markdown)->toBeString()
        ->and($markdown)->toContain('# Laravel Atlas')
        ->and($markdown)->toContain('## Models');
});

it('exports all component types as markdown', function (): void {
    $markdown = AtlasExportManager::exportAll('markdown');

    expect($markdown)->toContain('## Models')
        ->and($markdown)->toContain('## Routes')
        ->and($markdown)->toContain('## Form Requests');
});

it('forwards export options to the markdown exporter', function (): void {
    $markdown = AtlasExportManager::export('models', 'markdown', ['include_stats' => false]);

    expect($markdown)->not->toContain('## Summary');
});

it('exports markdown from the console command', function (): void {
    $output = $this->app->publicPath('atlas/markdown-map.md');

    $this->artisan('atlas:export', [
        '--type' => 'models',
        '--format' => 'markdown',
        '--output' => $output,
    ])->assertSuccessful();

    expect($output)->toBeFile()
        ->and(file_get_contents($output))->toContain('# Laravel Atlas');

    unlink($output);
});

it('uses the .md extension for the default markdown output path', function (): void {
    $this->artisan('atlas:export', [
        '--type' => 'models',
        '--format' => 'markdown',
    ])->assertSuccessful();

    $exportFile = $this->app->publicPath('atlas/models.md');

    expect($exportFile)->toBeFile();

    unlink($exportFile);
});
