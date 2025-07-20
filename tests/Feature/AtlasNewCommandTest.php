<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class AtlasCommandNewTest extends TestCase
{
    public function test_atlas_generate_runs_successfully(): void
    {
        $this->artisan('atlas:generate')
            ->expectsOutput('🗺️  Generating Laravel Atlas map...')
            ->expectsOutput('✔ Map generated successfully!')
            ->assertExitCode(0);
    }

    public function test_atlas_generate_with_models_type(): void
    {
        $this->artisan('atlas:generate --type=models')
            ->expectsOutput('🗺️  Generating Laravel Atlas map...')
            ->expectsOutput('📊 Mapping models...')
            ->expectsOutput('✔ Map generated successfully!')
            ->assertExitCode(0);
    }

    public function test_atlas_generate_with_invalid_type(): void
    {
        $this->artisan('atlas:generate --type=invalid')
            ->expectsOutput('Invalid type: invalid. Available types: models, routes, jobs, services, controllers, events, commands, middleware, all')
            ->assertExitCode(1);
    }
}
