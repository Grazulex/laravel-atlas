<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;
use LaravelAtlas\Support\Utils;

class CommandMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'commands';
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function scan(array $options = []): array
    {
        $commands = [];
        $paths = $options['paths'] ?? [app_path('Console/Commands')];
        $recursive = $options['recursive'] ?? true;

        $seen = [];

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $files = $recursive ? File::allFiles($path) : File::files($path);

            foreach ($files as $file) {
                $fqcn = ClassResolver::resolveFromPath($file->getRealPath());

                if (
                    $fqcn &&
                    class_exists($fqcn) &&
                    is_subclass_of($fqcn, Command::class) &&
                    !isset($seen[$fqcn])
                ) {
                    $seen[$fqcn] = true;
                    $instance = app($fqcn);
                    $commands[] = $this->analyzeCommand($instance);
                }
            }
        }

        return [
            'type' => $this->type(),
            'count' => count($commands),
            'data' => $commands,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeCommand(Command $command): array
    {
        $class = $command::class;
        $reflection = new ReflectionClass($command);
        $file = $reflection->getFileName();
        
        $source = null;
        if ($file && file_exists($file)) {
            $content = file_get_contents($file);
            $source = $content !== false ? $content : null;
        }

        // Get signature from property instead of method
        $signature = $this->getCommandSignature($command);
        $description = $this->getCommandDescription($command);

        return [
            'class' => $class,
            'signature' => $signature,
            'parsed_signature' => $this->parseSignature($signature),
            'description' => $description,
            'aliases' => $command->getAliases(),
            'flow' => $this->analyzeFlow($source),
        ];
    }

    /**
     * @return array<string, array<int|string, mixed>>
     */
    protected function analyzeFlow(?string $source): array
    {
        $flow = [
            'jobs' => [],
            'events' => [],
            'calls' => [],
            'dependencies' => [],
        ];

        if (!$source) {
            return $flow;
        }

        // detect jobs
        if (preg_match_all('/dispatch(?:Now)?\(\s*([A-Z][\w\\\\]+)::class/', $source, $matches)) {
            foreach ($matches[1] as $fqcn) {
                $flow['jobs'][] = [
                    'class' => $fqcn,
                    'async' => !str_contains($source, "dispatchNow({$fqcn}"),
                ];
            }
        }

        // detect events
        if (preg_match_all('/event\(\s*([A-Z][\w\\\\]+)::class/', $source, $matches)) {
            foreach ($matches[1] as $fqcn) {
                $flow['events'][] = ['class' => $fqcn];
            }
        }

        // detect other artisan calls
        if (preg_match_all('/\$this->call\(\s*[\'"]([\w:-]+)[\'"]/', $source, $matches)) {
            foreach ($matches[1] as $signature) {
                $flow['calls'][] = $signature;
            }
        }

        // detect dependencies (via new X or X::)
        if (preg_match_all('/new\s+([A-Z][\w\\\\]+)|([A-Z][\w\\\\]+)::/', $source, $matches)) {
            $found = array_filter(array_merge($matches[1], $matches[2]));
            $flow['dependencies'] = array_values(array_unique(array_filter($found)));
        }

        return $flow;
    }

    /**
     * Get command signature safely
     */
    protected function getCommandSignature(Command $command): string
    {
        // Access signature property via reflection
        $reflection = new ReflectionClass($command);
        if ($reflection->hasProperty('signature')) {
            $property = $reflection->getProperty('signature');
            $property->setAccessible(true);
            $signature = $property->getValue($command);
            return is_string($signature) ? $signature : '';
        }

        return '';
    }

    /**
     * Get command description safely
     */
    protected function getCommandDescription(Command $command): string
    {
        // Access description property via reflection
        $reflection = new ReflectionClass($command);
        if ($reflection->hasProperty('description')) {
            $property = $reflection->getProperty('description');
            $property->setAccessible(true);
            $description = $property->getValue($command);
            return is_string($description) ? $description : '';
        }

        return '';
    }

    protected function parseSignature(string $signature): array
{
    $parts = [];

    if (preg_match_all('/{(--)?([\w\-\:]+)([=*]?)?(?:\s*:\s*([^}]+))?}/', $signature, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $isOption = $match[1] === '--';
            $name = $match[2];
            $modifier = $match[3] ?? '';
            $description = $match[4] ?? null;

            $parts[] = [
                'type' => $isOption ? 'option' : 'argument',
                'name' => $name,
                'modifier' => $modifier,
                'description' => $description,
            ];
        }
    }

    return $parts;
}

}
