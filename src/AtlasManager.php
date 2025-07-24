<?php

declare(strict_types=1);

namespace LaravelAtlas;

use InvalidArgumentException;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Mappers\ActionMapper;
use LaravelAtlas\Mappers\CommandMapper;
use LaravelAtlas\Mappers\ControllerMapper;
use LaravelAtlas\Mappers\EventMapper;
use LaravelAtlas\Mappers\FormRequestMapper;
use LaravelAtlas\Mappers\JobMapper;
use LaravelAtlas\Mappers\ListenerMapper;
use LaravelAtlas\Mappers\MiddlewareMapper;
use LaravelAtlas\Mappers\ModelMapper;
use LaravelAtlas\Mappers\NotificationMapper;
use LaravelAtlas\Mappers\ObserverMapper;
use LaravelAtlas\Mappers\PolicyMapper;
use LaravelAtlas\Mappers\ResourceMapper;
use LaravelAtlas\Mappers\RouteMapper;
use LaravelAtlas\Mappers\RuleMapper;
use LaravelAtlas\Mappers\ServiceMapper;
use LaravelAtlas\Registry\MapperRegistry;

class AtlasManager
{
    protected MapperRegistry $registry;

    public function __construct()
    {
        $this->registry = new MapperRegistry;

        // 🔧 Enregistrement statique (à améliorer via auto-discovery plus tard)
        $this->registry->register(new ModelMapper);
        $this->registry->register(new CommandMapper);
        $this->registry->register(new RouteMapper);
        $this->registry->register(new ServiceMapper);
        $this->registry->register(new NotificationMapper);
        $this->registry->register(new MiddlewareMapper);
        $this->registry->register(new FormRequestMapper);
        $this->registry->register(new EventMapper);
        $this->registry->register(new ControllerMapper);
        $this->registry->register(new ResourceMapper);
        $this->registry->register(new JobMapper);
        $this->registry->register(new ActionMapper);
        $this->registry->register(new PolicyMapper);
        $this->registry->register(new RuleMapper);
        $this->registry->register(new ListenerMapper);
        $this->registry->register(new ObserverMapper);
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
