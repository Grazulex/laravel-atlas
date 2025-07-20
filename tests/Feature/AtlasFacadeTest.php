<?php

declare(strict_types=1);

use Grazulex\LaravelAtlas\Contracts\MapperInterface;
use Grazulex\LaravelAtlas\Contracts\ExporterInterface;
use Grazulex\LaravelAtlas\AtlasManager;
use Grazulex\LaravelAtlas\Facades\Atlas;

it('can access Atlas through facade', function (): void {
    expect(Atlas::getAvailableTypes())->toBeArray()
        ->and(Atlas::getAvailableTypes())->toContain('models', 'routes', 'jobs');
});

it('can scan models through facade', function (): void {
    $data = Atlas::scan('models');

    expect($data)->toBeArray()
        ->and($data)->toHaveKey('type')
        ->and($data['type'])->toBe('models');
});

it('can export through facade', function (): void {
    $json = Atlas::export('models', 'json');

    expect($json)->toBeString()
        ->and(json_decode($json, true))->toBeArray();
});

it('can get mapper instance through facade', function (): void {
    $mapper = Atlas::mapper('models');

    expect($mapper)->toBeInstanceOf(MapperInterface::class);
});

it('can get exporter instance through facade', function (): void {
    $exporter = Atlas::exporter('json');

    expect($exporter)->toBeInstanceOf(ExporterInterface::class);
});

it('can generate multiple types through facade', function (): void {
    $html = Atlas::generate(['models'], 'html');

    expect($html)->toBeString()
        ->and($html)->toContain('<!DOCTYPE html>');
});

it('resolves to AtlasManager singleton', function (): void {
    $manager1 = app(AtlasManager::class);
    $manager2 = app(AtlasManager::class);

    expect($manager1)->toBe($manager2);
});
