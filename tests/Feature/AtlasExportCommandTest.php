<?php

declare(strict_types=1);

use Tests\TestCase;

class AtlasExportCommandTest extends TestCase
{
    public function test_can_export_all_components_with_type_all(): void
    {
        // Ensure public/atlas directory exists in the testbench app
        $atlasDir = $this->app->publicPath('atlas');
        if (! is_dir($atlasDir)) {
            mkdir($atlasDir, 0755, true);
        }

        $this->artisan('atlas:export', [
            '--type' => 'all',
            '--format' => 'html',
        ])
            ->expectsOutput('🔍 Exporting all components as html...')
            ->assertSuccessful();

        // Verify the export file exists
        $exportFile = $this->app->publicPath('atlas/export.html');
        $this->assertFileExists($exportFile);

        // Clean up
        if (file_exists($exportFile)) {
            unlink($exportFile);
        }
    }

    public function test_can_export_all_components_without_type_option(): void
    {
        // Ensure public/atlas directory exists in the testbench app
        $atlasDir = $this->app->publicPath('atlas');
        if (! is_dir($atlasDir)) {
            mkdir($atlasDir, 0755, true);
        }

        $this->artisan('atlas:export', [
            '--format' => 'html',
        ])
            ->expectsOutput('🔍 Exporting all components as html...')
            ->assertSuccessful();

        // Verify the export file exists
        $exportFile = $this->app->publicPath('atlas/export.html');
        $this->assertFileExists($exportFile);

        // Clean up
        if (file_exists($exportFile)) {
            unlink($exportFile);
        }
    }

    public function test_can_export_a_specific_component_type(): void
    {
        // Ensure public/atlas directory exists in the testbench app
        $atlasDir = $this->app->publicPath('atlas');
        if (! is_dir($atlasDir)) {
            mkdir($atlasDir, 0755, true);
        }

        $this->artisan('atlas:export', [
            '--type' => 'models',
            '--format' => 'html',
        ])
            ->expectsOutput('🔍 Exporting models as html...')
            ->assertSuccessful();

        // Verify the export file exists
        $exportFile = $this->app->publicPath('atlas/models.html');
        $this->assertFileExists($exportFile);

        // Clean up
        if (file_exists($exportFile)) {
            unlink($exportFile);
        }
    }

    public function test_supports_different_export_formats(): void
    {
        // Ensure public/atlas directory exists in the testbench app
        $atlasDir = $this->app->publicPath('atlas');
        if (! is_dir($atlasDir)) {
            mkdir($atlasDir, 0755, true);
        }

        // Test JSON export
        $this->artisan('atlas:export', [
            '--type' => 'all',
            '--format' => 'json',
        ])
            ->expectsOutput('🔍 Exporting all components as json...')
            ->assertSuccessful();

        $jsonFile = $this->app->publicPath('atlas/export.json');
        $this->assertFileExists($jsonFile);

        // Clean up
        if (file_exists($jsonFile)) {
            unlink($jsonFile);
        }
    }

    public function test_accepts_custom_output_path(): void
    {
        $customOutput = $this->app->storagePath('app/custom-atlas-export.html');

        // Ensure storage/app directory exists
        $storageDir = dirname($customOutput);
        if (! is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $this->artisan('atlas:export', [
            '--type' => 'all',
            '--format' => 'html',
            '--output' => $customOutput,
        ])
            ->assertSuccessful();

        $this->assertFileExists($customOutput);

        // Clean up
        if (file_exists($customOutput)) {
            unlink($customOutput);
        }
    }
}
