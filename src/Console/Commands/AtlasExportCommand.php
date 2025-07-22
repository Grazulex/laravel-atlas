<?php

declare(strict_types=1);

namespace LaravelAtlas\Console\Commands;

use Throwable;
use Illuminate\Console\Command;
use LaravelAtlas\Exporters\AtlasExportManager;

class AtlasExportCommand extends Command
{
    protected $signature = 'atlas:export {--type= : Filter to a specific component type (models, routes, etc.)} {--format=html : Export format (html, json, markdown, etc.)} {--output= : Output file path}';

    protected $description = 'Export all components or filter to a specific type to a chosen format (HTML, JSON, Markdown, etc.)';

    public function handle(): int
    {
        $type = $this->option('type');
        $format = $this->option('format') ?? 'html';

        $output = $this->option('output') ?? public_path("atlas/export.{$format}");

        if ($type) {
            $this->info("🔍 Exporting {$type} as {$format}...");
            $output = $this->option('output') ?? public_path("atlas/{$type}.{$format}");
        } else {
            $this->info("🔍 Exporting all components as {$format}...");
        }

        try {
            if ($type) {
                // Export d'un type spécifique
                $content = AtlasExportManager::export($type, $format);
            } else {
                // Export de tous les composants
                $content = AtlasExportManager::exportAll($format);
            }
        } catch (Throwable $e) {
            $this->error('❌ Export failed: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->ensureDirectory(dirname($output));
        file_put_contents($output, $content);

        $this->info("✅ Exported to: {$output}");

        return self::SUCCESS;
    }

    protected function ensureDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            mkdir($dir, recursive: true);
        }
    }
}
