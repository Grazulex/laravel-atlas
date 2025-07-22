<?php

declare(strict_types=1);

namespace LaravelAtlas\Support;

use RuntimeException;

class JsonPrinter
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function pretty(array $data): string
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            throw new RuntimeException('Failed to encode data to JSON: ' . json_last_error_msg());
        }

        return $json;
    }
}
