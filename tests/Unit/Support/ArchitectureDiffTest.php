<?php

declare(strict_types=1);

use LaravelAtlas\Support\ArchitectureDiff;

/**
 * @param  array<string, array<int, array<string, mixed>>>  $components
 *
 * @return array<string, array<string, mixed>>
 */
function architectureDiffScan(array $components): array
{
    $scan = [];

    foreach ($components as $type => $data) {
        $scan[$type] = ['type' => $type, 'count' => count($data), 'data' => $data];
    }

    return $scan;
}

/**
 * @return array<string, array<string, mixed>>
 */
function architectureDiffOldScan(): array
{
    return architectureDiffScan([
        'models' => [
            ['class' => 'App\\Models\\User', 'file' => '/old/User.php', 'fillable' => ['name']],
            ['class' => 'App\\Models\\Post', 'file' => '/old/Post.php', 'fillable' => []],
        ],
        'routes' => [
            ['uri' => 'users', 'methods' => ['GET'], 'middleware' => ['web'], 'domain' => null],
            ['uri' => 'users', 'methods' => ['POST'], 'middleware' => ['web'], 'domain' => null],
        ],
        'jobs' => [
            ['class' => 'App\\Jobs\\SendEmail', 'queueable' => true],
        ],
        'events' => [],
    ]);
}

/**
 * @return array<string, array<string, mixed>>
 */
function architectureDiffNewScan(): array
{
    return architectureDiffScan([
        'models' => [
            ['class' => 'App\\Models\\User', 'file' => '/new/User.php', 'fillable' => ['name', 'email']],
            ['class' => 'App\\Models\\Invoice', 'file' => '/new/Invoice.php', 'fillable' => []],
        ],
        'routes' => [
            ['uri' => 'users', 'methods' => ['GET'], 'middleware' => ['web'], 'domain' => null],
            ['uri' => 'users', 'methods' => ['POST'], 'middleware' => ['web', 'auth'], 'domain' => null],
            ['uri' => 'invoices', 'methods' => ['GET'], 'middleware' => ['web'], 'domain' => null],
        ],
        'jobs' => [],
        'events' => [
            ['class' => 'App\\Events\\OrderShipped', 'broadcastable' => false],
        ],
    ]);
}

it('lists added, removed and changed components per type', function (): void {
    $diff = ArchitectureDiff::between(architectureDiffOldScan(), architectureDiffNewScan());

    expect($diff->types())->toBe(['models', 'routes', 'jobs', 'events']);

    expect($diff->added('models'))->toBe(['App\\Models\\Invoice']);
    expect($diff->removed('models'))->toBe(['App\\Models\\Post']);
    expect($diff->changed('models'))->toBe(['App\\Models\\User' => ['fillable']]);

    expect($diff->added('routes'))->toBe(['GET /invoices']);
    expect($diff->removed('routes'))->toBe([]);
    expect($diff->changed('routes'))->toBe(['POST /users' => ['middleware']]);

    expect($diff->added('jobs'))->toBe([]);
    expect($diff->removed('jobs'))->toBe(['App\\Jobs\\SendEmail']);

    expect($diff->added('events'))->toBe(['App\\Events\\OrderShipped']);
});

it('counts the changes', function (): void {
    $diff = ArchitectureDiff::between(architectureDiffOldScan(), architectureDiffNewScan());

    expect($diff->addedCount())->toBe(3);
    expect($diff->removedCount())->toBe(2);
    expect($diff->changedCount())->toBe(2);
    expect($diff->totalChanges())->toBe(7);
    expect($diff->hasChanges())->toBeTrue();
});

it('reports no change for identical scans', function (): void {
    $diff = ArchitectureDiff::between(architectureDiffOldScan(), architectureDiffOldScan());

    expect($diff->hasChanges())->toBeFalse();
    expect($diff->totalChanges())->toBe(0);
    expect($diff->toMarkdown())->toContain('No architecture changes detected.');
});

it('ignores file paths and the key order of associative data', function (): void {
    $old = architectureDiffScan([
        'models' => [
            ['class' => 'App\\Models\\User', 'file' => '/ci/base/User.php', 'casts' => ['id' => 'int', 'active' => 'bool']],
        ],
    ]);
    $new = architectureDiffScan([
        'models' => [
            ['file' => '/ci/head/User.php', 'casts' => ['active' => 'bool', 'id' => 'int'], 'class' => 'App\\Models\\User'],
        ],
    ]);

    expect(ArchitectureDiff::between($old, $new)->hasChanges())->toBeFalse();
});

it('treats a reordered list as a change', function (): void {
    $old = architectureDiffScan([
        'routes' => [['uri' => '/', 'methods' => ['GET'], 'middleware' => ['web', 'auth']]],
    ]);
    $new = architectureDiffScan([
        'routes' => [['uri' => '/', 'methods' => ['GET'], 'middleware' => ['auth', 'web']]],
    ]);

    expect(ArchitectureDiff::between($old, $new)->changed('routes'))->toBe(['GET /' => ['middleware']]);
});

