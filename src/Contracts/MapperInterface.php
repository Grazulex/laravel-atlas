<?php

declare(strict_types=1);

namespace Grazulex\LaravelAtlas\Contracts;

interface MapperInterface
{
    /**
     * Scan and map the components
     *
     * @param  array<string, mixed>  $options
     *
     * @return array<string, mixed>
     */
    public function scan(array $options = []): array;

    /**
     * Get the component type being mapped
     */
    public function getType(): string;

    /**
     * Get available scan options
     *
     * @return array<string, mixed>
     */
    public function getOptions(): array;
}
