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
            'constructor_parameters' => $this->getConstructorParameters($reflection),
            'implements' => $reflection->getInterfaceNames(),
            'message_method' => $reflection->hasMethod('message'),
            'is_abstract' => $reflection->isAbstract(),
            'is_final' => $reflection->isFinal(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getConstructorParameters(ReflectionClass $reflection): array
    {
        if (!$reflection->hasMethod('__construct')) {
            return [];
        }

        $constructor = $reflection->getMethod('__construct');
        $parameters = [];

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            $typeName = null;
            
            if ($type) {
                if (method_exists($type, '__toString')) {
                    $typeName = (string) $type;
                }
            }

            $parameters[] = [
                'name' => $parameter->getName(),
                'type' => $typeName,
                'nullable' => $parameter->allowsNull(),
                'has_default' => $parameter->isDefaultValueAvailable(),
                'default_value' => $parameter->isDefaultValueAvailable() ? 
                    $this->getDefaultValueDisplay($parameter) : null,
            ];
        }

        return $parameters;
    }

    /**
     * Obtenir une représentation affichable de la valeur par défaut
     */
    protected function getDefaultValueDisplay(\ReflectionParameter $parameter): string
    {
        try {
            $default = $parameter->getDefaultValue();
            
            if ($default === null) {
                return 'null';
            } elseif (is_bool($default)) {
                return $default ? 'true' : 'false';
            } elseif (is_string($default)) {
                return '"' . $default . '"';
            } elseif (is_array($default)) {
                return '[]';
            } else {
                return (string) $default;
            }
        } catch (\ReflectionException $e) {
            return 'unknown';
        }
    }
}
