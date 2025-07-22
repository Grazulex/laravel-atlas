<?php

declare(strict_types=1);

namespace LaravelAtlas\Registry;

use LaravelAtlas\Mappers\Contracts\ComponentMapper;

class MapperRegistry
{
    /** @var array<string, ComponentMapper> */
    protected array $mappers = [];

    public function register(ComponentMapper $mapper): void
    {
        $this->mappers[$mapper->type()] = $mapper;
    }

    public function get(string $type): ?ComponentMapper
    {
        return $this->mappers[$type] ?? null;
    }

    /**
     * @return array<string, ComponentMapper>
     */
    public function all(): array
    {
        return $this->mappers;
    }
}
