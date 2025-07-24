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
        $paths = $options['paths'] ?? [app_path('Rules')];
        $recursive = $options['recursive'] ?? true;
        $seen = [];

        foreach ($paths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            $files = $recursive ? File::allFiles($path) : File::files($path);

            foreach ($files as $file) {
                // Debug: afficher tous les fichiers trouvés
                echo "Checking file: " . $file->getRealPath() . "\n";
                
                $fqcn = ClassResolver::resolveFromPath($file->getRealPath());
                echo "Resolved FQCN: " . ($fqcn ?: 'NULL') . "\n";
                
                if ($fqcn) {
                    echo "Class exists: " . (class_exists($fqcn) ? 'YES' : 'NO') . "\n";
                    if (class_exists($fqcn)) {
                        echo "Implements rule: " . ($this->implementsRule($fqcn) ? 'YES' : 'NO') . "\n";
                    }
                }
                echo "---\n";

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
            'message_method' => $reflection->hasMethod('message'),
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
}
