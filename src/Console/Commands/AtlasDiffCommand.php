<?php

declare(strict_types=1);

namespace LaravelAtlas\Console\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;
use LaravelAtlas\Support\ArchitectureDiff;
use Symfony\Component\Console\Output\OutputInterface;

class AtlasDiffCommand extends Command
{
    protected $signature = 'atlas:diff {old : JSON export of the base architecture} {new : JSON export of the architecture to compare} {--output= : Write the Markdown report to this file instead of printing it} {--threshold= : Fail when the number of changes is greater than this value}';

    protected $description = 'Compare two JSON exports and list added, removed and changed models, routes, jobs and events as Markdown';

    public function handle(): int
    {
        $old = $this->argument('old');
        $new = $this->argument('new');
        $threshold = $this->option('threshold');
        $output = $this->option('output');

        if (! is_string($old) || ! is_string($new)) {
            $this->error('❌ Both scan files must be given as paths.');

            return self::FAILURE;
        }

        if ($threshold !== null && (! is_string($threshold) || ! ctype_digit($threshold))) {
            $this->error('❌ The --threshold option must be a non-negative integer.');

            return self::FAILURE;
        }

        try {
            $diff = ArchitectureDiff::fromFiles($old, $new);
        } catch (InvalidArgumentException $e) {
            $this->error('❌ Diff failed: ' . $e->getMessage());

            return self::FAILURE;
        }

        if ($diff->types() === []) {
            $this->error('❌ Diff failed: the two scans share no comparable component type (' . implode(', ', ArchitectureDiff::TYPES) . ').');

            return self::FAILURE;
        }

        $markdown = $diff->toMarkdown();

        if (is_string($output) && $output !== '') {
            $this->ensureDirectory(dirname($output));
            file_put_contents($output, $markdown);
            $this->info("✅ Diff written to: {$output}");
        } else {
            // Raw output: route URIs and class names must not be read as console tags.
            $this->output->writeln(rtrim($markdown), OutputInterface::OUTPUT_RAW);
        }

        if ($threshold !== null && $diff->totalChanges() > (int) $threshold) {
            $this->error("❌ {$diff->totalChanges()} architecture change(s) detected, above the threshold of {$threshold}.");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    protected function ensureDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            mkdir($dir, recursive: true);
        }
    }
}
