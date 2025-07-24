<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Support\Facades\File;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;
use ReflectionMethod;

class ObserverMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'observers';
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function scan(array $options = []): array
    {
        $observers = [];
        $paths = $options['paths'] ?? [app_path('Observers')];
        $recursive = $options['recursive'] ?? true;
        $seen = [];

        foreach ($paths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            $files = $recursive ? File::allFiles($path) : File::files($path);

            foreach ($files as $file) {
                $fqcn = ClassResolver::resolveFromPath($file->getRealPath());

                if (
                    $fqcn &&
                    class_exists($fqcn) &&
                    ! isset($seen[$fqcn])
                ) {
                    $seen[$fqcn] = true;
                    $observers[] = $this->analyzeObserver($fqcn, $file->getRealPath());
                }
            }
        }

        return [
            'type' => $this->type(),
            'count' => count($observers),
            'data' => $observers,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeObserver(string $fqcn, string $filePath): array
    {
        $reflection = new ReflectionClass($fqcn);

        return [
            'class' => $fqcn,
            'file' => $filePath,
            'namespace' => $reflection->getNamespaceName(),
            'name' => $reflection->getShortName(),
            'methods' => $this->extractMethods($reflection),
            'model_events' => $this->detectModelEvents($reflection),
            'model' => $this->guessModel($reflection),
            'is_abstract' => $reflection->isAbstract(),
            'is_final' => $reflection->isFinal(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function extractMethods(ReflectionClass $reflection): array
    {
        $methods = [];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class === $reflection->getName() && !$method->isConstructor()) {
                $methods[] = [
                    'name' => $method->getName(),
                    'parameters' => array_map(
                        fn ($param) => [
                            'name' => $param->getName(),
                            'type' => $param->getType()?->__toString(),
                            'has_default' => $param->isDefaultValueAvailable(),
                        ],
                        $method->getParameters()
                    ),
                    'return_type' => $method->getReturnType()?->__toString(),
                    'is_static' => $method->isStatic(),
                ];
            }
        }

        return $methods;
    }

    /**
     * @return array<int, string>
     */
    protected function detectModelEvents(ReflectionClass $reflection): array
    {
        $modelEvents = [
            'retrieved', 'creating', 'created', 'updating', 'updated',
            'saving', 'saved', 'deleting', 'deleted', 'restoring', 'restored'
        ];

        $foundEvents = [];

        foreach ($modelEvents as $event) {
            if ($reflection->hasMethod($event)) {
                $foundEvents[] = $event;
            }
        }

        return $foundEvents;
    }

    protected function guessModel(ReflectionClass $reflection): ?string
    {
        $observerName = $reflection->getShortName();
        
        if (str_ends_with($observerName, 'Observer')) {
            $modelName = substr($observerName, 0, -8); // Remove 'Observer' suffix
            $modelClass = "App\\Models\\{$modelName}";
            
            if (class_exists($modelClass)) {
                return $modelClass;
            }
        }
        
        return null;
    }
}
