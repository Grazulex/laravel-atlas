<?php

declare(strict_types=1);

use LaravelAtlas\Mappers\RouteMapper;

describe('RouteMapper', function (): void {
    beforeEach(function (): void {
        $this->mapper = new RouteMapper;
    });

    test('it has correct type', function (): void {
        expect($this->mapper->type())->toBe('routes');
    });

    test('it scans all routes', function (): void {
        $result = $this->mapper->scan();

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data'])
            ->and($result['type'])->toBe('routes');
    });

    test('it includes grouping metadata', function (): void {
        $result = $this->mapper->scan();

        expect($result)
            ->toHaveKey('grouping')
            ->and($result['grouping'])->toBeArray()
            ->toHaveKeys(['by_prefix', 'by_type', 'by_middleware', 'filters', 'stats']);
    });

    test('grouping metadata contains correct filters', function (): void {
        $result = $this->mapper->scan();

        expect($result['grouping']['filters'])
            ->toBeArray()
            ->toHaveKeys(['prefixes', 'types', 'middlewares', 'methods']);
    });

    test('grouping stats contain correct counts', function (): void {
        $result = $this->mapper->scan();

        expect($result['grouping']['stats'])
            ->toBeArray()
            ->toHaveKeys(['total', 'by_method', 'by_type', 'by_prefix'])
            ->and($result['grouping']['stats']['total'])->toBe($result['count']);
    });

    test('routes are grouped by type', function (): void {
        $result = $this->mapper->scan();

        expect($result['grouping']['by_type'])
            ->toBeArray();

        foreach ($result['grouping']['by_type'] as $type => $routes) {
            expect($routes)->toBeArray();
            foreach ($routes as $route) {
                expect($route['type'])->toBe($type);
            }
        }
    });

    test('routes are grouped by prefix', function (): void {
        $result = $this->mapper->scan();

        expect($result['grouping']['by_prefix'])->toBeArray();
    });

    test('routes are grouped by middleware', function (): void {
        $result = $this->mapper->scan();

        expect($result['grouping']['by_middleware'])->toBeArray();
    });
});
