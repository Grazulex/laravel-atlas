<?php

declare(strict_types=1);

use Tests\TestCase;

class AtlasExportBugFixTest extends TestCase
{
    public function test_type_all_parameter_does_not_cause_mapper_error(): void
    {
        // This test specifically verifies the fix for the bug:
        // "No mapper registered for type [all]" when using --type=all

        // Ensure public/atlas directory exists in the testbench app
        $atlasDir = $this->app->publicPath('atlas');
        if (! is_dir($atlasDir)) {
            mkdir($atlasDir, 0755, true);
        }

        // This command should NOT fail with "No mapper registered for type [all]"
        // It should successfully route to exportAll() instead of trying to scan for 'all'
        $this->artisan('atlas:export', [
            '--type' => 'all',
            '--format' => 'html',
        ])
            ->expectsOutput('🔍 Exporting all components as html...')
            ->assertSuccessful();

        // Verify the export file exists (confirming exportAll() was called)
        $exportFile = $this->app->publicPath('atlas/export.html');
        $this->assertFileExists($exportFile);

        // Verify the file is not empty (confirming actual export occurred)
        $this->assertGreaterThan(0, filesize($exportFile));

        // Clean up
        if (file_exists($exportFile)) {
            unlink($exportFile);
        }
    }

    public function test_type_all_and_no_type_produce_same_result(): void
    {
        // Ensure public/atlas directory exists in the testbench app
        $atlasDir = $this->app->publicPath('atlas');
        if (! is_dir($atlasDir)) {
            mkdir($atlasDir, 0755, true);
        }

        // Export with --type=all
        $this->artisan('atlas:export', [
            '--type' => 'all',
            '--format' => 'html',
            '--output' => $this->app->publicPath('atlas/export-with-all.html'),
        ])->assertSuccessful();

        // Export without --type (should default to all)
        $this->artisan('atlas:export', [
            '--format' => 'html',
            '--output' => $this->app->publicPath('atlas/export-without-type.html'),
        ])->assertSuccessful();

        $fileWithAll = $this->app->publicPath('atlas/export-with-all.html');
        $fileWithoutType = $this->app->publicPath('atlas/export-without-type.html');

        $this->assertFileExists($fileWithAll);
        $this->assertFileExists($fileWithoutType);

        // Both files should have the same content (since both call exportAll())
        $this->assertStringEqualsFile($fileWithAll, file_get_contents($fileWithoutType));

        // Clean up
        if (file_exists($fileWithAll)) {
            unlink($fileWithAll);
        }
        if (file_exists($fileWithoutType)) {
            unlink($fileWithoutType);
        }
    }
}
