<?php

declare(strict_types=1);

use LaravelAtlas\Mappers\ModelMapper;

describe('ModelMapper', function (): void {
    beforeEach(function (): void {
        $this->mapper = new ModelMapper;
    });

    test('it has correct type', function (): void {
        expect($this->mapper->type())->toBe('models');
    });

    test('it scans for models', function (): void {
        $result = $this->mapper->scan();

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data'])
            ->and($result['type'])->toBe('models')
            ->and($result['data'])->toBeArray();
    });

    test('model data has required keys when models exist', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $model) {
            expect($model)
                ->toHaveKeys([
                    'class',
                    'namespace',
                    'name',
                    'file',
                    'primary_key',
                    'table',
                    'fillable',
                    'guarded',
                    'casts',
                    'relations',
                    'scopes',
                    'booted_hooks',
                    'flow',
                ]);
        }
    });

    test('fillable and guarded are arrays', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $model) {
            expect($model['fillable'])->toBeArray();
            expect($model['guarded'])->toBeArray();
        }
    });

    test('casts is array', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $model) {
            expect($model['casts'])->toBeArray();
        }
    });

    test('relations have correct structure', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $model) {
            expect($model['relations'])->toBeArray();

            foreach ($model['relations'] as $name => $relation) {
                expect($relation)
                    ->toBeArray()
                    ->toHaveKeys(['type', 'related', 'foreignKey']);
            }
        }
    });

    test('scopes have correct structure', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $model) {
            expect($model['scopes'])->toBeArray();

            foreach ($model['scopes'] as $scope) {
                expect($scope)
                    ->toBeArray()
                    ->toHaveKeys(['name', 'parameters']);
            }
        }
    });

    test('flow has correct structure', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $model) {
            expect($model['flow'])
                ->toBeArray()
                ->toHaveKeys(['jobs', 'events', 'observers', 'dependencies']);
        }
    });
});
