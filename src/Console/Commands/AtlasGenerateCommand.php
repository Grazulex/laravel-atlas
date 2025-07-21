<?php

declare(strict_types=1);

namespace LaravelAtlas\Console\Commands;

use Illuminate\Console\Command;
use LaravelAtlas\AtlasManager;
use LaravelAtlas\Contracts\ExporterInterface;
use LaravelAtlas\Contracts\MapperInterface;
use LaravelAtlas\Exporters\HtmlExporter;
use LaravelAtlas\Exporters\ImageExporter;
use LaravelAtlas\Exporters\JsonExporter;
use LaravelAtlas\Exporters\MarkdownExporter;
use LaravelAtlas\Exporters\PdfExporter;
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
use LaravelAtlas\Support\DependencyChecker;
use RuntimeException;
use Throwable;

class AtlasGenerateCommand extends Command
{
    protected $signature = 'atlas:generate 
                            {--type=all : Type of component to map (models, routes, jobs, services, controllers, events, commands, middleware, policies, resources, notifications, requests, rules, all)}
                            {--format=json : Output format (json, image, markdown, pdf, html, php)}
                            {--output= : Output file path}
                            {--detailed : Include detailed information}';

    protected $description = 'Generate Laravel application atlas/map for architectural analysis';

    /**
     * @var array<string, class-string<MapperInterface>>
     */
    protected array $availableMappers = [
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

    /**
     * @var array<string, class-string<ExporterInterface>>
     */
    protected array $availableExporters = [
        'json' => JsonExporter::class,
        'image' => ImageExporter::class,
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

        // Validate format and check dependencies
        if (! isset($this->availableExporters[$format])) {
            $this->error("Invalid format: {$format}. Available formats: " . implode(', ', array_keys($this->availableExporters)));

            return self::FAILURE;
        }

        // Check dependencies for the requested format
        try {
            $this->checkFormatDependencies($format);
        } catch (RuntimeException $e) {
            $this->error("❌ {$e->getMessage()}");

            return self::FAILURE;
        }

        // Get mappers to run
        $mappersToRun = $type === 'all' ? $this->availableMappers : [$type => $this->availableMappers[$type]];

        /** @var array<string, array<string, mixed>> $results */
        $results = [];
        $startTime = microtime(true);

        // Run mappers with error handling
        foreach ($mappersToRun as $mapperType => $mapperClass) {
            $this->line("📊 Mapping {$mapperType}...");

            try {
                /** @var MapperInterface $mapper */
                $mapper = new $mapperClass;
                $options = $detailed ? ['include_detailed' => true] : [];

                $results[$mapperType] = $mapper->scan($options);

                $data = $results[$mapperType]['data'] ?? [];
                $count = is_countable($data) ? count($data) : 0;
                $this->info("   ✓ Found {$count} {$mapperType}");
            } catch (Throwable $e) {
                $this->error("   ❌ Error mapping {$mapperType}: {$e->getMessage()}");
                $this->line("   Stack trace: {$e->getFile()}:{$e->getLine()}");

                // Continue with other mappers instead of failing completely
                $results[$mapperType] = [
                    'error' => $e->getMessage(),
                    'data' => [],
                ];
            }
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

        // For HTML format with multiple types, use intelligent workflow
        if ($format === 'html' && $this->shouldUseIntelligentHtml($output)) {
            $atlasManager = new AtlasManager();
            
            // Transform command data format to AtlasManager expected format
            $transformedData = [];
            foreach ($output['data'] ?? [] as $type => $typeData) {
                // Extract just the 'data' portion, skip errors
                $transformedData[$type] = $typeData['data'] ?? $typeData;
            }
            
            // TEMPORARY DEBUG: Use debug template
            $exporter = new HtmlExporter();
            $debugTemplate = base_path() . '/debug-template.blade.php';
            $content = $exporter->export($transformedData, $debugTemplate);
            file_put_contents($path, $content);
            $this->info("💾 Debug output saved to: {$path} (using debug template)");
            return;
        }

        // Use the appropriate exporter for other formats or simple HTML
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
     * Determine if we should use intelligent HTML template
     *
     * @param  array<string, mixed>  $output
     */
    protected function shouldUseIntelligentHtml(array $output): bool
    {
        $data = $output['data'] ?? [];
        
        // Use intelligent HTML if we have multiple component types
        // or if we have rich data that benefits from the intelligent template
        $componentTypes = array_keys($data);
        $hasMultipleTypes = count($componentTypes) > 1;
        
        // Also use intelligent template if we have models, controllers, or services
        // as these create meaningful relationships and flows
        $richTypes = ['models', 'controllers', 'services', 'jobs', 'events'];
        $hasRichTypes = !empty(array_intersect($componentTypes, $richTypes));
        
        return $hasMultipleTypes || $hasRichTypes;
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

        // For HTML with intelligent template, suggest saving to file
        if ($format === 'html' && $this->shouldUseIntelligentHtml($output)) {
            $this->warn('⚠️  HTML intelligent template is best viewed in a file. Use --output option to save to file.');
            $this->info('💡 Example: php artisan atlas:generate --type=all --format=html --output=atlas.html');

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

    /**
     * Check if required dependencies are available for the specified format
     */
    protected function checkFormatDependencies(string $format): void
    {
        $missing = DependencyChecker::getMissingDependencies($format);

        if ($missing !== []) {
            $installCommand = DependencyChecker::getInstallCommand($missing);
            throw new RuntimeException(
                "Missing dependencies for {$format} format: " . implode(', ', $missing) . "\n" .
                "Install them with: {$installCommand}"
            );
        }
    }
}
