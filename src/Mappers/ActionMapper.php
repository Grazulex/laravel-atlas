<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Support\Facades\File;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;
use ReflectionMethod;

class ActionMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'actions';
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function scan(array $options = []): array
    {
        $actions = [];
        $paths = $options['paths'] ?? [app_path('Actions')];
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
                    $reflection = new ReflectionClass($fqcn);
                    if (! $reflection->isAbstract() && ! $reflection->isInterface()) {
                        $actions[] = $this->analyzeAction($fqcn);
                        $seen[$fqcn] = true;
                    }
                }
            }
        }

        return [
            'type' => $this->type(),
            'count' => count($actions),
            'data' => $actions,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeAction(string $fqcn): array
    {
        $reflection = new ReflectionClass($fqcn);

        return [
            'class' => $fqcn,
            'namespace' => $reflection->getNamespaceName(),
            'name' => $reflection->getShortName(),
            'file' => $reflection->getFileName(),
            'methods' => $this->getPublicMethods($reflection),
            'constructor' => $this->getConstructorInfo($reflection),
            'dependencies' => $this->getDependencies($reflection),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getPublicMethods(ReflectionClass $reflection): array
    {
        $methods = [];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class === $reflection->getName() && ! $method->isConstructor()) {
                $methods[] = [
                    'name' => $method->getName(),
                    'parameters' => array_map(
                        fn ($p) => [
                            'name' => $p->getName(),
                            'type' => $p->getType() ? $p->getType()->__toString() : null,
                            'default' => $p->isDefaultValueAvailable() ? $p->getDefaultValue() : null,
                        ],
                        $method->getParameters()
                    ),
                    'return_type' => $method->getReturnType() ? $method->getReturnType()->__toString() : null,
                ];
            }
        }

        return $methods;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getConstructorInfo(ReflectionClass $reflection): ?array
    {
        $constructor = $reflection->getConstructor();
        
        if (! $constructor) {
            return null;
        }

        return [
            'parameters' => array_map(
                fn ($p) => [
                    'name' => $p->getName(),
                    'type' => $p->getType() ? $p->getType()->__toString() : null,
                ],
                $constructor->getParameters()
            ),
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function getDependencies(ReflectionClass $reflection): array
    {
        $constructor = $reflection->getConstructor();
        
        if (! $constructor) {
            return [];
        }

        $dependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            if ($type && ! $type->isBuiltin()) {
                $dependencies[] = $type->__toString();
            }
        }

        return $dependencies;
    }
}
