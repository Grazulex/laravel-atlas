<?php

declare(strict_types=1);

/**
 * This test demonstrates that the original bug is fixed:
 * "No mapper registered for type [all]" when running:
 * php artisan atlas:export --type=all --format=html
 */
it('original bug is fixed', function (): void {
    $atlasDir = $this->app->publicPath('atlas');
    if (! is_dir($atlasDir)) {
        mkdir($atlasDir, 0755, true);
    }

    // This is the exact command that was failing before the fix
    $this->artisan('atlas:export', ['--type' => 'all', '--format' => 'html'])
        ->expectsOutput('🔍 Exporting all components as html...')
        ->expectsOutputToContain('✅ Exported to:')
        ->assertSuccessful();

    // Clean up
    $exportFile = $this->app->publicPath('atlas/export.html');
    if (file_exists($exportFile)) {
        unlink($exportFile);
    }
});
