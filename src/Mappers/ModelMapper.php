<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

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
        $reflection = new ReflectionClass($model);

        return [
            'class' => $model::class,
            'namespace' => $reflection->getNamespaceName(),
            'name' => $reflection->getShortName(),
            'file' => $reflection->getFileName() ?: 'N/A',
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
        $reflection = new ReflectionClass($model);
        $fileName = $reflection->getFileName();

        if ($fileName === false) {
            return $relations;
        }

        $source = file_get_contents($fileName);

        if ($source === false) {
            return $relations;
        }

        // List of Laravel relation types to detect
        $relationTypes = [
            'hasOne',
            'hasMany',
            'belongsTo',
            'belongsToMany',
            'hasOneThrough',
            'hasManyThrough',
            'morphOne',
            'morphMany',
            'morphTo',
            'morphToMany',
            'morphedByMany',
        ];

        $relationPattern = implode('|', $relationTypes);

        // Pattern to match relation methods:
        // public function methodName() { return $this->relationType(RelatedModel::class, ...); }
        // Also handles: return $this->relationType(RelatedModel::class);
        $pattern = '/public\s+function\s+(\w+)\s*\([^)]*\)\s*(?::\s*[\w\\\\]+)?\s*\{[^}]*return\s+\$this->(' . $relationPattern . ')\s*\(\s*([A-Z][\w\\\\]+)::class/s';

        if (preg_match_all($pattern, $source, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $methodName = $match[1];
                $relationType = $match[2];
                $relatedClass = $match[3];

                // Resolve full class name if it's a short name
                $relatedFqcn = $this->resolveRelatedClass($relatedClass, $source);

                $relations[$methodName] = [
                    'type' => ucfirst($relationType),
                    'related' => $relatedFqcn,
                    'foreignKey' => null, // Cannot determine without execution
                ];
            }
        }

        return $relations;
    }

    /**
     * Resolve the fully qualified class name for a related model.
     */
    protected function resolveRelatedClass(string $className, string $source): string
    {
        // If already fully qualified
        if (str_starts_with($className, '\\')) {
            return ltrim($className, '\\');
        }

        // Check for use statement
        if (preg_match('/use\s+([\w\\\\]+\\\\' . preg_quote($className, '/') . ')\s*;/', $source, $match)) {
            return $match[1];
        }

        // Check for use statement with alias
        if (preg_match('/use\s+([\w\\\\]+)\s+as\s+' . preg_quote($className, '/') . '\s*;/', $source, $match)) {
            return $match[1];
        }

        // Extract namespace from source
        if (preg_match('/namespace\s+([\w\\\\]+)\s*;/', $source, $match)) {
            $namespace = $match[1];

            // Assume same namespace
            return $namespace . '\\' . $className;
        }

        // Default to App\Models namespace (common Laravel convention)
        return 'App\\Models\\' . $className;
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
                    'parameters' => array_map(fn (ReflectionParameter $p): string => '$' . $p->getName(), $method->getParameters()),
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

    /**
     * @return array<string, mixed>
     */
    protected function analyzeFlow(Model $model): array
    {
        $flow = [
            'jobs' => [],
            'events' => [],
            'observers' => [],
            'dependencies' => [],
        ];

        $reflection = new ReflectionClass($model);
        $fileName = $reflection->getFileName();

        if ($fileName === false) {
            return $flow;
        }

        $source = file_get_contents($fileName);

        if ($source === false) {
            return $flow;
        }

        // Detect dispatched jobs
        if (preg_match_all('/dispatch(?:Now)?\(\s*([A-Z][\w\\\\]+)::class/', $source, $matches)) {
            foreach ($matches[1] as $fqcn) {
                $flow['jobs'][] = [
                    'class' => $fqcn,
                    'async' => ! str_contains($source, "dispatchNow({$fqcn}"),
                ];
            }
        }

        // Detect events
        if (preg_match_all('/event\(\s*([A-Z][\w\\\\]+)::class/', $source, $matches)) {
            foreach ($matches[1] as $fqcn) {
                $flow['events'][] = ['class' => $fqcn];
            }
        }

        // Detect dependencies (new X / X::)
        if (preg_match_all('/new\s+([A-Z][\w\\\\]+)|([A-Z][\w\\\\]+)::/', $source, $matches)) {
            $found = array_filter(array_merge($matches[1], $matches[2]));
            $flow['dependencies'] = array_values(array_unique(array_filter($found)));
        }

        // Detect model-level observers (static::observe(...))
        if (preg_match_all('/static::observe\(\s*([A-Z][\w\\\\]+)::class/', $source, $matches)) {
            foreach ($matches[1] as $fqcn) {
                $flow['observers'][] = $fqcn;
            }
        }

        // Add global observers (declared in Service Providers)
        $global = $this->extractGlobalObservers();
        $class = $model::class;
        $shortClass = class_basename($class);

        // Check both full class name and short class name
        $globalObservers = [];
        if (isset($global[$class])) {
            $globalObservers = array_merge($globalObservers, $global[$class]);
        }
        if (isset($global[$shortClass])) {
            $globalObservers = array_merge($globalObservers, $global[$shortClass]);
        }

        if ($globalObservers !== []) {
            $flow['observers'] = array_values(array_unique(array_merge(
                $flow['observers'],
                $globalObservers
            )));
        }

        return $flow;
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function extractGlobalObservers(): array
    {
        $observers = [];

        $providerFiles = glob(app_path('Providers/*.php'));

        if ($providerFiles === false) {
            return $observers;
        }

        foreach ($providerFiles as $file) {
            $content = file_get_contents($file);

            if ($content === false) {
                continue;
            }

            // Pattern: Model::observe(Observer::class)
            if (preg_match_all('/([A-Z][\w\\\\]+)::observe\(\s*([A-Z][\w\\\\]+)::class/', $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    [$_, $model, $observer] = $match;
                    // Le premier groupe est le modèle, le second est l'observer
                    $observers[$model][] = $observer;

                    // Debug: ajouter aussi le nom complet si c'est un nom court
                    if (! str_contains($model, '\\')) {
                        $fullModelName = 'App\\Models\\' . $model;
                        $observers[$fullModelName][] = $observer;
                    }
                }
            }
        }

        return $observers;
    }
}
