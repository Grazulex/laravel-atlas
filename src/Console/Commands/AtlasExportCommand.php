<?php

declare(strict_types=1);

namespace LaravelAtlas\Console\Commands;

use Illuminate\Console\Command;
use LaravelAtlas\Exporters\AtlasExportManager;
use Throwable;

class AtlasExportCommand extends Command
{
    protected $signature = 'atlas:export {--type= : Filter to a specific component type (models, routes, etc.) or "all" for all components} {--format=html : Export format (html, json, pdf, blade, etc.)} {--output= : Output file path}';

    protected $description = 'Export all components or filter to a specific type to a chosen format (HTML, JSON, PDF, etc.)';

    public function handle(): int
    {
        $type = $this->option('type');
        $format = $this->option('format') ?? 'html';

        $output = $this->option('output') ?? ($format === 'blade' ? resource_path('views/atlas/export.blade.php') : public_path("atlas/export.{$format}"));

        if ($type && $type !== 'all') {
            $this->info("🔍 Exporting {$type} as {$format}...");
            $output = $this->option('output') ?? ($format === 'blade' ? resource_path("views/atlas/{$type}.blade.php") : public_path("atlas/{$type}.{$format}"));
        } else {
            $this->info("🔍 Exporting all components as {$format}...");
        }

        try {
            if ($type && $type !== 'all') {
                // Export d'un type spécifique
                $content = AtlasExportManager::export($type, $format);
            } else {
                // Export de tous les composants (type=null ou type=all)
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
