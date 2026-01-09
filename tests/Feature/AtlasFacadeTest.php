<?php

declare(strict_types=1);

use LaravelAtlas\Facades\Atlas;

it('can scan models via facade', function (): void {
    $result = Atlas::scan('models');

    expect($result)->toBeArray()
        ->and($result)->toHaveKey('count')
        ->and($result)->toHaveKey('data')
        ->and($result['count'])->toBeInt();
});

it('can scan routes via facade', function (): void {
    $result = Atlas::scan('routes');

    expect($result)->toBeArray()
        ->and($result)->toHaveKey('count')
        ->and($result)->toHaveKey('data');
});

it('can scan commands via facade', function (): void {
    $result = Atlas::scan('commands');

    expect($result)->toBeArray()
        ->and($result)->toHaveKey('count')
        ->and($result)->toHaveKey('data');
});

it('can scan events via facade', function (): void {
    $result = Atlas::scan('events');

    expect($result)->toBeArray()
        ->and($result)->toHaveKey('count')
        ->and($result)->toHaveKey('data');
});

it('can scan controllers via facade', function (): void {
    $result = Atlas::scan('controllers');

    expect($result)->toBeArray()
        ->and($result)->toHaveKey('count')
        ->and($result)->toHaveKey('data');
});

it('can scan services via facade', function (): void {
    $result = Atlas::scan('services');

    expect($result)->toBeArray()
        ->and($result)->toHaveKey('count')
        ->and($result)->toHaveKey('data');
});

it('throws exception for unknown type via facade', function (): void {
    Atlas::scan('unknown_type');
})->throws(InvalidArgumentException::class);
