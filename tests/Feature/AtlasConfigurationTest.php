<?php

declare(strict_types=1);

it('atlas package can be disabled', function (): void {
    config(['atlas.enabled' => false]);

    expect(config('atlas.enabled'))->toBeFalse();
});

it('atlas status tracking can be configured', function (): void {
    config([
        'atlas.status_tracking.enabled' => false,
        'atlas.status_tracking.file_path' => '/tmp/custom_atlas.log',
        'atlas.status_tracking.max_entries' => 500,
    ]);

    expect(config('atlas.status_tracking.enabled'))->toBeFalse()
        ->and(config('atlas.status_tracking.file_path'))->toBe('/tmp/custom_atlas.log')
        ->and(config('atlas.status_tracking.max_entries'))->toBe(500);
});

it('atlas generation formats can be configured', function (): void {
    config([
        'atlas.generation.formats.image' => false,
        'atlas.generation.formats.json' => true,
        'atlas.generation.formats.markdown' => true,
        'atlas.generation.formats.blade' => true,
    ]);

    expect(config('atlas.generation.formats.image'))->toBeFalse()
        ->and(config('atlas.generation.formats.json'))->toBeTrue()
        ->and(config('atlas.generation.formats.markdown'))->toBeTrue()
        ->and(config('atlas.generation.formats.blade'))->toBeTrue();
});

it('atlas analysis settings are configurable', function (): void {
    config([
        'atlas.analysis.include_vendors' => true,
        'atlas.analysis.max_depth' => 15,
    ]);

    expect(config('atlas.analysis.include_vendors'))->toBeTrue()
        ->and(config('atlas.analysis.max_depth'))->toBe(15);
});

it('atlas default configuration values', function (): void {
    expect(config('atlas.enabled'))->toBeTrue()
        ->and(config('atlas.status_tracking.enabled'))->toBeTrue()
        ->and(config('atlas.status_tracking.track_history'))->toBeTrue()
        ->and(config('atlas.status_tracking.max_entries'))->toBe(1000)
        ->and(config('atlas.generation.formats.image'))->toBeTrue()
        ->and(config('atlas.generation.formats.json'))->toBeTrue()
        ->and(config('atlas.generation.formats.markdown'))->toBeTrue()
        ->and(config('atlas.generation.formats.blade'))->toBeTrue()
        ->and(config('atlas.analysis.include_vendors'))->toBeFalse()
        ->and(config('atlas.analysis.max_depth'))->toBe(10)
        ->and(config('atlas.analysis.scan_paths'))->toBeArray();
});
