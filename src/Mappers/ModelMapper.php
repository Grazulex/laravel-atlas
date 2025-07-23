<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\File;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

class ModelMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'models';
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @return array<string, mixed>
     */
    public function scan(array $options = []): array
    {
        $models = [];

        $paths = $options['paths'] ?? [app_path('Models'), app_path()];
        $recursive = $options['recursive'] ?? true;

        $seen = [];

        foreach ($paths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            $files = $recursive ? File::allFiles($path) : File::files($path);

            foreach ($files as $file) {
                $fqcn = $this->resolveClassFromFile($file->getRealPath());

                if (
                    $fqcn &&
                    class_exists($fqcn) &&
                    is_subclass_of($fqcn, Model::class) &&
                    ! isset($seen[$fqcn])
                ) {
                    $reflection = new ReflectionClass($fqcn);
                    if (! $reflection->isAbstract()) {
                        $instance = app($fqcn);
                        $models[] = $this->analyzeModel($instance);
                        $seen[$fqcn] = true;
                    }
                }
            }
        }

        return [
            'type' => $this->type(),
            'count' => count($models),
            'data' => $models,
        ];
    }

    protected function resolveClassFromFile(string $path): ?string
    {
        return ClassResolver::resolveFromPath($path);
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeModel(Model $model): array
    {
        return [
            'class' => $model::class,
            'primary_key' => $model->getKeyName(),
            'table' => $model->getTable(),
            'fillable' => $model->getFillable(),
            'guarded' => $model->getGuarded(),
            'casts' => $model->getCasts(),
            'relations' => $this->guessRelations($model),
            'scopes' => $this->guessScopes($model),
            'booted_hooks' => $this->guessBootHooks($model),
            'flow' => $this->analyzeFlow($model),
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function guessRelations(Model $model): array
    {
        $relations = [];
        $class = $model::class;
        $reflection = new ReflectionClass($class);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // Exclure les scopes, hérités, statiques, magiques, etc.
            if ($method->class !== $class) {
                continue;
            }
            if ($method->isStatic()) {
                continue;
            }
            if ($method->isConstructor()) {
                continue;
            }
            if ($method->getNumberOfParameters() > 0) {
                continue;
            }
            if (str_starts_with($method->name, '__')) {
                continue;
            }
            if (str_starts_with($method->name, 'scope')) {
                continue;
            }
            try {
                $result = $method->invoke($model);
                if ($result instanceof Relation) {
                    $relations[$method->getName()] = [
                        'type' => class_basename($result),
                        'related' => $result->getRelated()::class,
                        'foreignKey' => method_exists($result, 'getForeignKeyName')
                            ? $result->getForeignKeyName()
                            : null,
                    ];
                }
            } catch (Throwable) {
                // ne rien faire
            }
        }

        return $relations;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function guessScopes(Model $model): array
    {
        $scopes = [];
        $class = $model::class;
        $reflection = new ReflectionClass($class);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (
                $method->class === $class &&
                str_starts_with($method->getName(), 'scope')
            ) {
                $scopeName = lcfirst(substr($method->getName(), 5));
                $scopes[] = [
                    'name' => $scopeName,
                    'parameters' => array_map(fn ($p): string => '$' . $p->getName(), $method->getParameters()),
                ];
            }
        }

        return $scopes;
    }

    /**
     * @return array<int, string>
     */
    protected function guessBootHooks(Model $model): array
    {
        $class = $model::class;
        $reflection = new ReflectionClass($class);

        if (! $reflection->hasMethod('boot')) {
            return [];
        }

        $method = $reflection->getMethod('boot');

        if ($method->class !== $class || ! $method->isStatic()) {
            return [];
        }

        $fileName = $reflection->getFileName();

        if ($fileName === false) {
            return [];
        }

        $contents = file_get_contents($fileName);

        if ($contents === false) {
            return [];
        }

        // Extraire les hooks Laravel statiques appelés dans boot()
        $matches = [];
        preg_match_all('/static::(saving|creating|updating|deleting|restoring|retrieved)\(/', $contents, $matches);

        return array_unique($matches[1]);
    }

    protected function analyzeFlow(Model $model): array
    {
        $flow = [
            'jobs' => [],
            'events' => [],
            'observers' => [],
            'dependencies' => [],
        ];

        $reflection = new ReflectionClass($model);
        $source = file_get_contents($reflection->getFileName());

        // Jobs dispatch
        if (preg_match_all('/dispatch(?:Now)?\((.*?)::class/', $source, $matches)) {
            foreach ($matches[1] as $class) {
                $flow['jobs'][] = [
                    'class' => trim($class),
                    'async' => ! str_contains($source, 'dispatchNow(' . $class),
                ];
            }
        }

        // Events
        if (preg_match_all('/event\((.*?)::class/', $source, $matches)) {
            foreach ($matches[1] as $class) {
                $flow['events'][] = ['class' => trim($class)];
            }
        }

        // Dependencies (naïve : via use ou new)
        if (preg_match_all('/new ([A-Z][\w\\\\]+)|([A-Z][\w\\\\]+)::/', $source, $matches)) {
            $deps = array_filter(array_merge($matches[1], $matches[2]));
            $flow['dependencies'] = array_values(array_unique(array_filter($deps)));
        }

        // Observers (via booted static::observe)
        if (preg_match_all('/static::observe\((.*?)::class/', $source, $matches)) {
            $flow['observers'] = array_map('trim', $matches[1]);
        }

        return $flow;
    }
}
