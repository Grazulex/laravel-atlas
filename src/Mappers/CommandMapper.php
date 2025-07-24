<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;

class CommandMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'commands';
    }

    public function scan(array $options = []): array
    {
        $commands = [];
        $paths = $options['paths'] ?? [app_path('Console/Commands')];
        $recursive = $options['recursive'] ?? true;

        foreach ($paths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            $files = $recursive ? File::allFiles($path) : File::files($path);

            foreach ($files as $file) {
                $fqcn = ClassResolver::resolveFromPath($file->getRealPath());

                if (
                    $fqcn &&
                    class_exists($fqcn) &&
                    is_subclass_of($fqcn, Command::class)
                ) {
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

        return [
            'class' => $class,
            'namespace' => $reflection->getNamespaceName(),
            'name' => $reflection->getShortName(),
            'file' => $file ?: 'Unknown',
            'signature' => $this->getSignature($command),
            'parsed_signature' => $this->parseSignature($command),
            'description' => $this->getDescription($command),
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
            'notifications' => [],
            'dependencies' => [
                'models' => [],
                'services' => [],
                'notifications' => [],
                'facades' => [],
                'classes' => [],
            ],
        ];

        if (! $source) {
            return $flow;
        }

        // Jobs
        if (preg_match_all('/dispatch(?:Now)?\(\s*([A-Z][\w\\\\]+)::class/', $source, $matches)) {
            foreach ($matches[1] as $fqcn) {
                $flow['jobs'][] = [
                    'class' => $fqcn,
                    'async' => ! str_contains($source, "dispatchNow({$fqcn}"),
                ];
            }
        }

        // Events
        if (preg_match_all('/event\(\s*([A-Z][\w\\\\]+)::class/', $source, $matches)) {
            foreach ($matches[1] as $fqcn) {
                $flow['events'][] = ['class' => $fqcn];
            }
        }

        // Artisan calls
        if (preg_match_all('/\$this->call\(\s*[\'"]([\w:-]+)[\'"]/', $source, $matches)) {
            foreach ($matches[1] as $signature) {
                $flow['calls'][] = $signature;
            }
        }

        // Notifications
        if (preg_match_all('/->notify\(\s*new\s+([A-Z][\w\\\\]+)/', $source, $matches)) {
            foreach ($matches[1] as $fqcn) {
                $flow['notifications'][] = ['class' => $fqcn];
                $flow['dependencies']['notifications'][] = $fqcn;
            }
        }

        // Class usage (new or static calls)
        if (preg_match_all('/new\s+([A-Z][\w\\\\]+)|([A-Z][\w\\\\]+)::/', $source, $matches)) {
            $found = array_filter(array_merge($matches[1], $matches[2]));
            $classes = array_values(array_unique(array_filter($found)));

            foreach ($classes as $fqcn) {
                $basename = class_basename($fqcn);
                if (str_contains($fqcn, 'App\\Models\\')) {
                    $flow['dependencies']['models'][] = $fqcn;
                } elseif (str_contains($fqcn, 'App\\Services\\')) {
                    $flow['dependencies']['services'][] = $fqcn;
                } elseif (str_contains($fqcn, 'Notification')) {
                    $flow['dependencies']['notifications'][] = $fqcn;
                } elseif (in_array($basename, ['Log', 'Queue', 'Bus', 'Mail', 'DB', 'Cache', 'Event', 'Artisan'])) {
                    $flow['dependencies']['facades'][] = $basename;
                } else {
                    $flow['dependencies']['classes'][] = $fqcn;
                }
            }
        }

        // Remove duplicates
        foreach ($flow['dependencies'] as &$dep) {
            $dep = array_values(array_unique($dep));
        }

        return $flow;
    }

    protected function getSignature(Command $command): string
    {
        $reflection = new ReflectionClass($command);
        if ($reflection->hasProperty('signature')) {
            $property = $reflection->getProperty('signature');
            $property->setAccessible(true);

            return (string) $property->getValue($command);
        }

        return '';
    }

    protected function getDescription(Command $command): string
    {
        $reflection = new ReflectionClass($command);
        if ($reflection->hasProperty('description')) {
            $property = $reflection->getProperty('description');
            $property->setAccessible(true);

            return (string) $property->getValue($command);
        }

        return '';
    }

    /**
     * @return array<int, array<string, string>>
     */
    protected function parseSignature(Command $command): array
    {
        $signature = $this->getSignature($command);
        $parts = [];

        if (preg_match_all('/{(--)?([\w\-\:]+)([=*]?)?(?:\s*:\s*([^}]+))?}/', $signature, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $isOption = $match[1] === '--';
                $parts[] = [
                    'type' => $isOption ? 'option' : 'argument',
                    'name' => $match[2],
                    'modifier' => $match[3] ?? '',
                    'description' => $match[4] ?? '',
                ];
            }
        }

        return $parts;
    }
}
