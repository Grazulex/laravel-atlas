<?php

declare(strict_types=1);

namespace LaravelAtlas\Contracts;

interface ComponentMapper
{
    public function type(): string;

    /**
     * @param  array<string, mixed>  $options
     *
     * @return array<string, mixed>
     */
    public function scan(array $options = []): array;
}
