<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

class ControllerMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'controllers';
    }

    public function scan(array $options = []): array
    {
        $controllers = [];
        $paths = $options['paths'] ?? [app_path('Http/Controllers')];
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

                    if ($this->isController($reflection)) {
                        $controllers[] = $this->analyzeController($reflection);
                    }
                }
            }
        }

        return [
            'type' => $this->type(),
            'count' => count($controllers),
            'data' => $controllers,
        ];
    }

    protected function isController(ReflectionClass $reflection): bool
    {
        // Vérifier si hérite de Controller ou contient "Controller" dans le nom
        if ($reflection->isSubclassOf(Controller::class)) {
            return true;
        }

        return str_contains($reflection->getName(), 'Controller');
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeController(ReflectionClass $reflection): array
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
            'constructor' => $this->analyzeConstructor($reflection),
            'middlewares' => $this->extractMiddlewares($source),
            'methods' => $this->extractMethods($reflection),
            'dependencies' => $this->extractDependencies($reflection),
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
     * @return array<string, mixed>
     */
    protected function analyzeConstructor(ReflectionClass $reflection): array
    {
        $constructor = $reflection->getConstructor();

        if (! $constructor) {
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

        return [
            'parameters' => $parameters,
            'visibility' => $constructor->isPublic() ? 'public' : ($constructor->isProtected() ? 'protected' : 'private'),
        ];
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
            return implode('|', array_map(fn ($t) => $t instanceof ReflectionNamedType ? $t->getName() : (string) $t, $type->getTypes()));
        }

        return 'mixed';
    }

    /**
     * @return array<int, string>
     */
    protected function extractMiddlewares(?string $source): array
    {
        $middlewares = [];

        if (! $source) {
            return $middlewares;
        }

        // Rechercher les middlewares dans le constructeur ou les méthodes
        if (preg_match_all('/\$this->middleware\([\'"]([^\'"]+)[\'"]/', $source, $matches)) {
            $middlewares = array_merge($middlewares, $matches[1]);
        }

        return array_unique($middlewares);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function extractMethods(ReflectionClass $reflection): array
    {
        $methods = [];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // Ignorer les méthodes héritées de la classe parent Controller
            if ($method->getDeclaringClass()->getName() !== $reflection->getName()) {
                continue;
            }

            // Ignorer les méthodes magiques
            if (str_starts_with($method->getName(), '__')) {
                continue;
            }

            $methods[] = [
                'name' => $method->getName(),
                'parameters' => $this->getMethodParameters($method),
                'returnType' => $this->getMethodReturnType($method),
                'isStatic' => $method->isStatic(),
                'source' => 'class', // Toutes les méthodes du controller sont définies dans la classe
            ];
        }

        return $methods;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getMethodParameters(ReflectionMethod $method): array
    {
        $parameters = [];

        foreach ($method->getParameters() as $parameter) {
            $parameters[] = [
                'name' => $parameter->getName(),
                'type' => $this->getParameterType($parameter),
                'hasDefault' => $parameter->isDefaultValueAvailable(),
                'nullable' => $parameter->allowsNull(),
            ];
        }

        return $parameters;
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
     * @return array<string, array<int, string>>
     */
    protected function extractDependencies(ReflectionClass $reflection): array
    {
        $dependencies = [
            'models' => [],
            'services' => [],
            'requests' => [],
            'resources' => [],
            'facades' => [],
        ];

        $constructor = $reflection->getConstructor();
        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                $type = $this->getParameterType($parameter);

                if (str_contains($type, 'App\\Models\\')) {
                    $dependencies['models'][] = class_basename($type);
                } elseif (str_contains($type, 'App\\Services\\')) {
                    $dependencies['services'][] = class_basename($type);
                } elseif (str_contains($type, 'App\\Http\\Requests\\')) {
                    $dependencies['requests'][] = class_basename($type);
                }
            }
        }

        return $dependencies;
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
            'redirects' => [],
            'views' => [],
            'dependencies' => [
                'models' => [],
                'services' => [],
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

        // Événements
        if (preg_match_all('/event\(\s*new\s+([A-Z][\w\\\\]+)/', $source, $matches)) {
            foreach ($matches[1] as $fqcn) {
                $flow['events'][] = ['class' => $fqcn];
            }
        }

        // Redirections
        if (preg_match_all('/redirect\(\)->(?:route|to)\([\'"]([^\'"]+)[\'"]/', $source, $matches)) {
            $flow['redirects'] = array_unique($matches[1]);
        }

        // Vues
        if (preg_match_all('/view\([\'"]([^\'"]+)[\'"]/', $source, $matches)) {
            $flow['views'] = array_unique($matches[1]);
        }

        // Modèles et classes utilisées
        if (preg_match_all('/new\s+([A-Z][\w\\\\]+)|([A-Z][\w\\\\]+)::/', $source, $matches)) {
            $found = array_filter(array_merge($matches[1], $matches[2]));
            $classes = array_values(array_unique(array_filter($found)));

            foreach ($classes as $fqcn) {
                $basename = class_basename($fqcn);
                if (str_contains($fqcn, 'App\\Models\\')) {
                    $flow['dependencies']['models'][] = $fqcn;
                } elseif (str_contains($fqcn, 'App\\Services\\')) {
                    $flow['dependencies']['services'][] = $fqcn;
                } elseif (in_array($basename, ['Auth', 'Gate', 'DB', 'Cache', 'Mail', 'Log', 'Response'])) {
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
