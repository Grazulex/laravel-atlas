<?php

declare(strict_types=1);

use LaravelAtlas\Mappers\ObserverMapper;

describe('ObserverMapper', function (): void {
    beforeEach(function (): void {
        $this->mapper = new ObserverMapper;
    });

    test('it has correct type', function (): void {
        expect($this->mapper->type())->toBe('observers');
    });

    test('it scans for observers', function (): void {
        $result = $this->mapper->scan();

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data'])
            ->and($result['type'])->toBe('observers')
            ->and($result['data'])->toBeArray();
    });

    test('observer data has required keys when observers exist', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $observer) {
            expect($observer)
                ->toHaveKeys([
                    'class',
                    'file',
                    'namespace',
                    'name',
                    'methods',
                    'model_events',
                    'model',
                    'is_abstract',
                    'is_final',
                ]);
        }
    });

    test('methods have correct structure', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $observer) {
            expect($observer['methods'])->toBeArray();

            foreach ($observer['methods'] as $method) {
                expect($method)
                    ->toBeArray()
                    ->toHaveKeys(['name', 'parameters', 'return_type', 'is_static']);
            }
        }
    });

    test('model_events contains valid event names', function (): void {
        $validEvents = [
            'retrieved', 'creating', 'created', 'updating', 'updated',
            'saving', 'saved', 'deleting', 'deleted', 'restoring', 'restored',
        ];

        $result = $this->mapper->scan();

        foreach ($result['data'] as $observer) {
            expect($observer['model_events'])->toBeArray();

            foreach ($observer['model_events'] as $event) {
                expect($event)->toBeIn($validEvents);
            }
        }
    });

    test('is_abstract and is_final are booleans', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $observer) {
            expect($observer['is_abstract'])->toBeBool();
            expect($observer['is_final'])->toBeBool();
        }
    });

    test('model is string or null', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $observer) {
            expect($observer['model'])->toBeIn([null, ...array_filter([$observer['model']], 'is_string')]);
        }
    });
});
