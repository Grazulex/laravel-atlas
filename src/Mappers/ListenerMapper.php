<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;
use ReflectionMethod;

class ListenerMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'listeners';
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @return array<string, mixed>
     */
    public function scan(array $options = []): array
    {
        $listeners = [];
        $defaultPaths = [app_path('Listeners')];

        // Ajouter le beta_app s'il existe
        $betaAppPath = base_path('beta_app/app/Listeners');
        if (is_dir($betaAppPath)) {
            $defaultPaths[] = $betaAppPath;
        }

        $paths = $options['paths'] ?? $defaultPaths;
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
                    $listeners[] = $this->analyzeListener($fqcn, $file->getRealPath());
                }
            }
        }

        return [
            'type' => $this->type(),
            'count' => count($listeners),
            'data' => $listeners,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeListener(string $fqcn, string $filePath): array
    {
        if (! class_exists($fqcn)) {
            throw new InvalidArgumentException("Class {$fqcn} does not exist");
        }

        $reflection = new ReflectionClass($fqcn);

        return [
            'class' => $fqcn,
            'file' => $filePath,
            'namespace' => $reflection->getNamespaceName(),
            'name' => $reflection->getShortName(),
            'methods' => $this->extractMethods($reflection),
            'handle_method' => $this->hasHandleMethod($reflection),
            'queued' => $this->isQueued($reflection),
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
            if ($method->class === $reflection->getName() && ! $method->isConstructor()) {
                $methods[] = [
                    'name' => $method->getName(),
                    'parameters' => array_map(
                        fn ($param): array => [
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

    protected function hasHandleMethod(ReflectionClass $reflection): bool
    {
        return $reflection->hasMethod('handle');
    }

    protected function isQueued(ReflectionClass $reflection): bool
    {
        return in_array(ShouldQueue::class, class_implements($reflection->getName()) ?: []);
    }
}
