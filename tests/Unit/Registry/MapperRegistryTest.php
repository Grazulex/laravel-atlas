<?php

declare(strict_types=1);

use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Registry\MapperRegistry;

it('can register a mapper', function (): void {
    $registry = new MapperRegistry;
    $mapper = Mockery::mock(ComponentMapper::class);
    $mapper->shouldReceive('type')->andReturn('test');

    $registry->register($mapper);

    expect($registry->get('test'))->toBe($mapper);
});

it('returns null for unknown mapper type', function (): void {
    $registry = new MapperRegistry;

    expect($registry->get('unknown'))->toBeNull();
});

it('can get all registered mappers', function (): void {
    $registry = new MapperRegistry;

    $mapper1 = Mockery::mock(ComponentMapper::class);
    $mapper1->shouldReceive('type')->andReturn('test1');

    $mapper2 = Mockery::mock(ComponentMapper::class);
    $mapper2->shouldReceive('type')->andReturn('test2');

    $registry->register($mapper1);
    $registry->register($mapper2);

    $all = $registry->all();

    expect($all)->toHaveCount(2);
    expect($all['test1'])->toBe($mapper1);
    expect($all['test2'])->toBe($mapper2);
});

it('overwrites mapper with same type', function (): void {
    $registry = new MapperRegistry;

    $mapper1 = Mockery::mock(ComponentMapper::class);
    $mapper1->shouldReceive('type')->andReturn('test');

    $mapper2 = Mockery::mock(ComponentMapper::class);
    $mapper2->shouldReceive('type')->andReturn('test');

    $registry->register($mapper1);
    $registry->register($mapper2);

    expect($registry->get('test'))->toBe($mapper2);
    expect($registry->all())->toHaveCount(1);
});
