<?php

declare(strict_types=1);

namespace Tests\Feature\Expoters\Blade;

use Tests\TestCase;

class BladeLayoutExporterTest extends TestCase
{
    protected function cleanUpExportFile(string $filePath): void
    {
        if (file_exists($filePath)) {
            unlink($filePath);

            $directory = dirname($filePath);

            if (is_dir($directory) && count(scandir($directory)) === 2) {
                rmdir($directory);
            }
        }
    }

    public function test_can_export_all_components_with_type_all_in_blade_format(): void
    {
        $this->artisan('atlas:export', [
            '--type' => 'all',
            '--format' => 'blade',
        ])
            ->expectsOutput('🔍 Exporting all components as blade...')
            ->assertSuccessful();

        $exportFile = $this->app->resourcePath('views/atlas/export.blade.php');

        $this->assertFileExists($exportFile);
        $this->assertGreaterThan(0, filesize($exportFile), 'The exported blade file is empty.');
        $this->assertStringContainsString('<div>', file_get_contents($exportFile));

        $this->cleanUpExportFile($exportFile);
    }

    public function test_can_export_specific_components_in_blade_format(): void
    {
        $this->artisan('atlas:export', [
            '--type' => 'events',
            '--format' => 'blade',
        ])
            ->expectsOutput('🔍 Exporting events as blade...')
            ->assertSuccessful();

        $exportFile = $this->app->resourcePath('views/atlas/events.blade.php');

        $this->assertFileExists($exportFile);
        $this->assertGreaterThan(0, filesize($exportFile), 'The exported blade file is empty.');
        $this->assertStringContainsString('<div>', file_get_contents($exportFile));

        $this->cleanUpExportFile($exportFile);
    }

    public function test_accepts_custom_output_path(): void
    {
        $this->artisan('atlas:export', [
            '--type' => 'all',
            '--format' => 'blade',
            '--output' => 'resources/views/docs/docs.blade.php',
        ])
            ->expectsOutput('🔍 Exporting all components as blade...')
            ->assertSuccessful();

        $exportFile = $this->app->resourcePath('views/docs/docs.blade.php');

        $this->assertFileExists($exportFile);
        $this->assertGreaterThan(0, filesize($exportFile), 'The exported blade file is empty.');
        $this->assertStringContainsString('<div>', file_get_contents($exportFile));

        $this->cleanUpExportFile($exportFile);
    }
}
