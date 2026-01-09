<?php

namespace LaravelAtlas\Contracts;

interface AtlasExporter
{
    public function render(array $data): string;
}
