<?php

declare(strict_types=1);

use LaravelAtlas\Mappers\JobMapper;

describe('JobMapper', function (): void {
    beforeEach(function (): void {
        $this->mapper = new JobMapper;
    });

    test('it has correct type', function (): void {
        expect($this->mapper->type())->toBe('jobs');
    });

    test('it scans for jobs', function (): void {
        $result = $this->mapper->scan();

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data'])
            ->and($result['type'])->toBe('jobs')
            ->and($result['data'])->toBeArray();
    });

    test('job data has required keys when jobs exist', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $job) {
            expect($job)
                ->toHaveKeys([
                    'class',
                    'namespace',
                    'name',
                    'file',
                    'traits',
                    'queueable',
                    'properties',
                    'constructor',
                    'methods',
                    'queue_config',
                    'flow',
                ]);
        }
    });

    test('traits are array of strings', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $job) {
            expect($job['traits'])->toBeArray();

            foreach ($job['traits'] as $trait) {
                expect($trait)->toBeString();
            }
        }
    });

    test('queueable is boolean', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $job) {
            expect($job['queueable'])->toBeBool();
        }
    });

    test('constructor has parameters key', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $job) {
            expect($job['constructor'])
                ->toBeArray()
                ->toHaveKey('parameters');
        }
    });

    test('flow has correct structure', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $job) {
            expect($job['flow'])
                ->toBeArray()
                ->toHaveKeys(['jobs', 'events', 'notifications', 'dependencies']);

            expect($job['flow']['dependencies'])
                ->toBeArray()
                ->toHaveKeys(['models', 'services', 'facades', 'classes']);
        }
    });

    test('queue_config is array', function (): void {
        $result = $this->mapper->scan();

        foreach ($result['data'] as $job) {
            expect($job['queue_config'])->toBeArray();
        }
    });
});
