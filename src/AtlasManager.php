<?php

declare(strict_types=1);

namespace LaravelAtlas;

use InvalidArgumentException;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Mappers\ModelMapper;
use LaravelAtlas\Registry\MapperRegistry;

class AtlasManager
{
    protected MapperRegistry $registry;

    public function __construct()
    {
        $this->registry = new MapperRegistry;

        // 🔧 Enregistrement statique (à améliorer via auto-discovery plus tard)
        $this->registry->register(new ModelMapper);
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @return array<string, mixed>
     */
    public function scan(string $type, array $options = []): array
    {
        $mapper = $this->registry->get($type);

        if (! $mapper instanceof ComponentMapper) {
            throw new InvalidArgumentException("No mapper registered for type [$type].");
        }

        return $mapper->scan($options);
    }

    public function registry(): MapperRegistry
    {
        return $this->registry;
    }
}
