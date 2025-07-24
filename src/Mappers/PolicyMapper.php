<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Support\Facades\File;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;
use ReflectionMethod;

class PolicyMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'policies';
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function scan(array $options = []): array
    {
        $policies = [];
        $defaultPaths = [app_path('Policies')];
        
        // Ajouter le beta_app s'il existe
        $betaAppPath = base_path('beta_app/app/Policies');
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
                    $policies[] = $this->analyzePolicy($fqcn, $file->getRealPath());
                }
            }
        }

        return [
            'type' => $this->type(),
            'count' => count($policies),
            'data' => $policies,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzePolicy(string $fqcn, string $filePath): array
    {
        $reflection = new ReflectionClass($fqcn);

        return [
            'class' => $fqcn,
            'file' => $filePath,
            'namespace' => $reflection->getNamespaceName(),
            'name' => $reflection->getShortName(),
            'methods' => $this->extractMethods($reflection),
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

    protected function guessModel(ReflectionClass $reflection): ?string
    {
        $policyName = $reflection->getShortName();
        
        if (str_ends_with($policyName, 'Policy')) {
            $modelName = substr($policyName, 0, -6); // Remove 'Policy' suffix
            $modelClass = "App\\Models\\{$modelName}";
            
            if (class_exists($modelClass)) {
                return $modelClass;
            }
        }
        
        return null;
    }
}
