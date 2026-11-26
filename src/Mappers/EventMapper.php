<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use LaravelAtlas\Support\EventListenerDiscovery;
use LaravelAtlas\Support\ModelLocator;
use LaravelAtlas\Support\ScanOptions;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use Throwable;

class EventMapper implements ComponentMapper
{
    /**
     * Cached event-listener mappings from EventServiceProvider
     *
     * @var array<string, array<int, string>>|null
     */
    protected ?array $eventListenerMap = null;

    /**
     * Listeners collected from providers, subscribers, event discovery and
     * the runtime dispatcher for the current scan.
     */
    protected ?EventListenerDiscovery $discovery = null;

    /**
     * Models dispatching each event through `$dispatchesEvents`.
     *
     * @var array<string, array<int, array{model: string, event: string}>>
     */
    protected array $modelDispatches = [];

    public function type(): string
    {
        return 'events';
    }

    public function scan(array $options = []): array
    {
        // Build the event-listener map before scanning events
        $this->buildEventListenerMap();
        $this->discovery = new EventListenerDiscovery;
        $this->modelDispatches = $this->buildModelDispatches($options);

        $events = [];
        $seen = [];
        /** @var array<int, string> $paths */
        $paths = isset($options['paths']) && is_array($options['paths'])
            ? array_values(array_filter($options['paths'], is_string(...)))
            : $this->getEventPaths();
        $recursive = $options['recursive'] ?? true;

        foreach ($paths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            $files = $recursive ? File::allFiles($path) : File::files($path);

            foreach ($files as $file) {
                $fqcn = ClassResolver::resolveFromFile($file->getRealPath());

                if ($fqcn && class_exists($fqcn)) {
                    $reflection = new ReflectionClass($fqcn);

                    // Skip abstract classes - they should not appear in exports
                    if ($reflection->isAbstract()) {
                        continue;
                    }

                    // Vérifier si c'est un événement (utilise le trait Dispatchable ou a une méthode handle/fire)
                    if ($this->isEvent($reflection) && ! isset($seen[$fqcn])) {
                        $seen[$fqcn] = true;
                        $events[] = $this->analyzeEvent($reflection, $options);
                    }
                }
            }
        }

        // Events living outside the event paths (framework events, string
        // events, events of other modules) that the application listens to,
        // or that its models dispatch through `$dispatchesEvents`.
        $external = array_merge(
            array_keys($this->discovery->all()),
            array_keys($this->modelDispatches),
        );

        foreach (array_unique($external) as $event) {
            if (isset($seen[$event])) {
                continue;
            }

            $seen[$event] = true;
            $origin = isset($this->modelDispatches[$event]) && $this->discovery->forEvent($event) === [] ? 'model' : 'listener';

            if (class_exists($event) || interface_exists($event)) {
                $events[] = $this->analyzeEvent(new ReflectionClass($event), $options, $origin);
            } else {
                $events[] = $this->describeNamedEvent($event, $options, $origin);
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
     * @param  array<string, mixed>  $options
     *
     * @return array<string, mixed>
     */
    protected function analyzeEvent(ReflectionClass $reflection, array $options = [], string $origin = 'scanned'): array
    {
        $class = $reflection->getName();
        $file = $reflection->getFileName();

        $source = null;
        if ($file && file_exists($file)) {
            $content = file_get_contents($file);
            $source = $content !== false ? $content : null;
        }

        return ScanOptions::filter([
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
            'origin' => $origin,
            'dispatched_by' => $this->modelDispatches[$class] ?? [],
        ], $options, [
            'include_listeners' => ['listeners'],
            'include_properties' => ['properties'],
        ]);
    }

    /**
     * Describe an event registered by name only (e.g. `Event::listen('orders.*', ...)`).
     *
     * @param  array<string, mixed>  $options
     *
     * @return array<string, mixed>
     */
    protected function describeNamedEvent(string $event, array $options, string $origin): array
    {
        return ScanOptions::filter([
            'class' => $event,
            'namespace' => '',
            'name' => $event,
            'file' => 'N/A',
            'traits' => [],
            'properties' => [],
            'broadcastable' => false,
            'channels' => [],
            'listeners' => $this->findListeners($event),
            'flow' => $this->analyzeFlow(null),
            'origin' => $origin,
            'dispatched_by' => $this->modelDispatches[$event] ?? [],
        ], $options, [
            'include_listeners' => ['listeners'],
            'include_properties' => ['properties'],
        ]);
    }

    /**
     * Map each event to the models dispatching it through `$dispatchesEvents`.
     *
     * @param  array<string, mixed>  $options
     *
     * @return array<string, array<int, array{model: string, event: string}>>
     */
    protected function buildModelDispatches(array $options): array
    {
        $paths = ModelLocator::pathsFromOptions($options);

        $map = [];

        try {
            foreach (ModelLocator::find($paths) as $model) {
                foreach (ModelLocator::dispatchedEvents($model) as $modelEvent => $event) {
                    $map[ltrim($event, '\\')][] = ['model' => $model, 'event' => $modelEvent];
                }
            }
        } catch (Throwable) {
            // Model discovery is best effort
        }

        return $map;
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

        if ($constructor instanceof ReflectionMethod) {
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

        if (! $type instanceof ReflectionType) {
            return 'mixed';
        }

        if ($type instanceof ReflectionNamedType) {
            return $type->getName();
        }

        if ($type instanceof ReflectionUnionType) {
            return implode('|', array_map(fn (ReflectionIntersectionType|ReflectionNamedType $t): string => $t instanceof ReflectionNamedType ? $t->getName() : (string) $t, $type->getTypes()));
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
     * Find listeners for a specific event class
     *
     * @return array<int, array<string, mixed>>
     */
    protected function findListeners(string $eventClass): array
    {
        $listeners = [];

        // Get listeners from EventServiceProvider mappings
        if ($this->eventListenerMap !== null) {
            // Check exact match
            if (isset($this->eventListenerMap[$eventClass])) {
                foreach ($this->eventListenerMap[$eventClass] as $listenerClass) {
                    $listeners[] = $this->providerListener($listenerClass);
                }
            }

            // Check by short class name (for cases where namespace might differ)
            $shortName = class_basename($eventClass);
            foreach ($this->eventListenerMap as $event => $eventListeners) {
                if (class_basename($event) === $shortName && $event !== $eventClass) {
                    foreach ($eventListeners as $listenerClass) {
                        $listeners[] = $this->providerListener($listenerClass);
                    }
                }
            }
        }

        // Listeners declared explicitly ($listen arrays of the booted event
        // service providers, subscribers) take precedence over type-hints
        $discovered = $this->discovery?->forEvent($eventClass) ?? [];
        $explicit = [EventListenerDiscovery::SOURCE_PROVIDER, EventListenerDiscovery::SOURCE_SUBSCRIBER];
        $listeners = $this->mergeListeners($listeners, array_filter(
            $discovered,
            fn (array $listener): bool => in_array($listener['source'], $explicit, true),
        ));

        // Also scan listener files for handle() method type hints
        $listenersFromHandleMethod = $this->findListenersFromHandleMethod($eventClass);
        foreach ($listenersFromHandleMethod as $listenerClass) {
            // Avoid duplicates
            $exists = false;
            foreach ($listeners as $listener) {
                if ($listener['class'] === $listenerClass) {
                    $exists = true;
                    break;
                }
            }
            if (! $exists) {
                $listeners[] = [
                    'class' => $listenerClass,
                    'name' => class_basename($listenerClass),
                    'source' => 'handle() type-hint',
                    'method' => 'handle',
                ];
            }
        }

        // Then event discovery and the runtime dispatcher (Event::listen(), closures, ...)
        return $this->mergeListeners($listeners, $discovered);
    }

    /**
     * Append the listeners not already listed (same class and method).
     *
     * @param  array<int, array<string, mixed>>  $listeners
     * @param  array<int, array{class: string, name: string, method: string|null, source: string}>  $additional
     *
     * @return array<int, array<string, mixed>>
     */
    protected function mergeListeners(array $listeners, array $additional): array
    {
        foreach ($additional as $listener) {
            foreach ($listeners as $existing) {
                if ($existing['class'] === $listener['class']
                    && ($existing['method'] ?? null) === $listener['method']) {
                    continue 2;
                }
            }

            $listeners[] = $listener;
        }

        return $listeners;
    }

    /**
     * @return array{class: string, name: string, source: string, method: string}
     */
    protected function providerListener(string $listener): array
    {
        [$class, $method] = str_contains($listener, '@')
            ? explode('@', $listener, 2)
            : [$listener, 'handle'];

        return [
            'class' => $class,
            'name' => class_basename($class),
            'source' => 'EventServiceProvider',
            'method' => $method,
        ];
    }

    /**
     * Build event-listener mappings from EventServiceProvider
     */
    protected function buildEventListenerMap(): void
    {
        if ($this->eventListenerMap !== null) {
            return;
        }

        $this->eventListenerMap = [];

        // Scan for EventServiceProvider files
        $providerPaths = [
            app_path('Providers/EventServiceProvider.php'),
            app_path('Providers'),
        ];

        foreach ($providerPaths as $path) {
            if (is_file($path)) {
                $this->parseEventServiceProvider($path);
            } elseif (is_dir($path)) {
                $files = File::files($path);
                foreach ($files as $file) {
                    if (str_contains($file->getFilename(), 'EventServiceProvider')) {
                        $this->parseEventServiceProvider($file->getRealPath());
                    }
                }
            }
        }
    }

    /**
     * Parse an EventServiceProvider file to extract event-listener mappings
     */
    protected function parseEventServiceProvider(string $filePath): void
    {
        if (! file_exists($filePath)) {
            return;
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            return;
        }

        // Try to get the FQCN and use reflection if the class exists
        $fqcn = ClassResolver::resolveFromFile($filePath);
        if ($fqcn && class_exists($fqcn)) {
            try {
                $reflection = new ReflectionClass($fqcn);
                if ($reflection->hasProperty('listen')) {
                    $property = $reflection->getProperty('listen');
                    $instance = $reflection->newInstanceWithoutConstructor();
                    $listen = $property->getValue($instance);

                    if (is_array($listen)) {
                        foreach ($listen as $event => $listeners) {
                            $this->eventListenerMap[$event] ??= [];
                            foreach ((array) $listeners as $listener) {
                                $this->eventListenerMap[$event][] = $listener;
                            }
                        }

                        return;
                    }
                }
            } catch (Throwable) {
                // Fall through to regex parsing
            }
        }

        // Fallback: Parse using regex for cases where reflection fails
        // Match the $listen property array
        if (preg_match('/protected\s+\$listen\s*=\s*\[([\s\S]*?)\];/m', $content, $matches)) {
            $listenContent = $matches[1];

            // Match event => listener(s) pairs
            // Pattern: EventClass::class => [ListenerClass::class, ...] or EventClass::class => ListenerClass::class
            if (preg_match_all('/([A-Z][\w\\\\]+)::class\s*=>\s*(?:\[([\s\S]*?)\]|([A-Z][\w\\\\]+)::class)/m', $listenContent, $eventMatches, PREG_SET_ORDER)) {
                foreach ($eventMatches as $match) {
                    $eventClass = $match[1];

                    // Single listener
                    if (! empty($match[3])) {
                        $this->eventListenerMap[$eventClass] ??= [];
                        $this->eventListenerMap[$eventClass][] = $match[3];
                    }
                    // Array of listeners
                    elseif (! empty($match[2])) {
                        if (preg_match_all('/([A-Z][\w\\\\]+)::class/', $match[2], $listenerMatches)) {
                            $this->eventListenerMap[$eventClass] ??= [];
                            foreach ($listenerMatches[1] as $listener) {
                                $this->eventListenerMap[$eventClass][] = $listener;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Find listeners by scanning Listener files for handle() method type hints
     *
     * @return array<int, string>
     */
    protected function findListenersFromHandleMethod(string $eventClass): array
    {
        $listeners = [];
        $listenerPaths = $this->getListenerPaths();

        foreach ($listenerPaths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            $files = File::allFiles($path);
            foreach ($files as $file) {
                $fqcn = ClassResolver::resolveFromFile($file->getRealPath());
                if (! $fqcn) {
                    continue;
                }
                if (! class_exists($fqcn)) {
                    continue;
                }

                try {
                    $reflection = new ReflectionClass($fqcn);

                    // Skip abstract classes
                    if ($reflection->isAbstract()) {
                        continue;
                    }

                    if ($reflection->hasMethod('handle')) {
                        $handleMethod = $reflection->getMethod('handle');
                        $parameters = $handleMethod->getParameters();

                        if (count($parameters) > 0) {
                            $firstParam = $parameters[0];
                            $paramType = $firstParam->getType();

                            // Check if this listener handles our event (supports union types)
                            if ($this->typeMatchesEvent($paramType, $eventClass)) {
                                $listeners[] = $fqcn;
                            }
                        }
                    }
                } catch (Throwable) {
                    // Skip problematic classes
                }
            }
        }

        return $listeners;
    }

    /**
     * Check if a reflection type matches the given event class
     * Supports named types, union types, and nullable types
     */
    protected function typeMatchesEvent(?ReflectionType $type, string $eventClass): bool
    {
        if (! $type instanceof ReflectionType) {
            return false;
        }

        // Handle union types (e.g., EventA|EventB)
        if ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $unionType) {
                if ($this->typeMatchesEvent($unionType, $eventClass)) {
                    return true;
                }
            }

            return false;
        }

        // Handle named types (including nullable)
        if ($type instanceof ReflectionNamedType) {
            $typeName = $type->getName();

            // Skip built-in types
            if ($type->isBuiltin()) {
                return false;
            }

            return $typeName === $eventClass || class_basename($typeName) === class_basename($eventClass);
        }

        return false;
    }

    /**
     * Get listener paths from config with fallback to default
     *
     * @return array<int, string>
     */
    protected function getListenerPaths(): array
    {
        /** @var array<int, string> $configPaths */
        $configPaths = config('atlas.paths.listeners', []);

        if (is_array($configPaths) && $configPaths !== []) {
            return array_values(array_filter($configPaths, is_string(...)));
        }

        // Default paths
        $paths = [app_path('Listeners')];

        // Add beta_app if it exists (for backward compatibility)
        $betaAppPath = base_path('beta_app/app/Listeners');
        if (is_dir($betaAppPath)) {
            $paths[] = $betaAppPath;
        }

        return $paths;
    }

    /**
     * Get event paths from config with fallback to default
     *
     * @return array<int, string>
     */
    protected function getEventPaths(): array
    {
        /** @var array<int, string> $configPaths */
        $configPaths = config('atlas.paths.events', []);

        if (is_array($configPaths) && $configPaths !== []) {
            return array_values(array_filter($configPaths, is_string(...)));
        }

        // Default path
        return [app_path('Events')];
    }

    /**
     * Get the complete event-listener map
     *
     * @return array<string, array<int, string>>
     */
    public function getEventListenerMap(): array
    {
        if ($this->eventListenerMap === null) {
            $this->buildEventListenerMap();
        }

        return $this->eventListenerMap ?? [];
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
