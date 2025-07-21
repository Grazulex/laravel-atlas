<?php

declare(strict_types=1);

namespace LaravelAtlas;

use InvalidArgumentException;
use LaravelAtlas\Contracts\ExporterInterface;
use LaravelAtlas\Contracts\MapperInterface;
use LaravelAtlas\Exporters\HtmlExporter;
use LaravelAtlas\Exporters\ImageExporter;
use LaravelAtlas\Exporters\JsonExporter;
use LaravelAtlas\Exporters\MarkdownExporter;
use LaravelAtlas\Exporters\PdfExporter;
use LaravelAtlas\Exporters\PhpExporter;
use LaravelAtlas\Mappers\CommandMapper;
use LaravelAtlas\Mappers\ControllerMapper;
use LaravelAtlas\Mappers\EventMapper;
use LaravelAtlas\Mappers\JobMapper;
use LaravelAtlas\Mappers\MiddlewareMapper;
use LaravelAtlas\Mappers\ModelMapper;
use LaravelAtlas\Mappers\NotificationMapper;
use LaravelAtlas\Mappers\PolicyMapper;
use LaravelAtlas\Mappers\RequestMapper;
use LaravelAtlas\Mappers\ResourceMapper;
use LaravelAtlas\Mappers\RouteMapper;
use LaravelAtlas\Mappers\RuleMapper;
use LaravelAtlas\Mappers\ServiceMapper;

class AtlasManager
{
    /** @var array<string, string> */
    protected array $mappers = [
        'models' => ModelMapper::class,
        'routes' => RouteMapper::class,
        'jobs' => JobMapper::class,
        'services' => ServiceMapper::class,
        'controllers' => ControllerMapper::class,
        'events' => EventMapper::class,
        'commands' => CommandMapper::class,
        'middleware' => MiddlewareMapper::class,
        'policies' => PolicyMapper::class,
        'resources' => ResourceMapper::class,
        'notifications' => NotificationMapper::class,
        'requests' => RequestMapper::class,
        'rules' => RuleMapper::class,
    ];

    /** @var array<string, string> */
    protected array $exporters = [
        'json' => JsonExporter::class,
        'html' => HtmlExporter::class,
        'markdown' => MarkdownExporter::class,
        'image' => ImageExporter::class,
        'pdf' => PdfExporter::class,
        'php' => PhpExporter::class,
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

        // Pour HTML, utiliser le workflow intelligent si on a des données complètes (type='all')
        if ($format === 'html' && ($type === 'all' || $this->hasIntelligentData($data))) {
            return $this->exportIntelligentHtml([$type => $data], $options);
        }

        $exporter = $this->exporter($format);

        // Set configuration options
        $exporter->setConfig($options);

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

        // Pour HTML avec des données multi-types, utiliser le workflow intelligent
        if ($format === 'html' && count($allData) > 1) {
            return $this->exportIntelligentHtml($allData, $options);
        }

        $exporter = $this->exporter($format);

        // Set configuration options
        $exporter->setConfig(array_merge($options, ['multi_type' => true]));

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

    /**
     * Export using the PHP->HTML intelligent workflow
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $config
     */
    public function exportIntelligentHtml(array $data, array $config = []): string
    {
        // 1. Générer le fichier PHP canonical
        $phpExporter = new PhpExporter($config);
        $phpCode = $phpExporter->export($data);

        // 2. Sauvegarder temporairement (optionnel pour debug)
        $tempPhpFile = sys_get_temp_dir() . '/atlas-temp-' . uniqid() . '.php';
        file_put_contents($tempPhpFile, $phpCode);

        try {
            // 3. Générer le HTML depuis le fichier PHP
            $htmlExporter = new HtmlExporter($config);

            return $htmlExporter->exportFromPhpFile($tempPhpFile);
        } finally {
            // Nettoyer le fichier temporaire
            if (file_exists($tempPhpFile)) {
                unlink($tempPhpFile);
            }
        }
    }

    /**
     * Check if data contains intelligent flows/connections information
     *
     * @param  array<string, mixed>  $data
     */
    protected function hasIntelligentData(array $data): bool
    {
        // Si on a plusieurs types de composants (comme avec type=all)
        if (isset($data['routes']) || isset($data['commands']) || isset($data['flows'])) {
            return true;
        }

        // Si on a des flows définis dans les routes
        foreach ($data as $items) {
            if (is_array($items)) {
                foreach ($items as $item) {
                    if (isset($item['flows']) || isset($item['connected_to'])) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
