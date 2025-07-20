<?php

declare(strict_types=1);

namespace Grazulex\LaravelAtlas;

use Grazulex\LaravelAtlas\Contracts\ExporterInterface;
use Grazulex\LaravelAtlas\Contracts\MapperInterface;
use Grazulex\LaravelAtlas\Exporters\HtmlExporter;
use Grazulex\LaravelAtlas\Exporters\JsonExporter;
use Grazulex\LaravelAtlas\Exporters\MarkdownExporter;
use Grazulex\LaravelAtlas\Exporters\MermaidExporter;
use Grazulex\LaravelAtlas\Exporters\PdfExporter;
use Grazulex\LaravelAtlas\Mappers\JobMapper;
use Grazulex\LaravelAtlas\Mappers\ModelMapper;
use Grazulex\LaravelAtlas\Mappers\RouteMapper;
use InvalidArgumentException;

class AtlasManager
{
    /** @var array<string, string> */
    protected array $mappers = [
        'models' => ModelMapper::class,
        'routes' => RouteMapper::class,
        'jobs' => JobMapper::class,
    ];

    /** @var array<string, string> */
    protected array $exporters = [
        'json' => JsonExporter::class,
        'html' => HtmlExporter::class,
        'markdown' => MarkdownExporter::class,
        'mermaid' => MermaidExporter::class,
        'pdf' => PdfExporter::class,
    ];

    /**
     * Scan a specific type and return the data
     *
     * @param  array<string, mixed>  $options
     *
     * @return array<string, mixed>
     */
    public function scan(string $type, array $options = []): array
    {
        $mapper = $this->mapper($type);

        return $mapper->scan($options);
    }

    /**
     * Export a specific type to a format and return the content
     *
     * @param  array<string, mixed>  $options
     */
    public function export(string $type, string $format, array $options = []): string
    {
        $data = $this->scan($type, $options);
        $exporter = $this->exporter($format);

        // Set options if the exporter supports it
        if (method_exists($exporter, 'setOptions')) {
            $exporter->setOptions($options);
        }

        return $exporter->export($data);
    }

    /**
     * Generate multiple types and export to a format
     *
     * @param  array<string>|string  $types
     * @param  array<string, mixed>  $options
     */
    public function generate(array|string $types, string $format, array $options = []): string
    {
        if (is_string($types)) {
            return $this->export($types, $format, $options);
        }

        $allData = [];
        foreach ($types as $type) {
            $allData[$type] = $this->scan($type, $options);
        }

        $exporter = $this->exporter($format);

        // Set options if the exporter supports it
        if (method_exists($exporter, 'setOptions')) {
            $exporter->setOptions(array_merge($options, ['multi_type' => true]));
        }

        return $exporter->export($allData);
    }

    /**
     * Get a mapper instance
     */
    public function mapper(string $type): MapperInterface
    {
        if (! isset($this->mappers[$type])) {
            throw new InvalidArgumentException("Unknown mapper type: {$type}");
        }

        $mapperClass = $this->mappers[$type];

        return app($mapperClass);
    }

    /**
     * Get an exporter instance
     */
    public function exporter(string $format): ExporterInterface
    {
        if (! isset($this->exporters[$format])) {
            throw new InvalidArgumentException("Unknown exporter format: {$format}");
        }

        $exporterClass = $this->exporters[$format];

        return app($exporterClass);
    }

    /**
     * Get available mapper types
     *
     * @return array<string>
     */
    public function getAvailableTypes(): array
    {
        return array_keys($this->mappers);
    }

    /**
     * Get available export formats
     *
     * @return array<string>
     */
    public function getAvailableFormats(): array
    {
        return array_keys($this->exporters);
    }

    /**
     * Register a custom mapper
     */
    public function registerMapper(string $type, string $mapperClass): void
    {
        $this->mappers[$type] = $mapperClass;
    }

    /**
     * Register a custom exporter
     */
    public function registerExporter(string $format, string $exporterClass): void
    {
        $this->exporters[$format] = $exporterClass;
    }
}
