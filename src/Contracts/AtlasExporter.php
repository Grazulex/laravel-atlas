<?php

namespace LaravelAtlas\Contracts;

interface AtlasExporter
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function render(array $data): string;
}
