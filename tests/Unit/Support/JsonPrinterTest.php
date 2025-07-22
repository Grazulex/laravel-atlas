<?php

declare(strict_types=1);

use LaravelAtlas\Support\JsonPrinter;

it('formats array data to pretty JSON', function () {
    $data = [
        'name' => 'Test',
        'values' => [1, 2, 3],
        'nested' => [
            'key' => 'value',
            'boolean' => true,
        ],
    ];

    $result = JsonPrinter::pretty($data);

    expect($result)->toBeString();
    expect($result)->toContain('"name": "Test"');
    expect($result)->toContain('"values": [');
    expect($result)->not()->toContain('\/'); // Should not escape slashes

    // Verify it's properly formatted JSON
    $decoded = json_decode($result, true);
    expect($decoded)->toEqual($data);
});

it('handles empty array', function () {
    $data = [];

    $result = JsonPrinter::pretty($data);

    expect($result)->toBe('{}');
});

it('handles complex nested data', function () {
    $data = [
        'models' => [
            [
                'name' => 'User',
                'attributes' => ['id', 'name', 'email'],
                'relations' => ['posts', 'profile'],
            ],
            [
                'name' => 'Post',
                'attributes' => ['id', 'title', 'content'],
                'relations' => ['user', 'comments'],
            ],
        ],
        'count' => 2,
    ];

    $result = JsonPrinter::pretty($data);

    expect($result)->toBeString();
    expect($result)->toContain('"models":');
    expect($result)->toContain('"count": 2');

    // Verify JSON is valid
    $decoded = json_decode($result, true);
    expect($decoded)->toEqual($data);
});

it('throws exception for invalid JSON encoding', function () {
    // Create data that cannot be JSON encoded (resources can't be encoded)
    $resource = fopen('php://memory', 'r');
    $data = ['resource' => $resource];

    expect(fn () => JsonPrinter::pretty($data))
        ->toThrow(\RuntimeException::class, 'Failed to encode data to JSON');

    fclose($resource);
});
