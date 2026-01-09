<?php

declare(strict_types=1);

namespace LaravelAtlas\Contracts;

interface AtlasExporter
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function render(array $data): string;
}
