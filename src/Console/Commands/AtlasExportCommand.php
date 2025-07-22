<?php

declare(strict_types=1);

namespace LaravelAtlas\Console\Commands;

use Illuminate\Console\Command;
use LaravelAtlas\Exporters\Html\ModelHtmlExporter;
use LaravelAtlas\Facades\Atlas;

class AtlasExportCommand extends Command
{
    protected $signature = 'atlas:export
        {type : The component type to scan (models, routes, etc.)}
        {--format=html : Export format (html, json, etc.)}
        {--output= : Output file path (default: public/atlas/{type}.html)}';

    protected $description = 'Export the given component type to a chosen format (default: HTML)';

    public function handle(): int
    {
        $type = $this->argument('type');
        $format = $this->option('format');
        $output = $this->option('output') ?? public_path("atlas/{$type}.{$format}");

        $this->info("Scanning {$type}...");
        $data = Atlas::scan($type);

        match ($format) {
            'html' => $this->exportHtml($data, $output),
            default => $this->error("Unsupported format: {$format}"),
        };

        $this->info("✅ Exported to {$output}");

        return self::SUCCESS;
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function exportHtml(array $data, string $output): void
    {
        // pour l'instant, seul `models` est supporté
        if ($data['type'] !== 'models') {
            $this->error("HTML export is only implemented for 'models' for now.");

            return;
        }

        $html = (new ModelHtmlExporter)->render($data);
        ensureDirectoryExists(dirname($output));
        file_put_contents($output, $html);
    }
}

if (! function_exists('ensureDirectoryExists')) {
    function ensureDirectoryExists(string $path): void
    {
        if (! is_dir($path)) {
            mkdir($path, recursive: true);
        }
    }
}
