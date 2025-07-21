<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Override;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use SplFileInfo;

class ModelMapper extends BaseMapper
{
    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'models';
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    protected function getDefaultOptions(): array
    {
        return [
            'include_relationships' => true,
            'include_observers' => true,
            'include_factories' => true,
            'include_attributes' => true,
            'include_scopes' => true,
            'scan_path' => app_path('Models'),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return Collection<string, array<string, mixed>>
     */
    protected function performScan(): Collection
    {
        $results = collect();
        $modelPath = $this->config('scan_path', app_path('Models'));

        if (! is_string($modelPath) || ! File::isDirectory($modelPath)) {
            return $results;
        }

        $modelFiles = File::allFiles($modelPath);

        foreach ($modelFiles as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $modelData = $this->analyzeModelFile($file);
            if ($modelData !== null) {
                $results->put($modelData['class_name'], $modelData);
            }
        }

        /** @var Collection<string, array<string, mixed>> $results */
        return $results;
    }

    /**
     * Analyze a single model file
     *
     * @return array<string, mixed>|null
     */
    protected function analyzeModelFile(SplFileInfo $file): ?array
    {
        $content = File::get($file->getRealPath());
        $className = $this->extractClassName($content, $file);

        if (! $className || ! class_exists($className) || ! is_subclass_of($className, Model::class)) {
            return null;
        }

        try {
            $reflection = new ReflectionClass($className);
            $parentClass = $reflection->getParentClass();
            $modelData = [
                'class_name' => $className,
                'file_path' => $file->getRealPath(),
                'namespace' => $reflection->getNamespaceName(),
                'short_name' => $reflection->getShortName(),
                'is_abstract' => $reflection->isAbstract(),
                'parent_class' => $parentClass ? $parentClass->getName() : null,
                'traits' => array_keys($reflection->getTraits()),
                'interfaces' => $reflection->getInterfaceNames(),
            ];

            // Add relationships if enabled
            if ($this->config('include_relationships')) {
                $modelData['relationships'] = $this->extractRelationships($reflection);
            }

            // Add attributes if enabled
            if ($this->config('include_attributes')) {
                $modelData['attributes'] = $this->extractAttributes($className);
            }

            // Add scopes if enabled
            if ($this->config('include_scopes')) {
                $modelData['scopes'] = $this->extractScopes($reflection);
            }

            // Add observers if enabled
            if ($this->config('include_observers')) {
                $modelData['observers'] = $this->extractObservers($className);
            }

            return $modelData;
        } catch (Exception) {
            // Skip models that can't be analyzed
            return null;
        }
    }

    /**
     * Extract class name from file content
     */
    protected function extractClassName(string $content, SplFileInfo $file): ?string
    {
        // Extract namespace
        preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches);
        $namespace = $namespaceMatches[1] ?? '';

        // Extract class name
        preg_match('/class\s+(\w+)/', $content, $classMatches);
        $className = $classMatches[1] ?? '';

        if ($className === '' || $className === '0') {
            return null;
        }

        return $namespace !== '' && $namespace !== '0' ? $namespace . '\\' . $className : $className;
    }

    /**
     * Extract model relationships
     *
     * @param  ReflectionClass<object>  $reflection
     *
     * @return array<string, array<string, mixed>>
     */
    protected function extractRelationships(ReflectionClass $reflection): array
    {
        $relationships = [];
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if ($method->getDeclaringClass()->getName() !== $reflection->getName()) {
                continue;
            }

            $returnType = $method->getReturnType();
            if (! $returnType) {
                continue;
            }
            if (! ($returnType instanceof ReflectionNamedType)) {
                continue;
            }

            $returnTypeName = $returnType->getName();
            if (is_subclass_of($returnTypeName, Relation::class)) {
                $relationships[$method->getName()] = [
                    'type' => class_basename($returnTypeName),
                    'method' => $method->getName(),
                ];
            }
        }

        return $relationships;
    }

    /**
     * Extract model attributes (fillable, guarded, etc.)
     *
     * @return array<string, mixed>
     */
    protected function extractAttributes(string $className): array
    {
        /** @var Model $model */
        $model = new $className;

        return [
            'fillable' => $model->getFillable(),
            'guarded' => $model->getGuarded(),
            'hidden' => $model->getHidden(),
            'visible' => $model->getVisible(),
            'casts' => $model->getCasts(),
            'dates' => method_exists($model, 'getDates') ? $model->getDates() : [],
            'table' => $model->getTable(),
            'primary_key' => $model->getKeyName(),
            'incrementing' => $model->getIncrementing(),
            'timestamps' => $model->usesTimestamps(),
        ];
    }

    /**
     * Extract model scopes
     *
     * @param  ReflectionClass<object>  $reflection
     *
     * @return array<int, array<string, mixed>>
     */
    protected function extractScopes(ReflectionClass $reflection): array
    {
        $scopes = [];
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $methodName = $method->getName();
            if (str_starts_with($methodName, 'scope') && $methodName !== 'scope') {
                $scopeName = lcfirst(substr($methodName, 5));
                $scopes[] = [
                    'name' => $scopeName,
                    'method' => $methodName,
                    'parameters' => array_map(
                        fn ($param): string => $param->getName(),
                        $method->getParameters()
                    ),
                ];
            }
        }

        return $scopes;
    }

    /**
     * Extract model observers (placeholder - would need more complex logic)
     *
     * @return array<string, mixed>
     */
    protected function extractObservers(string $className): array
    {
        // This is a placeholder - extracting observers would require
        // analyzing the EventServiceProvider or using more complex reflection
        return [];
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    #[Override]
    protected function getSummary(): array
    {
        $summary = parent::getSummary();

        $relationshipCount = 0;
        foreach ($this->results as $model) {
            if (is_array($model) && isset($model['relationships'])) {
                $relationshipCount += count($model['relationships']);
            }
        }

        $summary['relationships_count'] = $relationshipCount;
        $summary['models_with_relationships'] = $this->results->filter(
            fn ($model): bool => is_array($model) && ! empty($model['relationships'])
        )->count();

        return $summary;
    }
}
