<?php

namespace LaravelAtlas;

use Illuminate\Support\ServiceProvider;
use LaravelAtlas\Console\Commands\AtlasDebugModelsCommand;
use LaravelAtlas\Console\Commands\AtlasExportCommand;
use Override;

class LaravelAtlasServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'atlas');

        $this->publishes([
            __DIR__ . '/Config/atlas.php' => config_path('atlas.php'),
        ], 'atlas-config');

        $this->commands([
            AtlasDebugModelsCommand::class,
            AtlasExportCommand::class,
        ]);
    }

    #[Override]
    public function register(): void
    {
        // Merge config file
        $this->mergeConfigFrom(
            __DIR__ . '/Config/atlas.php', 'atlas'
        );

        $this->app->singleton('atlas', fn (): AtlasManager => new AtlasManager);
    }
}
