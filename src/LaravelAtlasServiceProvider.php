<?php

namespace LaravelAtlas;

use Illuminate\Support\ServiceProvider;
use LaravelAtlas\Console\Commands\AtlasDebugModelsCommand;
use Override;

class LaravelAtlasServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Publish config file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/Config/atlas.php' => config_path('atlas.php'),
            ], 'atlas-config');

            $this->commands([
                AtlasDebugModelsCommand::class,
            ]);
        }
    }

    #[Override]
    public function register(): void
    {
        // Merge config file
        $this->mergeConfigFrom(
            __DIR__ . '/Config/atlas.php', 'atlas'
        );
    }
}
