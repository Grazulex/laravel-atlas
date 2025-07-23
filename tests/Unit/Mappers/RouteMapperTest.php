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
});
