<?php

declare(strict_types=1);

use LaravelAtlas\AtlasManager;
use LaravelAtlas\Mappers\CommandMapper;
use LaravelAtlas\Mappers\FormRequestMapper;
use LaravelAtlas\Mappers\MiddlewareMapper;
use LaravelAtlas\Mappers\ModelMapper;
use LaravelAtlas\Mappers\NotificationMapper;
use LaravelAtlas\Mappers\RouteMapper;
use LaravelAtlas\Mappers\ServiceMapper;
use LaravelAtlas\Registry\MapperRegistry;

describe('AtlasManager', function (): void {
    test('it initializes with all default mappers registered', function (): void {
        $manager = new AtlasManager;
        $registry = $manager->registry();

        expect($registry->get('models'))->toBeInstanceOf(ModelMapper::class);
        expect($registry->get('commands'))->toBeInstanceOf(CommandMapper::class);
        expect($registry->get('routes'))->toBeInstanceOf(RouteMapper::class);
        expect($registry->get('services'))->toBeInstanceOf(ServiceMapper::class);
        expect($registry->get('notifications'))->toBeInstanceOf(NotificationMapper::class);
        expect($registry->get('middlewares'))->toBeInstanceOf(MiddlewareMapper::class);
        expect($registry->get('form_requests'))->toBeInstanceOf(FormRequestMapper::class);
    });

    test('it can scan with registered mapper', function (): void {
        $manager = new AtlasManager;

        $result = $manager->scan('models');

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data'])
            ->and($result['type'])->toBe('models')
            ->and($result['count'])->toBeInt()
            ->and($result['data'])->toBeArray();
    });

    test('it throws exception for unknown mapper type', function (): void {
        $manager = new AtlasManager;

        expect(fn (): array => $manager->scan('unknown_type'))
            ->toThrow(InvalidArgumentException::class, 'No mapper registered for type [unknown_type].');
    });

    test('it can scan with options', function (): void {
        $manager = new AtlasManager;

        $result = $manager->scan('models', ['paths' => [__DIR__ . '/../../Fixtures']]);

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data']);
    });

    test('it returns mapper registry', function (): void {
        $manager = new AtlasManager;

        expect($manager->registry())->toBeInstanceOf(MapperRegistry::class);
    });
});
