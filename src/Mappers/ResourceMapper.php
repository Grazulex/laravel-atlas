<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\File;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;

class ResourceMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'resources';
    }

    public function scan(array $options = []): array
    {
        $resources = [];
        $paths = $options['paths'] ?? [app_path('Http/Resources')];
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

                    if ($this->isResource($reflection)) {
                        $resources[] = $this->analyzeResource($reflection);
                    }
                }
            }
        }

        return [
            'type' => $this->type(),
            'count' => count($resources),
            'data' => $resources,
        ];
    }

    protected function isResource(ReflectionClass $reflection): bool
    {
        if ($reflection->isSubclassOf(JsonResource::class)) {
            return true;
        }

        return str_contains($reflection->getName(), 'Resource');
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeResource(ReflectionClass $reflection): array
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
            'file' => $file ?: 'Unknown',
            'traits' => $this->extractTraits($reflection),
            'methods' => $this->extractMethods($reflection),
            'relationships' => $this->extractRelationships($source),
            'conditionals' => $this->extractConditionals($source),
            'transformations' => $this->extractTransformations($source),
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

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function extractMethods(ReflectionClass $reflection): array
    {
        $methods = [];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // Ignorer les méthodes héritées de JsonResource
            if ($method->getDeclaringClass()->getName() !== $reflection->getName()) {
                continue;
            }

            $methods[] = [
                'name' => $method->getName(),
                'returnType' => $this->getMethodReturnType($method),
                'isStatic' => $method->isStatic(),
                'source' => 'class', // Toutes les méthodes de resource sont définies dans la classe
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

        if ($type instanceof ReflectionNamedType) {
            return $type->getName();
        }

        if ($type instanceof ReflectionUnionType) {
            return implode('|', array_map(fn ($t) => $t instanceof ReflectionNamedType ? $t->getName() : (string) $t, $type->getTypes()));
        }

        return 'mixed';
    }

    /**
     * @return array<int, string>
     */
    protected function extractRelationships(?string $source): array
    {
        $relationships = [];

        if (! $source) {
            return $relationships;
        }

        // Rechercher les relations dans les resources (whenLoaded, when)
        if (preg_match_all('/[\'"]([a-zA-Z_]+)[\'"]\s*=>\s*(?:new\s+)?([A-Z]\w+Resource)/', $source, $matches)) {
            $counter = count($matches[1]);
            for ($i = 0; $i < $counter; $i++) {
                $relationships[] = $matches[1][$i] . ' → ' . $matches[2][$i];
            }
        }

        // Rechercher whenLoaded
        if (preg_match_all('/whenLoaded\([\'"]([^\'"]+)[\'"]/', $source, $matches)) {
            foreach ($matches[1] as $relation) {
                $relationships[] = $relation . ' (whenLoaded)';
            }
        }

        return array_unique($relationships);
    }

    /**
     * @return array<int, string>
     */
    protected function extractConditionals(?string $source): array
    {
        $conditionals = [];

        if (! $source) {
            return $conditionals;
        }

        // Rechercher les when() conditionals avec une regex plus complète
        if (preg_match_all('/\$this->when\(([^,)]+(?:\([^)]*\))?[^,)]*),/', $source, $matches)) {
            foreach ($matches[1] as $match) {
                $condition = trim($match);
                // Nettoyer et raccourcir si trop long
                if (strlen($condition) > 50) {
                    $condition = substr($condition, 0, 47) . '...';
                }
                $conditionals[] = $condition;
            }
        }

        // Rechercher les mergeWhen avec une regex plus complète
        if (preg_match_all('/\$this->mergeWhen\(([^,)]+(?:\([^)]*\))?[^,)]*),/', $source, $matches)) {
            foreach ($matches[1] as $match) {
                $condition = trim($match);
                // Nettoyer et raccourcir si trop long
                if (strlen($condition) > 45) {
                    $condition = substr($condition, 0, 42) . '...';
                }
                $conditionals[] = 'merge: ' . $condition;
            }
        }

        // Rechercher les conditions simples dans les expressions ternaires
        if (preg_match_all('/\$this->([a-zA-Z_]+)\s*\?\s*/', $source, $matches)) {
            foreach ($matches[1] as $field) {
                $conditionals[] = '$this->' . $field . ' ? (optional)';
            }
        }

        return array_unique($conditionals);
    }

    /**
     * @return array<int, string>
     */
    protected function extractTransformations(?string $source): array
    {
        $transformations = [];

        if (! $source) {
            return $transformations;
        }

        // Rechercher les transformations de dates
        if (preg_match_all('/\$this->([a-zA-Z_]+)\?->format\([\'"]([^\'"]+)[\'"]/', $source, $matches)) {
            $counter = count($matches[1]);
            for ($i = 0; $i < $counter; $i++) {
                $transformations[] = $matches[1][$i] . ' → format(' . $matches[2][$i] . ')';
            }
        }

        // Rechercher les appels de services
        if (preg_match_all('/app\(([A-Z]\w+Service)::class\)->([a-zA-Z_]+)/', $source, $matches)) {
            $counter = count($matches[1]);
            for ($i = 0; $i < $counter; $i++) {
                $transformations[] = $matches[1][$i] . '::' . $matches[2][$i] . '()';
            }
        }

        return array_unique($transformations);
    }

    /**
     * @return array<string, array<int|string, mixed>>
     */
    protected function analyzeFlow(?string $source): array
    {
        $flow = [
            'services' => [],
            'facades' => [],
            'dependencies' => [
                'models' => [],
                'services' => [],
                'resources' => [],
                'facades' => [],
                'classes' => [],
            ],
        ];

        if (! $source) {
            return $flow;
        }

        // Services utilisés
        if (preg_match_all('/app\(([A-Z][\w\\\\]+Service)::class\)/', $source, $matches)) {
            foreach ($matches[1] as $service) {
                $flow['services'][] = $service;
                $flow['dependencies']['services'][] = $service;
            }
        }

        // Resources imbriqués
        if (preg_match_all('/new\s+([A-Z][\w\\\\]+Resource)/', $source, $matches)) {
            foreach ($matches[1] as $resource) {
                $flow['dependencies']['resources'][] = $resource;
            }
        }

        // Facades utilisées
        if (preg_match_all('/([A-Z][a-zA-Z]+)::/', $source, $matches)) {
            $facades = ['Auth', 'Gate', 'Cache', 'Storage', 'URL', 'Log'];
            foreach ($matches[1] as $facade) {
                if (in_array($facade, $facades)) {
                    $flow['facades'][] = $facade;
                    $flow['dependencies']['facades'][] = $facade;
                }
            }
        }

        // Nettoyer les doublons
        foreach ($flow['dependencies'] as &$dep) {
            $dep = array_values(array_unique($dep));
        }
        $flow['services'] = array_values(array_unique($flow['services']));
        $flow['facades'] = array_values(array_unique($flow['facades']));

        return $flow;
    }
}
