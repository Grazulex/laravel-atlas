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
                    is_subclass_of($fqcn, Model::class)
                ) {
                    $reflection = new ReflectionClass($fqcn);
                    if (! $reflection->isAbstract()) {
                        $instance = app($fqcn);
                        $models[] = $this->analyzeModel($instance);
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

        $class = $model::class;
        $methods = (new ReflectionClass($class))->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if ($method->class !== $class) {
                continue;
            }
            if ($method->getNumberOfParameters() > 0) {
                continue;
            }
            if ($method->isStatic()) {
                continue;
            }
            try {
                $return = $method->invoke($model);
                if ($return instanceof Relation) {
                    $relations[$method->getName()] = class_basename($return::class);
                }
            } catch (Throwable) {
                // silencieux
            }
        }

        return $relations;
    }
}
