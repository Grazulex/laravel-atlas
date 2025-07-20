<?php

declare(strict_types=1);

namespace Grazulex\LaravelAtlas\Mappers;

use Grazulex\LaravelAtlas\Contracts\MapperInterface;
use Illuminate\Support\Collection;

abstract class BaseMapper implements MapperInterface
{
    /** @var array<string, mixed> */
    protected array $config;

    /** @var Collection<string, array<string, mixed>> */
    protected Collection $results;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->getDefaultOptions(), $config);
        /** @var Collection<string, array<string, mixed>> $results */
        $results = collect();
        $this->results = $results;
    }

    /**
     * Get default options for this mapper
     *
     * @return array<string, mixed>
     */
    abstract protected function getDefaultOptions(): array;

    /**
     * Perform the actual scanning
     *
     * @return Collection<string, array<string, mixed>>
     */
    abstract protected function performScan(): Collection;

    /**
     * {@inheritdoc}
     *
     * @param  array<string, mixed>  $options
     *
     * @return array<string, mixed>
     */
    public function scan(array $options = []): array
    {
        $this->config = array_merge($this->config, $options);
        $this->results = $this->performScan();

        return [
            'type' => $this->getType(),
            'timestamp' => now()->toISOString(),
            'config' => $this->config,
            'data' => $this->results->toArray(),
            'summary' => $this->getSummary(),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->config;
    }

    /**
     * Get a summary of the scan results
     *
     * @return array<string, mixed>
     */
    protected function getSummary(): array
    {
        return [
            'total_count' => $this->results->count(),
            'scan_time' => now()->toISOString(),
        ];
    }

    /**
     * Get configuration value with default
     */
    protected function config(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }

    /**
     * Add result to the collection
     *
     * @param  array<string, mixed>  $data
     */
    protected function addResult(string $key, array $data): void
    {
        $this->results->put($key, $data);
    }
}
