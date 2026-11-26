<?php

declare(strict_types=1);

namespace LaravelAtlas\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use ReflectionClass;

/**
 * Finds the concrete Eloquent models declared in a set of directories.
 */
class ModelLocator
{
    /**
     * @param  array<int, string>|null  $paths  Defaults to the paths scanned by the models mapper
     *
     * @return array<int, class-string<Model>>
     */
    public static function find(?array $paths = null, bool $recursive = true): array
    {
        $paths ??= [app_path('Models'), app_path()];
        $models = [];

        foreach ($paths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            $files = $recursive ? File::allFiles($path) : File::files($path);

            foreach ($files as $file) {
                $class = ClassResolver::resolveFromFile($file->getRealPath());

                if ($class === null || isset($models[$class]) || ! is_subclass_of($class, Model::class)) {
                    continue;
                }

                if ((new ReflectionClass($class))->isAbstract()) {
                    continue;
                }

                $models[$class] = $class;
            }
        }

        return array_values($models);
    }

    /**
     * The `model_paths` scan option, or null for the default paths.
     *
     * @param  array<string, mixed>  $options
     *
     * @return array<int, string>|null
     */
    public static function pathsFromOptions(array $options): ?array
    {
        if (! isset($options['model_paths']) || ! is_array($options['model_paths'])) {
            return null;
        }

        $paths = [];
        foreach ($options['model_paths'] as $path) {
            if (is_string($path)) {
                $paths[] = $path;
            }
        }

        return $paths;
    }

    /**
     * The model's `$dispatchesEvents` map (model event => event class),
     * read without instantiating the model.
     *
     * @param  class-string<Model>  $model
     *
     * @return array<string, string>
     */
    public static function dispatchedEvents(string $model): array
    {
        $defaults = (new ReflectionClass($model))->getDefaultProperties();
        $map = $defaults['dispatchesEvents'] ?? [];

        if (! is_array($map)) {
            return [];
        }

        $events = [];
        foreach ($map as $modelEvent => $event) {
            if (is_string($modelEvent) && is_string($event)) {
                $events[$modelEvent] = $event;
            }
        }

        return $events;
    }
}
