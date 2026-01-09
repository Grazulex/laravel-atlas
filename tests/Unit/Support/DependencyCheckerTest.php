<?php

declare(strict_types=1);

use LaravelAtlas\Mappers\ServiceMapper;
use LaravelAtlas\Mappers\ControllerMapper;
use LaravelAtlas\Contracts\ComponentMapper;

it('service mapper implements ComponentMapper interface', function (): void {
    $mapper = new ServiceMapper();

    expect($mapper)->toBeInstanceOf(ComponentMapper::class);
});

it('controller mapper implements ComponentMapper interface', function (): void {
    $mapper = new ControllerMapper();

    expect($mapper)->toBeInstanceOf(ComponentMapper::class);
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
