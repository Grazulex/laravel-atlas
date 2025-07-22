<?php

declare(strict_types=1);

namespace LaravelAtlas\Exporters\Json;

class JsonExporter
{
    public function render(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
