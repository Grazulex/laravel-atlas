<?php

declare(strict_types=1);

use LaravelAtlas\Mappers\ActionMapper;

describe('ActionMapper', function (): void {
    beforeEach(function (): void {
        $this->mapper = new ActionMapper;
    });

    test('it has correct type', function (): void {
        expect($this->mapper->type())->toBe('actions');
    });

    test('it scans for actions', function (): void {
        $result = $this->mapper->scan();

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data'])
            ->and($result['type'])->toBe('actions')
            ->and($result['data'])->toBeArray();
    });

    test('action data has required keys when actions exist', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $action) {
            expect($action)
                ->toHaveKeys([
                    'class',
                    'namespace',
                    'name',
                    'file',
                    'methods',
                    'constructor',
                    'dependencies',
                ]);
        }
    });

    test('methods have correct structure', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $action) {
            expect($action['methods'])->toBeArray();

            foreach ($action['methods'] as $method) {
                expect($method)
                    ->toBeArray()
                    ->toHaveKeys(['name', 'parameters', 'return_type']);
            }
        }
    });

    test('dependencies are array of strings', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $action) {
            expect($action['dependencies'])->toBeArray();

            foreach ($action['dependencies'] as $dependency) {
                expect($dependency)->toBeString();
            }
        }
    });
});
