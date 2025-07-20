<?php

namespace Grazulex\LaravelAtlas\Console\Commands;

use Illuminate\Console\Command;

class AtlasGenerateCommand extends Command
{
    protected $signature = 'atlas:generate {--format=mermaid,json,markdown}';

    protected $description = 'Generate an architecture map of your Laravel app';

    public function handle(): int
    {
        $this->info('Generating Laravel Atlas map...');
        // Stubbed behavior
        $this->info('✔ Map generated!');

        return self::SUCCESS;
    }
}
