<?php

declare(strict_types=1);

namespace Tests\Unit;

use Grazulex\LaravelAtlas\LaravelAtlasServiceProvider;
use Orchestra\Testbench\TestCase;

class BasicTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelAtlasServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup the application environment for testing
        $app['config']->set('atlas.enabled', true);
    }

    public function test_service_provider_is_loaded(): void
    {
        $this->assertNotNull($this->app);
        // Test that the service provider is registered instead of bound
        $providers = $this->app->getLoadedProviders();
        $this->assertArrayHasKey(LaravelAtlasServiceProvider::class, $providers);
    }

    public function test_config_is_published(): void
    {
        $this->assertTrue(config('atlas.status_tracking.enabled', true));
    }

    public function test_package_configuration_is_available(): void
    {
        $this->assertNotNull($this->app);
        // Test that config is properly merged
        $this->assertIsArray(config('atlas'));
        $this->assertArrayHasKey('status_tracking', config('atlas'));

        // Test status tracking config structure
        $statusTracking = config('atlas.status_tracking');
        $this->assertIsArray($statusTracking);
        $this->assertArrayHasKey('enabled', $statusTracking);
        $this->assertArrayHasKey('file_path', $statusTracking);
        $this->assertArrayHasKey('track_history', $statusTracking);
        $this->assertArrayHasKey('max_entries', $statusTracking);
    }
}