it('tells routes apart by methods and domain', function (): void {
    $old = architectureDiffScan([
        'routes' => [['uri' => 'users', 'methods' => ['GET'], 'domain' => null]],
    ]);
    $new = architectureDiffScan([
        'routes' => [
            ['uri' => 'users', 'methods' => ['GET'], 'domain' => null],
            ['uri' => 'users', 'methods' => ['PUT', 'PATCH'], 'domain' => null],
            ['uri' => 'users', 'methods' => ['GET'], 'domain' => 'api.example.com'],
        ],
    ]);

    expect(ArchitectureDiff::between($old, $new)->added('routes'))
        ->toBe(['GET api.example.com/users', 'PUT|PATCH /users']);
});

it('accepts the export of a single component type', function (): void {
    $old = ['type' => 'models', 'count' => 1, 'data' => [['class' => 'App\\Models\\User']]];
    $new = ['type' => 'models', 'count' => 2, 'data' => [['class' => 'App\\Models\\User'], ['class' => 'App\\Models\\Team']]];

    $diff = ArchitectureDiff::between($old, $new);

    expect($diff->types())->toBe(['models']);
    expect($diff->added('models'))->toBe(['App\\Models\\Team']);
    expect($diff->skipped())->toBe([]);
});

it('skips types present in only one scan', function (): void {
    $old = architectureDiffScan(['models' => [['class' => 'App\\Models\\User']]]);
    $new = architectureDiffScan([
        'models' => [['class' => 'App\\Models\\User']],
        'jobs' => [['class' => 'App\\Jobs\\SendEmail']],
    ]);

    $diff = ArchitectureDiff::between($old, $new);

    expect($diff->types())->toBe(['models']);
    expect($diff->skipped())->toBe(['jobs']);
    expect($diff->hasChanges())->toBeFalse();
    expect($diff->toMarkdown())->toContain('Not compared (present in only one of the two scans): jobs.');
});

it('ignores component types outside models, routes, jobs and events', function (): void {
    $old = architectureDiffScan(['controllers' => [['class' => 'App\\Http\\Controllers\\A']]]);
    $new = architectureDiffScan(['controllers' => []]);

    $diff = ArchitectureDiff::between($old, $new);

    expect($diff->types())->toBe([]);
    expect($diff->hasChanges())->toBeFalse();
});

it('renders a markdown report', function (): void {
    $markdown = ArchitectureDiff::between(architectureDiffOldScan(), architectureDiffNewScan())->toMarkdown();

    expect($markdown)
        ->toStartWith("## Architecture diff\n")
        ->toContain('**7 changes**: 3 added, 2 removed, 2 changed.')
        ->toContain('| Component | Added | Removed | Changed |')
        ->toContain('| Models | 1 | 1 | 1 |')
        ->toContain('| Routes | 1 | 0 | 1 |')
        ->toContain('| Jobs | 0 | 1 | 0 |')
        ->toContain('| Events | 1 | 0 | 0 |')
        ->toContain('### Models')
        ->toContain('- `App\\Models\\Invoice`')
        ->toContain('- `App\\Models\\Post`')
        ->toContain('- `App\\Models\\User` — `fillable`')
        ->toContain('- `POST /users` — `middleware`')
        ->toContain('- `App\\Events\\OrderShipped`');
});

it('uses the singular for a single change', function (): void {
    $old = architectureDiffScan(['events' => []]);
    $new = architectureDiffScan(['events' => [['class' => 'App\\Events\\OrderShipped']]]);

    expect(ArchitectureDiff::between($old, $new)->toMarkdown())
        ->toContain('**1 change**: 1 added, 0 removed, 0 changed.');
});

it('exposes the diff as an array', function (): void {
    $diff = ArchitectureDiff::between(architectureDiffOldScan(), architectureDiffNewScan());

    expect($diff->toArray()['models'])->toBe([
        'added' => ['App\\Models\\Invoice'],
        'removed' => ['App\\Models\\Post'],
        'changed' => ['App\\Models\\User' => ['fillable']],
    ]);
});

it('reads scans from JSON files', function (): void {
    $oldPath = tempnam(sys_get_temp_dir(), 'atlas-old-');
    $newPath = tempnam(sys_get_temp_dir(), 'atlas-new-');
    file_put_contents($oldPath, json_encode(architectureDiffOldScan()));
    file_put_contents($newPath, json_encode(architectureDiffNewScan()));

    try {
        expect(ArchitectureDiff::fromFiles($oldPath, $newPath)->totalChanges())->toBe(7);
    } finally {
        unlink($oldPath);
        unlink($newPath);
    }
});

it('rejects a missing scan file', function (): void {
    expect(fn (): ArchitectureDiff => ArchitectureDiff::fromFiles('/nonexistent/old.json', '/nonexistent/new.json'))
        ->toThrow(InvalidArgumentException::class, 'Scan file not found or not readable');
});

it('rejects a scan file with invalid JSON', function (): void {
    $path = tempnam(sys_get_temp_dir(), 'atlas-bad-');
    file_put_contents($path, '{not json');

    try {
        expect(fn (): ArchitectureDiff => ArchitectureDiff::fromFiles($path, $path))
            ->toThrow(InvalidArgumentException::class, 'Invalid JSON in scan file');
    } finally {
        unlink($path);
    }
});
