<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use ReflectionNamedType;
use SplFileInfo;

class NotificationMapper extends BaseMapper
{
    public function getType(): string
    {
        return 'notifications';
    }

    protected function getDefaultOptions(): array
    {
        return [
            'include_channels' => true,
            'include_queue_config' => true,
            'include_data_structure' => true,
            'include_dependencies' => true,
            'scan_path' => app_path('Notifications'),
        ];
    }

    public function performScan(): Collection
    {
        $notifications = collect();
        $scanPath = $this->config('scan_path', app_path('Notifications'));

        if (! File::exists($scanPath)) {
            return $notifications;
        }

        $files = File::allFiles($scanPath);

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $content = File::get($file->getRealPath());
            $className = $this->extractClassName($content);

            if (! $className) {
                continue;
            }

            // Check if it's a notification class
            if (! str_contains($content, 'use Illuminate\Notifications\Notification') &&
                ! str_contains($content, 'extends Notification')) {
                continue;
            }

            if (! class_exists($className)) {
                continue;
            }

            $reflectionClass = new ReflectionClass($className);

            $notifications->put($className, [
                'name' => $reflectionClass->getShortName(),
                'full_name' => $reflectionClass->getName(),
                'file' => $file->getRealPath(),
                'namespace' => $reflectionClass->getNamespaceName(),
                'channels' => $this->extractChannels($content),
                'queue_config' => $this->extractQueueConfig($content),
                'data_structure' => $this->extractDataStructure($content),
                'dependencies' => $this->extractDependencies($reflectionClass),
                'line_count' => substr_count($content, "\n") + 1,
            ]);
        }

        return $notifications;
    }

    private function extractClassName(string $content): ?string
    {
        if (preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatch) &&
            preg_match('/class\s+(\w+)/', $content, $classMatch)) {
            return $namespaceMatch[1] . '\\' . $classMatch[1];
        }

        return null;
    }

    /**
     * @return array<string>
     */
    private function extractChannels(string $content): array
    {
        $channels = [];

        // Look for via method
        if (preg_match('/function\s+via\s*\([^)]*\)\s*\{([^}]+)\}/', $content, $matches)) {
            $viaBody = $matches[1];

            // Extract channel names from return array
            if (preg_match_all('/[\'"](\w+)[\'"]/', $viaBody, $channelMatches)) {
                $channels = array_unique($channelMatches[1]);
            }
        }

        return $channels;
    }

    /**
     * @return array<string, mixed>
     */
    private function extractQueueConfig(string $content): array
    {
        $config = [
            'is_queued' => false,
            'queue' => null,
            'delay' => null,
        ];

        // Check if implements ShouldQueue
        if (str_contains($content, 'implements') && str_contains($content, 'ShouldQueue')) {
            $config['is_queued'] = true;
        }

        // Look for queue property
        if (preg_match('/public\s+\$queue\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
            $config['queue'] = $matches[1];
        }

        // Look for delay property
        if (preg_match('/public\s+\$delay\s*=\s*(\d+)/', $content, $matches)) {
            $config['delay'] = (int) $matches[1];
        }

        return $config;
    }

    /**
     * @return list<array<string, string>>
     */
    private function extractDataStructure(string $content): array
    {
        $dataStructure = [];

        // Look for constructor parameters
        if (preg_match('/function\s+__construct\s*\(([^)]+)\)/', $content, $matches)) {
            $params = $matches[1];

            if (preg_match_all('/(\w+)\s+\$(\w+)/', $params, $paramMatches, PREG_SET_ORDER)) {
                foreach ($paramMatches as $match) {
                    $dataStructure[] = [
                        'name' => $match[2],
                        'type' => $match[1],
                    ];
                }
            }
        }

        return $dataStructure;
    }

    /**
     * @return array<string>
     */
    private function extractDependencies(ReflectionClass $reflection): array
    {
        $dependencies = [];

        // Check constructor dependencies
        $constructor = $reflection->getConstructor();
        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                $type = $parameter->getType();
                if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
                    $dependencies[] = $type->getName();
                }
            }
        }

        return $dependencies;
    }
}
