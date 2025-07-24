<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Support\Facades\File;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;
use ReflectionMethod;

class RuleMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'rules';
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function scan(array $options = []): array
    {
        $rules = [];
        $defaultPaths = [app_path('Rules')];
        
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
                    $this->implementsRule($fqcn) &&
                    ! isset($seen[$fqcn])
                ) {
                    $seen[$fqcn] = true;
                    $rules[] = $this->analyzeRule($fqcn, $file->getRealPath());
                }
            }
        }

        return [
            'type' => $this->type(),
            'count' => count($rules),
            'data' => $rules,
        ];
    }

    /**
     * Vérifier si un fichier est une rule en analysant son contenu
     */
    protected function isRuleFile(string $filePath): bool
    {
        if (!file_exists($filePath) || !str_ends_with($filePath, '.php')) {
            return false;
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            return false;
        }

        // Vérifier si la classe implémente l'une des interfaces de validation
        return str_contains($content, 'ValidationRule') || 
               str_contains($content, 'Illuminate\Contracts\Validation\Rule');
    }

    /**
     * Analyser un fichier de rule sans charger la classe
     */
    protected function analyzeRuleFile(string $filePath): ?array
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return null;
        }

        // Extraire le namespace
        $namespace = '';
        if (preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches)) {
            $namespace = trim($namespaceMatches[1]);
        }

        // Extraire le nom de classe
        $className = '';
        if (preg_match('/class\s+(\w+)/', $content, $classMatches)) {
            $className = $classMatches[1];
        }

        if (!$className) {
            return null;
        }

        $fqcn = $namespace ? $namespace . '\\' . $className : $className;

        // Extraire les méthodes publiques
        $methods = $this->extractMethodsFromContent($content);

        // Déterminer le type de rule
        $implements = [];
        if (str_contains($content, 'ValidationRule')) {
            $implements[] = 'Illuminate\Contracts\Validation\ValidationRule';
        }
        if (str_contains($content, 'Illuminate\Contracts\Validation\Rule')) {
            $implements[] = 'Illuminate\Contracts\Validation\Rule';
        }

        return [
            'class' => $fqcn,
            'file' => $filePath,
            'namespace' => $namespace,
            'name' => $className,
            'methods' => $methods,
            'implements' => $implements,
            'message_method' => str_contains($content, 'function message'),
            'is_abstract' => str_contains($content, 'abstract class'),
            'is_final' => str_contains($content, 'final class'),
        ];
    }

    /**
     * Extraire les méthodes d'un contenu de fichier
     */
    protected function extractMethodsFromContent(string $content): array
    {
        $methods = [];
        
        // Pattern pour trouver les méthodes publiques
        if (preg_match_all('/public\s+function\s+(\w+)\s*\([^)]*\)/', $content, $matches)) {
            foreach ($matches[1] as $methodName) {
                if ($methodName !== '__construct') {
                    $methods[] = [
                        'name' => $methodName,
                        'parameters' => [], // Simplifié pour éviter la complexité
                        'return_type' => null,
                        'is_static' => false,
                    ];
                }
            }
        }

        return $methods;
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

    protected function implementsRule(string $fqcn): bool
    {
        if (!class_exists($fqcn)) {
            return false;
        }

        try {
            $reflection = new ReflectionClass($fqcn);
            $interfaces = $reflection->getInterfaceNames();
            
            return in_array('Illuminate\Contracts\Validation\Rule', $interfaces) ||
                   in_array('Illuminate\Contracts\Validation\ValidationRule', $interfaces);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeRule(string $fqcn, string $filePath): array
    {
        $reflection = new ReflectionClass($fqcn);

        return [
            'class' => $fqcn,
            'file' => $filePath,
            'namespace' => $reflection->getNamespaceName(),
            'name' => $reflection->getShortName(),
            'methods' => $this->extractMethods($reflection),
            'implements' => $reflection->getInterfaceNames(),
            'message_method' => $this->hasMessageMethod($reflection),
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

    protected function hasMessageMethod(ReflectionClass $reflection): bool
    {
        return $reflection->hasMethod('message');
    }
}
