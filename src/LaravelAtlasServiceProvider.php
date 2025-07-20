<?php

namespace Grazulex\LaravelAtlas;

use Grazulex\LaravelAtlas\Console\Commands\AtlasGenerateCommand;
use Illuminate\Support\ServiceProvider;
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
                AtlasGenerateCommand::class,
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
