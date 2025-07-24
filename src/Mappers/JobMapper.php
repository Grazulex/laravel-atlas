<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\File;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

class JobMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'jobs';
    }

    public function scan(array $options = []): array
    {
        $jobs = [];
        $paths = $options['paths'] ?? [app_path('Jobs')];
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
                    
                    if ($this->isJob($reflection)) {
                        $jobs[] = $this->analyzeJob($reflection);
                    }
                }
            }
        }

        return [
            'type' => $this->type(),
            'count' => count($jobs),
            'data' => $jobs,
        ];
    }

    protected function isJob(ReflectionClass $reflection): bool
    {
        // Vérifier si implémente ShouldQueue ou a les traits de Job
        $interfaces = $reflection->getInterfaceNames();
        $traits = $reflection->getTraitNames();
        
        $jobTraits = [
            'Illuminate\Foundation\Bus\Dispatchable',
            'Illuminate\Bus\Queueable',
            'Illuminate\Queue\InteractsWithQueue',
            'Illuminate\Queue\SerializesModels',
        ];

        foreach ($jobTraits as $trait) {
            if (in_array($trait, $traits)) {
                return true;
            }
        }

        foreach ($interfaces as $interface) {
            if (str_contains($interface, 'ShouldQueue')) {
                return true;
            }
        }

        return str_contains($reflection->getName(), '\\Jobs\\') || 
               str_contains($reflection->getName(), 'Job');
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeJob(ReflectionClass $reflection): array
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
            'name' => $reflection->getShortName(),
            'traits' => $this->extractTraits($reflection),
            'queueable' => $this->isQueueable($reflection),
            'properties' => $this->extractProperties($reflection, $source),
            'constructor' => $this->analyzeConstructor($reflection),
            'methods' => $this->extractMethods($reflection),
            'queue_config' => $this->extractQueueConfig($source),
            'flow' => $this->analyzeFlow($source),
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function extractTraits(ReflectionClass $reflection): array
    {
        $traits = [];
        foreach ($reflection->getTraitNames() as $trait) {
            $traits[] = class_basename($trait);
        }
        return $traits;
    }

    protected function isQueueable(ReflectionClass $reflection): bool
    {
        foreach ($reflection->getInterfaceNames() as $interface) {
            if (str_contains($interface, 'ShouldQueue')) {
                return true;
            }
        }
        
        $traits = $reflection->getTraitNames();
        return in_array('Illuminate\Bus\Queueable', $traits);
    }

        /**
     * @return array<int, array<string, mixed>>
     */
    protected function extractProperties(ReflectionClass $reflection, ?string $source): array
    {
        $properties = [];
        
        // Propriétés publiques définies dans la classe
        foreach ($reflection->getProperties() as $property) {
            if ($property->getDeclaringClass()->getName() === $reflection->getName()) {
                $properties[] = [
                    'name' => $property->getName(),
                    'type' => $property->isPublic() ? 'public' : ($property->isProtected() ? 'protected' : 'private'),
                    'static' => $property->isStatic(),
                    'hasDefault' => $property->hasDefaultValue(),
                ];
            }
        }

        return $properties;
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeConstructor(ReflectionClass $reflection): array
    {
        $constructor = $reflection->getConstructor();
        
        if (!$constructor) {
            return ['parameters' => []];
        }

        $parameters = [];
        foreach ($constructor->getParameters() as $parameter) {
            $parameters[] = [
                'name' => $parameter->getName(),
                'type' => $this->getParameterType($parameter),
                'hasDefault' => $parameter->isDefaultValueAvailable(),
                'nullable' => $parameter->allowsNull(),
            ];
        }

        return ['parameters' => $parameters];
    }

    protected function getParameterType(ReflectionParameter $parameter): string
    {
        $type = $parameter->getType();
        
        if ($type === null) {
            return 'mixed';
        }

        if ($type instanceof \ReflectionNamedType) {
            return $type->getName();
        }

        if ($type instanceof \ReflectionUnionType) {
            return implode('|', array_map(fn($t) => $t->getName(), $type->getTypes()));
        }

        return 'mixed';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function extractMethods(ReflectionClass $reflection): array
    {
        $methods = [];
        $classTraits = $reflection->getTraitNames();
        
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // Ignorer les méthodes magiques
            if (str_starts_with($method->getName(), '__')) {
                continue;
            }

            $source = 'class';
            $declaringClass = $method->getDeclaringClass();
            
            // Déterminer la source de la méthode
            if ($declaringClass->getName() !== $reflection->getName()) {
                $declaringClassName = $declaringClass->getName();
                
                // Vérifier si c'est un trait en parcourant les traits utilisés
                foreach ($classTraits as $traitName) {
                    // Essayer de créer une réflexion du trait
                    try {
                        $traitReflection = new ReflectionClass($traitName);
                        if ($traitReflection->hasMethod($method->getName())) {
                            $source = 'trait: ' . class_basename($traitName);
                            break;
                        }
                    } catch (\Exception $e) {
                        // Ignorer les erreurs de réflexion
                    }
                }
                
                // Si ce n'est toujours pas trouvé, vérifier les interfaces et classes parentes
                if ($source === 'class') {
                    if ($declaringClass->isInterface()) {
                        $source = 'interface: ' . class_basename($declaringClassName);
                    } elseif ($declaringClass->isTrait()) {
                        $source = 'trait: ' . class_basename($declaringClassName);
                    } else {
                        $source = 'parent: ' . class_basename($declaringClassName);
                    }
                }
            }

            $methods[] = [
                'name' => $method->getName(),
                'returnType' => $this->getMethodReturnType($method),
                'isStatic' => $method->isStatic(),
                'source' => $source,
            ];
        }

        return $methods;
    }

    protected function getMethodReturnType(ReflectionMethod $method): string
    {
        $type = $method->getReturnType();
        
        if ($type === null) {
            return 'mixed';
        }

        if ($type instanceof \ReflectionNamedType) {
            return $type->getName();
        }

        if ($type instanceof \ReflectionUnionType) {
            return implode('|', array_map(fn($t) => $t->getName(), $type->getTypes()));
        }

        return 'mixed';
    }

    /**
     * @return array<string, mixed>
     */
    protected function extractQueueConfig(?string $source): array
    {
        $config = [];

        if (!$source) {
            return $config;
        }

        // Rechercher les configurations de queue
        if (preg_match('/public\s+\$tries\s*=\s*(\d+)/', $source, $matches)) {
            $config['tries'] = (int) $matches[1];
        }

        if (preg_match('/public\s+\$maxExceptions\s*=\s*(\d+)/', $source, $matches)) {
            $config['maxExceptions'] = (int) $matches[1];
        }

        if (preg_match('/public\s+\$timeout\s*=\s*(\d+)/', $source, $matches)) {
            $config['timeout'] = (int) $matches[1];
        }

        if (preg_match('/\$this->onQueue\([\'"]([^\'"]+)[\'"]/', $source, $matches)) {
            $config['queue'] = $matches[1];
        }

        if (preg_match('/\$this->delay\(([^)]+)\)/', $source, $matches)) {
            $config['delay'] = trim($matches[1]);
        }

        return $config;
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
            'dependencies' => [
                'models' => [],
                'services' => [],
                'facades' => [],
                'classes' => [],
            ],
        ];

        if (!$source) {
            return $flow;
        }

        // Jobs dispatchés
        if (preg_match_all('/dispatch(?:Now)?\(\s*new\s+([A-Z][\w\\\\]+)/', $source, $matches)) {
            foreach ($matches[1] as $fqcn) {
                $flow['jobs'][] = [
                    'class' => $fqcn,
                    'async' => !str_contains($source, "dispatchNow(new {$fqcn}"),
                ];
            }
        }

        // Événements déclenchés
        if (preg_match_all('/event\(\s*new\s+([A-Z][\w\\\\]+)/', $source, $matches)) {
            foreach ($matches[1] as $fqcn) {
                $flow['events'][] = ['class' => $fqcn];
            }
        }

        // Notifications
        if (preg_match_all('/->notify\(\s*new\s+([A-Z][\w\\\\]+)/', $source, $matches)) {
            foreach ($matches[1] as $fqcn) {
                $flow['notifications'][] = ['class' => $fqcn];
            }
        }

        // Classes utilisées
        if (preg_match_all('/new\s+([A-Z][\w\\\\]+)|([A-Z][\w\\\\]+)::/', $source, $matches)) {
            $found = array_filter(array_merge($matches[1], $matches[2]));
            $classes = array_values(array_unique(array_filter($found)));

            foreach ($classes as $fqcn) {
                $basename = class_basename($fqcn);
                if (str_contains($fqcn, 'App\\Models\\')) {
                    $flow['dependencies']['models'][] = $fqcn;
                } elseif (str_contains($fqcn, 'App\\Services\\')) {
                    $flow['dependencies']['services'][] = $fqcn;
                } elseif (in_array($basename, ['Log', 'Mail', 'DB', 'Cache', 'Bus', 'Event'])) {
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
