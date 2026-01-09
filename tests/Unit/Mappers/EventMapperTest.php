<?php

declare(strict_types=1);

use LaravelAtlas\Mappers\EventMapper;

describe('EventMapper', function (): void {
    beforeEach(function (): void {
        $this->mapper = new EventMapper;
    });

    test('it has correct type', function (): void {
        expect($this->mapper->type())->toBe('events');
    });

    test('it scans for events', function (): void {
        $result = $this->mapper->scan();

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data'])
            ->and($result['type'])->toBe('events');
    });

    test('event data has required keys', function (): void {
        $result = $this->mapper->scan();

        expect($result['data'])->toBeArray();

        if ($result['count'] > 0) {
            $event = $result['data'][0];

            expect($event)
                ->toHaveKeys([
                    'class',
                    'namespace',
                    'name',
                    'file',
                    'traits',
                    'properties',
                    'broadcastable',
                    'channels',
                    'listeners',
                    'flow',
                ]);
        }
    });

    test('listeners have correct structure', function (): void {
        $result = $this->mapper->scan();

        expect($result['data'])->toBeArray();

        foreach ($result['data'] as $event) {
            expect($event['listeners'])->toBeArray();

            foreach ($event['listeners'] as $listener) {
                // Each listener must be an array with specific keys
                expect($listener)
                    ->toBeArray()
                    ->toHaveKeys(['class', 'name', 'source']);

                // The 'name' key must contain the class basename
                expect($listener['name'])->toBeString();
                expect($listener['class'])->toBeString();
                expect($listener['source'])->toBeString();
            }
        }
    });

    test('getEventListenerMap returns array', function (): void {
        $map = $this->mapper->getEventListenerMap();

        expect($map)->toBeArray();
    });

    test('properties have correct structure', function (): void {
        $result = $this->mapper->scan();

        expect($result['data'])->toBeArray();

        foreach ($result['data'] as $event) {
            expect($event['properties'])->toBeArray();

            foreach ($event['properties'] as $property) {
                expect($property)
                    ->toBeArray()
                    ->toHaveKeys(['name', 'type', 'hasDefault', 'nullable']);
            }
        }
    });

    test('flow has correct structure', function (): void {
        $result = $this->mapper->scan();

        expect($result['data'])->toBeArray();

        foreach ($result['data'] as $event) {
            expect($event['flow'])
                ->toBeArray()
                ->toHaveKeys(['jobs', 'events', 'notifications', 'models', 'dependencies']);

            expect($event['flow']['dependencies'])
                ->toBeArray()
                ->toHaveKeys(['models', 'services', 'notifications', 'facades', 'classes']);
        }
    });
});
