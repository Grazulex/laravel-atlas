<?php

declare(strict_types=1);

namespace LaravelAtlas\Exporters\Json;

use RuntimeException;

class JsonExporter
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function render(array $data): string
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            throw new RuntimeException('Failed to encode data to JSON');
        }

        return $json;
    }
}
