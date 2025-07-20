<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Testing\PendingCommand;
use Illuminate\Contracts\Console\Kernel;
use Grazulex\LaravelAtlas\Console\Commands\AtlasGenerateCommand;
use Tests\TestCase;

class AtlasCommandTest extends TestCase
{
    public function test_atlas_generate_command_exists(): void
    {
        $result = $this->artisan('list');
        $this->assertInstanceOf(PendingCommand::class, $result);
        $result->assertExitCode(0);

        // Vérifier que la commande existe dans la liste
        $app = $this->app;
        $this->assertNotNull($app);
        $commands = collect($app->make(Kernel::class)->all());
        $this->assertTrue($commands->has('atlas:generate'));
    }

    public function test_atlas_generate_command_runs_successfully(): void
    {
        $result = $this->artisan('atlas:generate');
        $this->assertInstanceOf(PendingCommand::class, $result);
        $result->expectsOutput('Generating Laravel Atlas map...')
            ->expectsOutput('✔ Map generated!')
            ->assertExitCode(0);
    }

    public function test_atlas_generate_command_with_format_option(): void
    {
        $result = $this->artisan('atlas:generate --format=mermaid');
        $this->assertInstanceOf(PendingCommand::class, $result);
        $result->expectsOutput('Generating Laravel Atlas map...')
            ->expectsOutput('✔ Map generated!')
            ->assertExitCode(0);
    }

    public function test_atlas_generate_command_with_multiple_formats(): void
    {
        $result = $this->artisan('atlas:generate --format=mermaid,json,markdown');
        $this->assertInstanceOf(PendingCommand::class, $result);
        $result->expectsOutput('Generating Laravel Atlas map...')
            ->expectsOutput('✔ Map generated!')
            ->assertExitCode(0);
    }

    public function test_atlas_configuration_is_loaded_in_feature_context(): void
    {
        $this->assertTrue(config('atlas.enabled'));
        $this->assertIsArray(config('atlas.status_tracking'));
        $this->assertIsArray(config('atlas.generation'));
        $this->assertIsArray(config('atlas.analysis'));
    }

    public function test_atlas_service_provider_registers_commands(): void
    {
        $app = $this->app;
        $this->assertNotNull($app);

        $kernel = $app->make(Kernel::class);
        $commands = $kernel->all();

        $this->assertArrayHasKey('atlas:generate', $commands);
        $this->assertInstanceOf(
            AtlasGenerateCommand::class,
            $commands['atlas:generate']
        );
    }
}
