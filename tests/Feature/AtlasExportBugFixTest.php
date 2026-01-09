<?php

declare(strict_types=1);

beforeEach(function (): void {
    $atlasDir = $this->app->publicPath('atlas');
    if (! is_dir($atlasDir)) {
        mkdir($atlasDir, 0755, true);
    }
});

it('type all parameter does not cause mapper error', function (): void {
    // This test specifically verifies the fix for the bug:
    // "No mapper registered for type [all]" when using --type=all

    $this->artisan('atlas:export', [
        '--type' => 'all',
        '--format' => 'html',
    ])
        ->expectsOutput('🔍 Exporting all components as html...')
        ->assertSuccessful();

    $exportFile = $this->app->publicPath('atlas/export.html');

    expect($exportFile)->toBeFile()
        ->and(filesize($exportFile))->toBeGreaterThan(0);

    // Clean up
    if (file_exists($exportFile)) {
        unlink($exportFile);
    }
});

it('type all and no type produce same result', function (): void {
    $this->artisan('atlas:export', [
        '--type' => 'all',
        '--format' => 'html',
        '--output' => $this->app->publicPath('atlas/export-with-all.html'),
    ])->assertSuccessful();

    $this->artisan('atlas:export', [
        '--format' => 'html',
        '--output' => $this->app->publicPath('atlas/export-without-type.html'),
    ])->assertSuccessful();

    $fileWithAll = $this->app->publicPath('atlas/export-with-all.html');
    $fileWithoutType = $this->app->publicPath('atlas/export-without-type.html');

    expect($fileWithAll)->toBeFile()
        ->and($fileWithoutType)->toBeFile()
        ->and(file_get_contents($fileWithAll))->toBe(file_get_contents($fileWithoutType));

    // Clean up
    if (file_exists($fileWithAll)) {
        unlink($fileWithAll);
    }
    if (file_exists($fileWithoutType)) {
        unlink($fileWithoutType);
    }
});
