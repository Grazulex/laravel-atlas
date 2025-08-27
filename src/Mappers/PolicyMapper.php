<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Support\Facades\File;
use InvalidArgumentException;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use Throwable;

class PolicyMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'policies';
    }

    /**
     * @param  array<string, mixed>  $options
     *
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
                $fqcn = $this->resolveClassFromFile($file->getRealPath());

                if (
                    $fqcn &&
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
            if ($method->class === $reflection->getName() && ! $method->isConstructor()) {
                $methods[] = [
                    'name' => $method->getName(),
                    'parameters' => array_map(
                        fn (ReflectionParameter $param): array => [
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

    /**
     * Résoudre le nom de classe à partir d'un fichier
     */
    protected function resolveClassFromFile(string $filePath): ?string
    {
        // D'abord essayer avec ClassResolver
        $fqcn = ClassResolver::resolveFromPath($filePath);
        if ($fqcn && class_exists($fqcn)) {
            return $fqcn;
        }

        // Si ça ne fonctionne pas, essayer de charger manuellement le fichier
        if (! file_exists($filePath)) {
            return null;
        }

        // Lire le contenu du fichier pour extraire le namespace et le nom de classe
        $content = file_get_contents($filePath);
        if ($content === false) {
            return null;
        }

        // Extraire le namespace
        $namespace = preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches) ? trim($namespaceMatches[1]) : '';

        // Extraire le nom de classe
        if (preg_match('/class\s+(\w+)/', $content, $classMatches)) {
            $className = $classMatches[1];
        } else {
            return null;
        }

        // Construire le FQCN
        $fqcn = $namespace !== '' && $namespace !== '0' ? $namespace . '\\' . $className : $className;

        // Essayer de charger le fichier
        try {
            require_once $filePath;
            if (class_exists($fqcn)) {
                return $fqcn;
            }
        } catch (Throwable) {
            // Ignorer les erreurs de chargement
        }

        return null;
    }
}
