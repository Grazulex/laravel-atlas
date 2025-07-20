<?php

declare(strict_types=1);

namespace Grazulex\LaravelAtlas\Exporters;

use Grazulex\LaravelAtlas\Contracts\ExporterInterface;

abstract class BaseExporter implements ExporterInterface
{
    /** @var array<string, mixed> */
    protected array $config;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->getDefaultConfig(), $config);
    }

    /**
     * Get default configuration for this exporter
     *
     * @return array<string, mixed>
     */
    abstract protected function getDefaultConfig(): array;

    /**
     * Get configuration value with default
     */
    protected function config(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }
}
