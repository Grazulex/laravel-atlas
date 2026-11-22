<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    $this->diffDir = sys_get_temp_dir() . '/atlas-diff-' . uniqid();
    mkdir($this->diffDir, 0755, true);

    $this->oldScan = $this->diffDir . '/old.json';
    $this->newScan = $this->diffDir . '/new.json';

    file_put_contents($this->oldScan, json_encode([
        'models' => ['type' => 'models', 'count' => 1, 'data' => [
            ['class' => 'App\\Models\\User', 'fillable' => ['name']],
        ]],
        'routes' => ['type' => 'routes', 'count' => 1, 'data' => [
            ['uri' => 'users', 'methods' => ['GET'], 'middleware' => ['web']],
        ]],
        'jobs' => ['type' => 'jobs', 'count' => 1, 'data' => [
            ['class' => 'App\\Jobs\\SendEmail'],
        ]],
        'events' => ['type' => 'events', 'count' => 0, 'data' => []],
    ]));

    file_put_contents($this->newScan, json_encode([
        'models' => ['type' => 'models', 'count' => 2, 'data' => [
            ['class' => 'App\\Models\\User', 'fillable' => ['name', 'email']],
            ['class' => 'App\\Models\\Invoice', 'fillable' => []],
        ]],
        'routes' => ['type' => 'routes', 'count' => 1, 'data' => [
            ['uri' => 'users', 'methods' => ['GET'], 'middleware' => ['web']],
        ]],
        'jobs' => ['type' => 'jobs', 'count' => 0, 'data' => []],
        'events' => ['type' => 'events', 'count' => 0, 'data' => []],
    ]));
});

afterEach(function (): void {
    File::deleteDirectory($this->diffDir);
});

it('prints the markdown diff', function (): void {
    $exitCode = Artisan::call('atlas:diff', [
        'old' => $this->oldScan,
        'new' => $this->newScan,
    ]);

    $output = Artisan::output();

    expect($exitCode)->toBe(0);
    expect($output)
        ->toContain('## Architecture diff')
        ->toContain('**3 changes**: 1 added, 1 removed, 1 changed.')
        ->toContain('- `App\\Models\\Invoice`')
        ->toContain('- `App\\Jobs\\SendEmail`')
        ->toContain('- `App\\Models\\User` — `fillable`');
});

it('writes the markdown diff to a file', function (): void {
    $report = $this->diffDir . '/reports/diff.md';

    $this->artisan('atlas:diff', [
        'old' => $this->oldScan,
        'new' => $this->newScan,
        '--output' => $report,
    ])
        ->expectsOutput("✅ Diff written to: {$report}")
        ->assertSuccessful();

    expect($report)->toBeFile();
    expect(file_get_contents($report))
        ->toContain('## Architecture diff')
        ->toContain('| Models | 1 | 0 | 1 |');
});

it('succeeds when the scans are identical', function (): void {
    $exitCode = Artisan::call('atlas:diff', [
        'old' => $this->oldScan,
        'new' => $this->oldScan,
        '--threshold' => '0',
    ]);

    expect($exitCode)->toBe(0);
    expect(Artisan::output())->toContain('No architecture changes detected.');
});

it('fails when the changes exceed the threshold', function (): void {
    $this->artisan('atlas:diff', [
        'old' => $this->oldScan,
        'new' => $this->newScan,
        '--threshold' => '2',
    ])
        ->expectsOutput('❌ 3 architecture change(s) detected, above the threshold of 2.')
        ->assertFailed();
});

it('succeeds when the changes stay within the threshold', function (): void {
    $this->artisan('atlas:diff', [
        'old' => $this->oldScan,
        'new' => $this->newScan,
        '--threshold' => '3',
    ])->assertSuccessful();
});

it('still writes the report when the threshold is exceeded', function (): void {
    $report = $this->diffDir . '/diff.md';

    $this->artisan('atlas:diff', [
        'old' => $this->oldScan,
        'new' => $this->newScan,
        '--output' => $report,
        '--threshold' => '0',
    ])->assertFailed();

    expect($report)->toBeFile();
});

it('rejects an invalid threshold', function (): void {
    $this->artisan('atlas:diff', [
        'old' => $this->oldScan,
        'new' => $this->newScan,
        '--threshold' => 'many',
    ])
        ->expectsOutput('❌ The --threshold option must be a non-negative integer.')
        ->assertFailed();
});

it('fails when a scan file is missing', function (): void {
    $missing = $this->diffDir . '/missing.json';

    $this->artisan('atlas:diff', [
        'old' => $missing,
        'new' => $this->newScan,
    ])
        ->expectsOutput("❌ Diff failed: Scan file not found or not readable: {$missing}")
        ->assertFailed();
});

it('fails when a scan file is not valid JSON', function (): void {
    $broken = $this->diffDir . '/broken.json';
    file_put_contents($broken, '{not json');

    $this->artisan('atlas:diff', [
        'old' => $this->oldScan,
        'new' => $broken,
    ])->assertFailed();
});

it('fails when the scans share no comparable component type', function (): void {
    $controllers = $this->diffDir . '/controllers.json';
    file_put_contents($controllers, json_encode(['type' => 'controllers', 'count' => 0, 'data' => []]));

    $this->artisan('atlas:diff', [
        'old' => $controllers,
        'new' => $controllers,
    ])
        ->expectsOutput('❌ Diff failed: the two scans share no comparable component type (models, routes, jobs, events).')
        ->assertFailed();
});

it('compares the real JSON export against itself without changes', function (): void {
    $export = $this->diffDir . '/export.json';

    $this->artisan('atlas:export', [
        '--type' => 'all',
        '--format' => 'json',
        '--output' => $export,
    ])->assertSuccessful();

    $this->artisan('atlas:diff', [
        'old' => $export,
        'new' => $export,
        '--threshold' => '0',
    ])->assertSuccessful();
});
