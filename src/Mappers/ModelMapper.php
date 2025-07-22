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
            'table' => $model->getTable(),
            'fillable' => $model->getFillable(),
            'guarded' => $model->getGuarded(),
            'casts' => $model->getCasts(),
            'relations' => [], // à remplir plus tard
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function guessRelations(Model $model): array
    {
        $relations = [];
        $class = get_class($model);
        $reflection = new ReflectionClass($class);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // Exclure les scopes, hérités, statiques, magiques, etc.
            if (
                $method->class !== $class ||
                $method->isStatic() ||
                $method->isConstructor() ||
                $method->getNumberOfParameters() > 0 ||
                str_starts_with($method->name, '__') ||
                str_starts_with($method->name, 'scope')
            ) {
                continue;
            }

            try {
                $result = $method->invoke($model);
                if ($result instanceof Relation) {
                    $relations[$method->getName()] = [
                        'type' => class_basename($result),
                        'related' => get_class($result->getRelated()),
                        'foreignKey' => method_exists($result, 'getForeignKeyName')
                            ? $result->getForeignKeyName()
                            : null,
                    ];
                }
            } catch (\Throwable) {
                // ne rien faire
            }
        }

        return $relations;
    }
}
