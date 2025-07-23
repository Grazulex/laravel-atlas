<?php

declare(strict_types=1);

use LaravelAtlas\Mappers\CommandMapper;

describe('CommandMapper', function (): void {
    beforeEach(function (): void {
        $this->mapper = new CommandMapper;
    });

    test('it has correct type', function (): void {
        expect($this->mapper->type())->toBe('commands');
    });

    test('it scans for commands', function (): void {
        $result = $this->mapper->scan();

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data'])
            ->and($result['type'])->toBe('commands');
    });
});
