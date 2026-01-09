<?php

declare(strict_types=1);

use LaravelAtlas\LaravelAtlasServiceProvider;

it('service provider is loaded', function (): void {
    $providers = $this->app->getLoadedProviders();

    expect($this->app)->not->toBeNull()
        ->and($providers)->toHaveKey(LaravelAtlasServiceProvider::class);
});

it('config is published', function (): void {
    expect(config('atlas.status_tracking.enabled', true))->toBeTrue();
});

it('package configuration is available', function (): void {
    expect($this->app)->not->toBeNull()
        ->and(config('atlas'))->toBeArray()
        ->and(config('atlas'))->toHaveKey('status_tracking');

    $statusTracking = config('atlas.status_tracking');

    expect($statusTracking)->toBeArray()
        ->and($statusTracking)->toHaveKey('enabled')
        ->and($statusTracking)->toHaveKey('file_path')
        ->and($statusTracking)->toHaveKey('track_history')
        ->and($statusTracking)->toHaveKey('max_entries');
});
