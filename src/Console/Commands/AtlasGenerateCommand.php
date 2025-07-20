<?php

declare(strict_types=1);

namespace Grazulex\LaravelAtlas\Console\Commands;

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
use Illuminate\Console\Command;

class AtlasGenerateCommand extends Command
{
    protected $signature = 'atlas:generate 
                            {--type=all : Type of component to map (models, routes, jobs, all)}
                            {--format=json : Output format (json, mermaid, markdown, pdf, html)}
                            {--output= : Output file path}
                            {--detailed : Include detailed information}';

    protected $description = 'Generate an architecture map of your Laravel app';

    /**
     * @var array<string, class-string<MapperInterface>>
     */
    protected array $availableMappers = [
        'models' => ModelMapper::class,
        'routes' => RouteMapper::class,
        'jobs' => JobMapper::class,
    ];

    /**
     * @var array<string, class-string<ExporterInterface>>
     */
    protected array $availableExporters = [
        'json' => JsonExporter::class,
        'mermaid' => MermaidExporter::class,
        'markdown' => MarkdownExporter::class,
        'pdf' => PdfExporter::class,
        'html' => HtmlExporter::class,
    ];

    public function handle(): int
    {
        $this->info('🗺️  Generating Laravel Atlas map...');

        /** @var string $type */
        $type = $this->option('type') ?: 'all';
        /** @var string|null $format */
        $format = $this->option('format');
        $format = $format ?: 'json';

        /** @var bool $detailed */
        $detailed = (bool) $this->option('detailed');

        // Validate type
        if ($type !== 'all' && ! isset($this->availableMappers[$type])) {
            $this->error("Invalid type: {$type}. Available types: " . implode(', ', array_keys($this->availableMappers)) . ', all');

            return self::FAILURE;
        }

        // Validate format
        if (! isset($this->availableExporters[$format])) {
            $this->error("Invalid format: {$format}. Available formats: " . implode(', ', array_keys($this->availableExporters)));

            return self::FAILURE;
        }

        // Get mappers to run
        $mappersToRun = $type === 'all' ? $this->availableMappers : [$type => $this->availableMappers[$type]];

        /** @var array<string, array<string, mixed>> $results */
        $results = [];
        $startTime = microtime(true);

        // Run mappers
        foreach ($mappersToRun as $mapperType => $mapperClass) {
            $this->line("📊 Mapping {$mapperType}...");

            /** @var MapperInterface $mapper */
            $mapper = new $mapperClass;
            $options = $detailed ? ['include_detailed' => true] : [];

            $results[$mapperType] = $mapper->scan($options);

            $data = $results[$mapperType]['data'] ?? [];
            $count = is_countable($data) ? count($data) : 0;
            $this->info("   ✓ Found {$count} {$mapperType}");
        }

        $totalTime = round((microtime(true) - $startTime) * 1000, 2);

        // Generate output
        /** @var array<string, mixed> $output */
        $output = [
            'atlas_version' => '1.0.0',
            'generated_at' => now()->toISOString(),
            'generation_time_ms' => $totalTime,
            'type' => $type,
            'format' => $format,
            'options' => [
                'detailed' => $detailed,
            ],
            'summary' => $this->generateSummary($results),
            'data' => $results,
        ];

        // Output results
        /** @var string|null $outputPath */
        $outputPath = $this->option('output');
        if ($outputPath) {
            $this->saveToFile($output, $outputPath, $format);
        } else {
            $this->displayResults($output, $format);
        }

        $this->newLine();
        $this->info('✔ Map generated successfully!');
        $this->line("⏱️  Generation time: {$totalTime}ms");

        return self::SUCCESS;
    }

    /**
     * @param  array<string, array<string, mixed>>  $results
     *
     * @return array<string, mixed>
     */
    protected function generateSummary(array $results): array
    {
        $totalComponents = 0;
        /** @var array<string, int> $summary */
        $summary = [];

        foreach ($results as $type => $data) {
            $dataContent = $data['data'] ?? [];
            $count = is_countable($dataContent) ? count($dataContent) : 0;
            $totalComponents += $count;
            $summary[$type] = $count;
        }

        return [
            'total_components' => $totalComponents,
            'by_type' => $summary,
        ];
    }

    /**
     * @param  array<string, mixed>  $output
     */
    protected function saveToFile(array $output, string $path, string $format): void
    {
        $directory = dirname($path);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Use the appropriate exporter
        if (isset($this->availableExporters[$format])) {
            $exporterClass = $this->availableExporters[$format];
            /** @var ExporterInterface $exporter */
            $exporter = new $exporterClass;

            $content = $exporter->export($output);

            // For PDF and HTML, we need to handle binary content
            file_put_contents($path, $content);
        } else {
            // Fallback to JSON
            file_put_contents($path, json_encode($output, JSON_PRETTY_PRINT));
        }

        $this->info("💾 Output saved to: {$path}");
    }

    /**
     * @param  array<string, mixed>  $output
     */
    protected function displayResults(array $output, string $format): void
    {
        // Don't display binary formats in terminal
        if ($format === 'pdf') {
            $this->warn('⚠️  PDF format cannot be displayed in terminal. Use --output option to save to file.');

            return;
        }

        if (isset($this->availableExporters[$format])) {
            $exporterClass = $this->availableExporters[$format];
            /** @var ExporterInterface $exporter */
            $exporter = new $exporterClass;

            $content = $exporter->export($output);
            $this->line($content);
        } else {
            // Fallback to JSON
            $jsonContent = json_encode($output, JSON_PRETTY_PRINT);
            $this->line($jsonContent ?: 'JSON encoding failed');
        }
    }
}
