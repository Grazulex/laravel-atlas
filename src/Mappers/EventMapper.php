<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

class EventMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'events';
    }

    public function scan(array $options = []): array
    {
        $events = [];
        $paths = $options['paths'] ?? [app_path('Events')];
        $recursive = $options['recursive'] ?? true;

        foreach ($paths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            $files = $recursive ? File::allFiles($path) : File::files($path);

            foreach ($files as $file) {
                $fqcn = ClassResolver::resolveFromPath($file->getRealPath());

                if ($fqcn && class_exists($fqcn)) {
                    $reflection = new ReflectionClass($fqcn);

                    // Vérifier si c'est un événement (utilise le trait Dispatchable ou a une méthode handle/fire)
                    if ($this->isEvent($reflection)) {
                        $events[] = $this->analyzeEvent($reflection);
                    }
                }
            }
        }

        return [
            'type' => $this->type(),
            'count' => count($events),
            'data' => $events,
        ];
    }

    protected function isEvent(ReflectionClass $reflection): bool
    {
        // Vérifier les traits Laravel d'événements
        $traits = $reflection->getTraitNames();
        $eventTraits = [
            Dispatchable::class,
            InteractsWithSockets::class,
            SerializesModels::class,
        ];

        foreach ($eventTraits as $trait) {
            if (in_array($trait, $traits)) {
                return true;
            }
        }

        // Vérifier si implémente une interface de broadcast (sans forcer l'import)
        foreach ($reflection->getInterfaceNames() as $interface) {
            if (str_contains($interface, 'ShouldBroadcast')) {
                return true;
            }
        }

        // Vérifier si c'est dans le namespace Events
        return str_contains($reflection->getName(), '\\Events\\');
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeEvent(ReflectionClass $reflection): array
    {
        $class = $reflection->getName();
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
            'file' => $file ?: 'N/A',
            'traits' => $this->extractTraits($reflection),
            'properties' => $this->extractProperties($reflection),
            'broadcastable' => $this->isBroadcastable($reflection),
            'channels' => $this->extractBroadcastChannels($source),
            'listeners' => $this->findListeners($class),
            'flow' => $this->analyzeFlow($source),
        ];
    }

    protected function isBroadcastable(ReflectionClass $reflection): bool
    {
        foreach ($reflection->getInterfaceNames() as $interface) {
            if (str_contains($interface, 'ShouldBroadcast')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string>
     */
    protected function extractTraits(ReflectionClass $reflection): array
    {
        $traits = [];
        $traitNames = $reflection->getTraitNames();

        foreach ($traitNames as $trait) {
            // Garder seulement le nom de classe (pas le namespace complet)
            $traits[] = class_basename($trait);
        }

        return $traits;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function extractProperties(ReflectionClass $reflection): array
    {
        $properties = [];
        $constructor = $reflection->getConstructor();

        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                $properties[] = [
                    'name' => $parameter->getName(),
                    'type' => $this->getParameterType($parameter),
                    'hasDefault' => $parameter->isDefaultValueAvailable(),
                    'default' => $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null,
                    'nullable' => $parameter->allowsNull(),
                ];
            }
        }

        return $properties;
    }

    protected function getParameterType(ReflectionParameter $parameter): string
    {
        $type = $parameter->getType();

        if ($type === null) {
            return 'mixed';
        }

        if ($type instanceof ReflectionNamedType) {
            return $type->getName();
        }

        if ($type instanceof ReflectionUnionType) {
            return implode('|', array_map(fn (ReflectionIntersectionType|ReflectionNamedType $t) => $t instanceof ReflectionNamedType ? $t->getName() : (string) $t, $type->getTypes()));
        }

        return 'mixed';
    }

    /**
     * @return array<int, string>
     */
    protected function extractBroadcastChannels(?string $source): array
    {
        $channels = [];

        if (! $source) {
            return $channels;
        }

        // Rechercher les canaux dans la méthode broadcastOn
        if (preg_match('/public function broadcastOn\(\)[^{]*{([^}]+)}/', $source, $matches)) {
            $method = $matches[1];

            // Rechercher les canaux (Channel, PrivateChannel, PresenceChannel)
            if (preg_match_all('/new\s+(?:Private|Presence)?Channel\(\s*[\'"]([^\'"]+)[\'"]/', $method, $channelMatches)) {
                $channels = array_merge($channels, $channelMatches[1]);
            }
        }

        return array_unique($channels);
    }

    /**
     * @return array<int, string>
     */
    protected function findListeners(string $eventClass): array
    {
        // Ceci est une implémentation basique - en pratique, on pourrait scanner les EventServiceProvider
        // ou utiliser les listeners enregistrés dans l'application
        return [];
    }

    /**
     * @return array<string, array<int|string, mixed>>
     */
    protected function analyzeFlow(?string $source): array
    {
        $flow = [
            'jobs' => [],
            'events' => [],
            'notifications' => [],
            'models' => [],
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

        // Jobs dispatchés
        if (preg_match_all('/dispatch(?:Now)?\(\s*new\s+([A-Z][\w\\\\]+)/', $source, $matches)) {
            foreach ($matches[1] as $fqcn) {
                $flow['jobs'][] = [
                    'class' => $fqcn,
                    'async' => ! str_contains($source, "dispatchNow(new {$fqcn}"),
                ];
            }
        }

        // Autres événements déclenchés
        if (preg_match_all('/event\(\s*new\s+([A-Z][\w\\\\]+)/', $source, $matches)) {
            foreach ($matches[1] as $fqcn) {
                $flow['events'][] = ['class' => $fqcn];
            }
        }

        // Notifications
        if (preg_match_all('/->notify\(\s*new\s+([A-Z][\w\\\\]+)/', $source, $matches)) {
            foreach ($matches[1] as $fqcn) {
                $flow['notifications'][] = ['class' => $fqcn];
                $flow['dependencies']['notifications'][] = $fqcn;
            }
        }

        // Modèles utilisés
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
                } elseif (in_array($basename, ['Log', 'Queue', 'Bus', 'Mail', 'DB', 'Cache', 'Event', 'Broadcast'])) {
                    $flow['dependencies']['facades'][] = $basename;
                } else {
                    $flow['dependencies']['classes'][] = $fqcn;
                }
            }
        }

        // Nettoyer les doublons
        foreach ($flow['dependencies'] as &$dep) {
            $dep = array_values(array_unique($dep));
        }

        return $flow;
    }
}
