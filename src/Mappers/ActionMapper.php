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
        $defaultPaths = [app_path('Actions')];
        
        // Ajouter le beta_app s'il existe
        $betaAppPath = base_path('beta_app/app/Actions');
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
                foreach ($files as $file) {
                $fqcn = $this->resolveClassFromFile($file->getRealPath());

                if (
                    $fqcn &&
                    ! isset($seen[$fqcn])
                ) {
                    $seen[$fqcn] = true;
                    $actions[] = $this->analyzeAction($fqcn, $file->getRealPath());
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
        if (!file_exists($filePath)) {
            return null;
        }

        // Lire le contenu du fichier pour extraire le namespace et le nom de classe
        $content = file_get_contents($filePath);
        if ($content === false) {
            return null;
        }

        // Extraire le namespace
        if (preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches)) {
            $namespace = trim($namespaceMatches[1]);
        } else {
            $namespace = '';
        }

        // Extraire le nom de classe
        if (preg_match('/class\s+(\w+)/', $content, $classMatches)) {
            $className = $classMatches[1];
        } else {
            return null;
        }

        // Construire le FQCN
        $fqcn = $namespace ? $namespace . '\\' . $className : $className;

        // Essayer de charger le fichier
        try {
            require_once $filePath;
            if (class_exists($fqcn)) {
                return $fqcn;
            }
        } catch (\Throwable $e) {
            // Ignorer les erreurs de chargement
        }

        return null;
    }
}
