<?php

declare(strict_types=1);

use LaravelAtlas\Mappers\ServiceMapper;
use LaravelAtlas\Mappers\ControllerMapper;

it('service mapper extracts dependencies from constructor', function (): void {
    $mapper = new ServiceMapper();

    expect($mapper)->toBeInstanceOf(ServiceMapper::class)
        ->and($mapper->getType())->toBe('services');
});

it('controller mapper extracts dependencies from constructor', function (): void {
    $mapper = new ControllerMapper();

    expect($mapper)->toBeInstanceOf(ControllerMapper::class)
        ->and($mapper->getType())->toBe('controllers');
});

it('service mapper scan returns expected structure', function (): void {
    $mapper = new ServiceMapper();
    $result = $mapper->scan();

    expect($result)->toBeArray()
        ->and($result)->toHaveKey('count')
        ->and($result)->toHaveKey('data')
        ->and($result['count'])->toBeInt()
        ->and($result['data'])->toBeArray();
});

it('controller mapper scan returns expected structure', function (): void {
    $mapper = new ControllerMapper();
    $result = $mapper->scan();

    expect($result)->toBeArray()
        ->and($result)->toHaveKey('count')
        ->and($result)->toHaveKey('data')
        ->and($result['count'])->toBeInt()
        ->and($result['data'])->toBeArray();
});
