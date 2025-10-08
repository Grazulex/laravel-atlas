<?php

use LaravelAtlas\Mappers\ControllerMapper;

it('can scan controllers and extract dependencies correctly', function () {
    $mapper = new ControllerMapper;

    $result = $mapper->scan();

    expect($result)->toBeArray()
        ->and($result)->toHaveKey('type')
        ->and($result['type'])->toBe('controllers')
        ->and($result)->toHaveKey('data')
        ->and($result['data'])->toBeArray();
});

it('returns structured dependencies with correct format', function () {
    $mapper = new ControllerMapper;

    $result = $mapper->scan();

    // If there are controllers, verify the structure
    if (! empty($result['data'])) {
        $firstController = $result['data'][0];

        expect($firstController)->toHaveKey('dependencies')
            ->and($firstController['dependencies'])->toBeArray();

        // Dependencies should be a nested structure with keys: models, services, requests, resources, facades
        $expectedKeys = ['models', 'services', 'requests', 'resources', 'facades'];
        foreach ($expectedKeys as $key) {
            expect($firstController['dependencies'])->toHaveKey($key)
                ->and($firstController['dependencies'][$key])->toBeArray();
        }
    }
});
