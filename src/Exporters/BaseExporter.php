<?php

declare(strict_types=1);

namespace LaravelAtlas\Exporters;

use LaravelAtlas\Contracts\ExporterInterface;

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
    
    /**
     * Set configuration options
     *
     * @param array<string, mixed> $config
     */
    public function setConfig(array $config): self
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }
    
    /**
     * Generate a diagram image from the analysis data
     * 
     * @param array<string, mixed> $data Analysis data
     * @param string $format Image format (png, jpg, etc.)
     * @param int $width Image width
     * @param int $height Image height
     * @return string Binary image data
     */
    protected function generateDiagramImage(array $data, string $format = 'png', int $width = 1200, int $height = 800): string
    {
        $exporter = new ImageExporter();
        $exporter->setFormat($format);
        $exporter->setDimensions($width, $height);
        
        return $exporter->export($data);
    }
}
