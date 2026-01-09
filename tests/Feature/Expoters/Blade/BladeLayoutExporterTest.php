<?php

declare(strict_types=1);

afterEach(function (): void {
    // Clean up any created files
    $paths = [
        $this->app->resourcePath('views/atlas/export.blade.php'),
        $this->app->resourcePath('views/atlas/events.blade.php'),
        $this->app->resourcePath('views/docs/docs.blade.php'),
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            unlink($path);
            $directory = dirname($path);
            if (is_dir($directory) && count(scandir($directory)) === 2) {
                rmdir($directory);
            }
        }
    }
});

it('can export all components with type all in blade format', function (): void {
    $this->artisan('atlas:export', [
        '--type' => 'all',
        '--format' => 'blade',
    ])
        ->expectsOutput('🔍 Exporting all components as blade...')
        ->assertSuccessful();

    $exportFile = $this->app->resourcePath('views/atlas/export.blade.php');

    expect($exportFile)->toBeFile()
        ->and(filesize($exportFile))->toBeGreaterThan(0)
        ->and(file_get_contents($exportFile))->toContain('<div>');
});

it('can export specific components in blade format', function (): void {
    $this->artisan('atlas:export', [
        '--type' => 'events',
        '--format' => 'blade',
    ])
        ->expectsOutput('🔍 Exporting events as blade...')
        ->assertSuccessful();

    $exportFile = $this->app->resourcePath('views/atlas/events.blade.php');

    expect($exportFile)->toBeFile()
        ->and(filesize($exportFile))->toBeGreaterThan(0)
        ->and(file_get_contents($exportFile))->toContain('<div>');
});

it('accepts custom output path', function (): void {
    $customOutput = $this->app->resourcePath('views/docs/docs.blade.php');

    $docsDir = dirname($customOutput);
    if (! is_dir($docsDir)) {
        mkdir($docsDir, 0755, true);
    }

    $this->artisan('atlas:export', [
        '--type' => 'all',
        '--format' => 'blade',
        '--output' => $customOutput,
    ])
        ->expectsOutput('🔍 Exporting all components as blade...')
        ->assertSuccessful();

    expect($customOutput)->toBeFile()
        ->and(filesize($customOutput))->toBeGreaterThan(0)
        ->and(file_get_contents($customOutput))->toContain('<div>');
});
