<?php

declare(strict_types=1);

namespace LaravelAtlas\Console\Commands;

use Illuminate\Console\Command;
use LaravelAtlas\Exporters\AtlasExportManager;

class AtlasExportCommand extends Command
{
    protected $signature = 'atlas:export {type : The component type to scan (models, routes, etc.)} {--format=html : Export format (html, json, markdown, etc.)} {--output= : Output file path}';

    protected $description = 'Export a component type (models, routes, etc.) to a chosen format (HTML, JSON, Markdown, etc.)';

    public function handle(): int
    {
        $type = $this->argument('type');
        $format = $this->option('format') ?? 'html';

        $output = $this->option('output') ?? public_path("atlas/{$type}.{$format}");

        $this->info("🔍 Exporting {$type} as {$format}...");

        try {
            $content = AtlasExportManager::export($type, $format);
        } catch (\Throwable $e) {
            $this->error("❌ Export failed: " . $e->getMessage());
            return self::FAILURE;
        }

        $this->ensureDirectory(dirname($output));
        file_put_contents($output, $content);

        $this->info("✅ Exported to: {$output}");

        return self::SUCCESS;
    }

    protected function ensureDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, recursive: true);
        }
    }
}
