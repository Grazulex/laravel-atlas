<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\File;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;

class NotificationMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'notifications';
    }

    public function scan(array $options = []): array
    {
        $notifications = [];
        $paths = $options['paths'] ?? [app_path('Notifications')];
        $recursive = $options['recursive'] ?? true;

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
                    is_subclass_of($fqcn, Notification::class)
                ) {
                    $notifications[] = $this->analyzeNotification($fqcn);
                }
            }
        }

        return [
            'type' => $this->type(),
            'count' => count($notifications),
            'data' => $notifications,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeNotification(string $fqcn): array
    {
        $reflection = new ReflectionClass($fqcn);
        $file = $reflection->getFileName();
        $source = ($file && file_exists($file)) ? file_get_contents($file) : null;

        return [
            'class' => $fqcn,
            'channels' => $this->detectChannels($source),
            'methods' => $this->detectDefinedMethods($source),
            'flow' => $this->analyzeFlow($source),
        ];
    }

    /**
     * @return array<string>
     */
    protected function detectChannels(?string $source): array
    {
        if (! $source) {
            return [];
        }

        if (preg_match('/function\s+via\s*\(.*?\)\s*:\s*array\s*\{(.+?)\}/s', $source, $match)) {
            preg_match_all('/[\'"](\w+)[\'"]/', $match[1], $channels);
            return array_unique($channels[1]);
        }

        return [];
    }

    /**
     * @return array<string>
     */
    protected function detectDefinedMethods(?string $source): array
    {
        if (! $source) {
            return [];
        }

        preg_match_all('/function\s+(to[A-Z][a-zA-Z]*)\s*\(/', $source, $matches);
        return array_unique($matches[1]);
    }

    /**
     * @return array<string, mixed>
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

        // Notifications used
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

        foreach ($flow['dependencies'] as &$deps) {
            $deps = array_values(array_unique($deps));
        }

        return $flow;
    }
}
