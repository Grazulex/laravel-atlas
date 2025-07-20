<?php

declare(strict_types=1);

namespace Grazulex\LaravelAtlas\Mappers;

use Exception;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Override;
use ReflectionClass;
use ReflectionNamedType;
use SplFileInfo;

class EventMapper extends BaseMapper
{
    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'events';
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    protected function getDefaultOptions(): array
    {
        return [
            'include_listeners' => true,
            'include_broadcasting' => true,
            'include_properties' => true,
            'scan_paths' => [
                app_path('Events'),
                app_path('Domain/Events'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return Collection<string, array<string, mixed>>
     */
    protected function performScan(): Collection
    {
        $results = collect();
        $scanPaths = $this->config('scan_paths', [app_path('Events')]);

        foreach ($scanPaths as $scanPath) {
            if (! is_string($scanPath) || ! File::isDirectory($scanPath)) {
                continue;
            }

            $eventFiles = File::allFiles($scanPath);

            foreach ($eventFiles as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $eventData = $this->analyzeEventFile($file);
                if ($eventData !== null) {
                    $results->put($eventData['class_name'], $eventData);
                }
            }
        }

        /** @var Collection<string, array<string, mixed>> $results */
        return $results;
    }

    /**
     * Analyze a single event file
     *
     * @return array<string, mixed>|null
     */
    protected function analyzeEventFile(SplFileInfo $file): ?array
    {
        $content = File::get($file->getRealPath());
        $className = $this->extractClassName($content, $file);

        if (! $className || ! class_exists($className)) {
            return null;
        }

        try {
            $reflection = new ReflectionClass($className);

            // Check if it looks like an event
            if (! $this->looksLikeEvent($reflection)) {
                return null;
            }

            $eventData = [
                'class_name' => $className,
                'file_path' => $file->getRealPath(),
                'namespace' => $reflection->getNamespaceName(),
                'short_name' => $reflection->getShortName(),
                'is_abstract' => $reflection->isAbstract(),
                'parent_class' => $reflection->getParentClass()?->getName(),
                'traits' => array_keys($reflection->getTraits()),
                'interfaces' => $reflection->getInterfaceNames(),
                'should_queue' => $reflection->implementsInterface(ShouldQueue::class),
                'should_broadcast' => $reflection->implementsInterface(ShouldBroadcast::class),
            ];

            // Add broadcasting info if enabled
            if ($this->config('include_broadcasting')) {
                $eventData['broadcasting_info'] = $this->extractBroadcastingInfo($reflection, $content);
            }

            // Add properties if enabled
            if ($this->config('include_properties')) {
                $eventData['properties'] = $this->extractProperties($reflection);
            }

            // Add listeners if enabled (basic detection)
            if ($this->config('include_listeners')) {
                $eventData['potential_listeners'] = $this->findPotentialListeners($className);
            }

            return $eventData;
        } catch (Exception) {
            // Skip events that can't be analyzed
            return null;
        }
    }

    /**
     * Check if a class looks like an event
     */
    protected function looksLikeEvent(ReflectionClass $reflection): bool
    {
        $className = $reflection->getShortName();
        $namespace = $reflection->getNamespaceName();

        // Check naming patterns
        if (str_ends_with($className, 'Event') ||
            str_ends_with($className, 'Happened') ||
            str_ends_with($className, 'Occurred') ||
            str_starts_with($className, 'When') ||
            str_contains($className, 'Created') ||
            str_contains($className, 'Updated') ||
            str_contains($className, 'Deleted')) {
            return true;
        }

        // Check namespace patterns
        if (str_contains($namespace, 'Events')) {
            return true;
        }

        // Check for broadcasting or queueing interfaces
        if ($reflection->implementsInterface(ShouldBroadcast::class) ||
            $reflection->implementsInterface(ShouldQueue::class)) {
            return true;
        }

        return false;
    }

    /**
     * Extract class name from file content
     */
    protected function extractClassName(string $content, SplFileInfo $file): ?string
    {
        // Extract namespace
        preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches);
        $namespace = $namespaceMatches[1] ?? '';

        // Extract class name
        preg_match('/class\s+(\w+)/', $content, $classMatches);
        $className = $classMatches[1] ?? '';

        if ($className === '' || $className === '0') {
            return null;
        }

        return $namespace !== '' && $namespace !== '0' ? $namespace . '\\' . $className : $className;
    }

    /**
     * Extract broadcasting information
     *
     * @return array<string, mixed>
     */
    protected function extractBroadcastingInfo(ReflectionClass $reflection, string $content): array
    {
        $broadcastingInfo = [
            'should_broadcast' => $reflection->implementsInterface(ShouldBroadcast::class),
            'channels' => [],
            'broadcast_as' => null,
            'broadcast_with' => null,
        ];

        if ($broadcastingInfo['should_broadcast']) {
            // Try to extract channel information from broadcastOn method
            if ($reflection->hasMethod('broadcastOn')) {
                $broadcastingInfo['has_broadcast_on'] = true;
            }

            // Try to extract broadcast name from broadcastAs method
            if ($reflection->hasMethod('broadcastAs')) {
                $broadcastingInfo['has_broadcast_as'] = true;
            }

            // Try to extract broadcast data from broadcastWith method
            if ($reflection->hasMethod('broadcastWith')) {
                $broadcastingInfo['has_broadcast_with'] = true;
            }

            // Try to find channel patterns in content
            if (preg_match_all('/new\s+(Private|Presence)?Channel\([\'"]([^\'"]+)[\'"]/', $content, $matches)) {
                foreach ($matches[2] as $i => $channel) {
                    $broadcastingInfo['channels'][] = [
                        'name' => $channel,
                        'type' => $matches[1][$i] ?: 'public',
                    ];
                }
            }
        }

        return $broadcastingInfo;
    }

    /**
     * Extract event properties
     *
     * @return array<int, array<string, mixed>>
     */
    protected function extractProperties(ReflectionClass $reflection): array
    {
        $properties = [];
        $reflectionProperties = $reflection->getProperties();

        foreach ($reflectionProperties as $property) {
            if ($property->getDeclaringClass()->getName() !== $reflection->getName()) {
                continue;
            }

            $type = $property->getType();
            $typeName = 'mixed';
            if ($type instanceof ReflectionNamedType) {
                $typeName = $type->getName();
            }

            $properties[] = [
                'name' => $property->getName(),
                'type' => $typeName,
                'visibility' => $this->getPropertyVisibility($property),
                'is_static' => $property->isStatic(),
                'has_default' => $property->hasDefaultValue(),
            ];
        }

        return $properties;
    }

    /**
     * Get property visibility
     */
    protected function getPropertyVisibility(\ReflectionProperty $property): string
    {
        if ($property->isPublic()) {
            return 'public';
        }
        if ($property->isProtected()) {
            return 'protected';
        }

        return 'private';
    }

    /**
     * Find potential listeners for this event
     *
     * @return array<string>
     */
    protected function findPotentialListeners(string $eventClassName): array
    {
        $listeners = [];
        $eventName = class_basename($eventClassName);

        // Look for listeners in common locations
        $listenerPaths = [
            app_path('Listeners'),
            app_path('Domain/Listeners'),
        ];

        foreach ($listenerPaths as $path) {
            if (! File::isDirectory($path)) {
                continue;
            }

            $files = File::allFiles($path);
            foreach ($files as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $content = File::get($file->getRealPath());

                // Look for handle method with event type hint
                if (preg_match('/function\s+handle\s*\(\s*([^)]*' . preg_quote($eventName, '/') . '[^)]*)\)/', $content)) {
                    $listenerName = $this->extractClassName($content, $file);
                    if ($listenerName) {
                        $listeners[] = $listenerName;
                    }
                }
            }
        }

        return $listeners;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    #[Override]
    protected function getSummary(): array
    {
        $summary = parent::getSummary();

        $queuedEvents = 0;
        $broadcastEvents = 0;
        $totalProperties = 0;
        $totalListeners = 0;

        foreach ($this->results as $event) {
            if (is_array($event)) {
                if (isset($event['should_queue']) && $event['should_queue']) {
                    $queuedEvents++;
                }
                if (isset($event['should_broadcast']) && $event['should_broadcast']) {
                    $broadcastEvents++;
                }
                if (isset($event['properties'])) {
                    $totalProperties += count($event['properties']);
                }
                if (isset($event['potential_listeners'])) {
                    $totalListeners += count($event['potential_listeners']);
                }
            }
        }

        $summary['queued_events_count'] = $queuedEvents;
        $summary['broadcast_events_count'] = $broadcastEvents;
        $summary['total_properties'] = $totalProperties;
        $summary['total_listeners_found'] = $totalListeners;

        return $summary;
    }
}
