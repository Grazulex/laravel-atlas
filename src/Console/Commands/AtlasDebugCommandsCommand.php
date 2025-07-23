<?php

declare(strict_types=1);

namespace LaravelAtlas\Console\Commands;

use Illuminate\Console\Command;
use LaravelAtlas\Mappers\CommandMapper;
use LaravelAtlas\Support\JsonPrinter;

class AtlasDebugCommandsCommand extends Command
{
    protected $signature = 'atlas:debug-commands 
                            {--path= : Restrict to a custom commands path}
                            {--no-recursive : Do not scan recursively}';

    protected $description = 'Scan and debug command mapping from the app using CommandMapper';

    public function handle(): int
    {
        $path = $this->option('path');
        $recursive = ! $this->option('no-recursive');

        $mapper = new CommandMapper;

        $result = $mapper->scan([
            'paths' => $path ? [base_path($path)] : [app_path('Console/Commands')],
            'recursive' => $recursive,
        ]);

        $this->info("Found {$result['count']} command(s).");

        $this->line(JsonPrinter::pretty($result));

        return self::SUCCESS;
    }
}
