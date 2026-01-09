<?php

declare(strict_types=1);

beforeEach(function (): void {
    $atlasDir = $this->app->publicPath('atlas');
    if (! is_dir($atlasDir)) {
        mkdir($atlasDir, 0755, true);
    }
});

it('can export all components with type all', function (): void {
    $this->artisan('atlas:export', [
        '--type' => 'all',
        '--format' => 'html',
    ])
        ->expectsOutput('🔍 Exporting all components as html...')
        ->assertSuccessful();

    $exportFile = $this->app->publicPath('atlas/export.html');

    expect($exportFile)->toBeFile();

    if (file_exists($exportFile)) {
        unlink($exportFile);
    }
});

it('can export all components without type option', function (): void {
    $this->artisan('atlas:export', [
        '--format' => 'html',
    ])
        ->expectsOutput('🔍 Exporting all components as html...')
        ->assertSuccessful();

    $exportFile = $this->app->publicPath('atlas/export.html');

    expect($exportFile)->toBeFile();

    if (file_exists($exportFile)) {
        unlink($exportFile);
    }
});

it('can export a specific component type', function (): void {
    $this->artisan('atlas:export', [
        '--type' => 'models',
        '--format' => 'html',
    ])
        ->expectsOutput('🔍 Exporting models as html...')
        ->assertSuccessful();

    $exportFile = $this->app->publicPath('atlas/models.html');

    expect($exportFile)->toBeFile();

    if (file_exists($exportFile)) {
        unlink($exportFile);
    }
});

it('supports different export formats', function (): void {
    $this->artisan('atlas:export', [
        '--type' => 'all',
        '--format' => 'json',
    ])
        ->expectsOutput('🔍 Exporting all components as json...')
        ->assertSuccessful();

    $jsonFile = $this->app->publicPath('atlas/export.json');

    expect($jsonFile)->toBeFile();

    if (file_exists($jsonFile)) {
        unlink($jsonFile);
    }
});

it('accepts custom output path', function (): void {
    $customOutput = $this->app->storagePath('app/custom-atlas-export.html');

    $storageDir = dirname($customOutput);
    if (! is_dir($storageDir)) {
        mkdir($storageDir, 0755, true);
    }

    $this->artisan('atlas:export', [
        '--type' => 'all',
        '--format' => 'html',
        '--output' => $customOutput,
    ])->assertSuccessful();

    expect($customOutput)->toBeFile();

    if (file_exists($customOutput)) {
        unlink($customOutput);
    }
});
