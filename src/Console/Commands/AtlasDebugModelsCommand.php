<?php

declare(strict_types=1);

namespace LaravelAtlas\Console\Commands;

use Illuminate\Console\Command;
use LaravelAtlas\Mappers\ModelMapper;
use LaravelAtlas\Support\JsonPrinter;

class AtlasDebugModelsCommand extends Command
{
    protected $signature = 'atlas:debug-models 
                            {--path= : Restrict to a custom model path}
                            {--no-recursive : Do not scan recursively}';

    protected $description = 'Scan and debug model mapping from the app using ModelMapper';

    public function handle(): int
    {
        $path = $this->option('path');
        $recursive = ! $this->option('no-recursive');

        $mapper = new ModelMapper;

        $result = $mapper->scan([
            'paths' => $path ? [base_path($path)] : [app_path('Models'), app_path()],
            'recursive' => $recursive,
        ]);

        $this->info("Found {$result['count']} model(s).");

        $this->line(JsonPrinter::pretty($result));

        return self::SUCCESS;
    }
}
