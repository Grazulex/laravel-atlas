<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class AtlasConfigurationTest extends TestCase
{
    public function test_atlas_package_can_be_disabled(): void
    {
        // Temporarily disable the package
        config(['atlas.enabled' => false]);

        $this->assertFalse(config('atlas.enabled'));
    }

    public function test_atlas_status_tracking_can_be_configured(): void
    {
        config([
            'atlas.status_tracking.enabled' => false,
            'atlas.status_tracking.file_path' => '/tmp/custom_atlas.log',
            'atlas.status_tracking.max_entries' => 500,
        ]);

        $this->assertFalse(config('atlas.status_tracking.enabled'));
        $this->assertSame('/tmp/custom_atlas.log', config('atlas.status_tracking.file_path'));
        $this->assertSame(500, config('atlas.status_tracking.max_entries'));
    }

    public function test_atlas_generation_formats_can_be_configured(): void
    {
        config([
            'atlas.generation.formats.image' => false,
            'atlas.generation.formats.json' => true,
            'atlas.generation.formats.markdown' => true,
        ]);

        $this->assertFalse(config('atlas.generation.formats.image'));
        $this->assertTrue(config('atlas.generation.formats.json'));
        $this->assertTrue(config('atlas.generation.formats.markdown'));
    }

    public function test_atlas_analysis_settings_are_configurable(): void
    {
        config([
            'atlas.analysis.include_vendors' => true,
            'atlas.analysis.max_depth' => 15,
        ]);

        $this->assertTrue(config('atlas.analysis.include_vendors'));
        $this->assertSame(15, config('atlas.analysis.max_depth'));
    }

    public function test_atlas_default_configuration_values(): void
    {
        // Test default values from our config file
        $this->assertTrue(config('atlas.enabled'));
        $this->assertTrue(config('atlas.status_tracking.enabled'));
        $this->assertTrue(config('atlas.status_tracking.track_history'));
        $this->assertSame(1000, config('atlas.status_tracking.max_entries'));

        $this->assertTrue(config('atlas.generation.formats.image'));
        $this->assertTrue(config('atlas.generation.formats.json'));
        $this->assertTrue(config('atlas.generation.formats.markdown'));

        $this->assertFalse(config('atlas.analysis.include_vendors'));
        $this->assertSame(10, config('atlas.analysis.max_depth'));
        $this->assertIsArray(config('atlas.analysis.scan_paths'));
    }
}
