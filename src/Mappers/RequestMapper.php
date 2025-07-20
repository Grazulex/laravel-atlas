<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use ReflectionNamedType;
use SplFileInfo;

class RequestMapper extends BaseMapper
{
    public function getType(): string
    {
        return 'requests';
    }

    protected function getDefaultOptions(): array
    {
        return [
            'include_validation_rules' => true,
            'include_authorization' => true,
            'include_custom_messages' => true,
            'include_custom_attributes' => true,
            'include_dependencies' => true,
            'scan_path' => base_path('app/Http/Requests'),
        ];
    }

    public function performScan(): Collection
    {
        $requests = collect();
        $scanPath = $this->config('scan_path', base_path('app/Http/Requests'));

        if (! File::exists($scanPath)) {
            return $requests;
        }

        $files = File::allFiles($scanPath);

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $content = File::get($file->getRealPath());
            $className = $this->extractClassName($content);

            if (! $className) {
                continue;
            }

            // Check if it's a request class
            if (! str_contains($content, 'use Illuminate\Foundation\Http\FormRequest') &&
                ! str_contains($content, 'extends FormRequest')) {
                continue;
            }

            if (! class_exists($className)) {
                continue;
            }

            $reflectionClass = new ReflectionClass($className);

            $requests->put($className, [
                'name' => $reflectionClass->getShortName(),
                'full_name' => $reflectionClass->getName(),
                'file' => $file->getRealPath(),
                'namespace' => $reflectionClass->getNamespaceName(),
                'validation_rules' => $this->extractValidationRules($content),
                'authorization' => $this->extractAuthorization($content),
                'custom_messages' => $this->extractCustomMessages($content),
                'custom_attributes' => $this->extractCustomAttributes($content),
                'dependencies' => $this->extractDependencies($reflectionClass),
                'line_count' => substr_count($content, "\n") + 1,
            ]);
        }

        return $requests;
    }

    private function extractClassName(string $content): ?string
    {
        if (preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatch) &&
            preg_match('/class\s+(\w+)/', $content, $classMatch)) {
            return $namespaceMatch[1] . '\\' . $classMatch[1];
        }

        return null;
    }

    /**
     * @return list<array<string, array<string>|string>>
     */
    private function extractValidationRules(string $content): array
    {
        $rules = [];

        // Look for rules method
        if (preg_match('/function\s+rules\s*\(\s*\)\s*\{([^}]+)\}/', $content, $matches)) {
            $rulesBody = $matches[1];

            // Extract array rules
            if (preg_match('/return\s*\[([^\]]+)\]/', $rulesBody, $arrayMatches)) {
                $arrayContent = $arrayMatches[1];

                if (preg_match_all('/[\'"]([^\'";]+)[\'"]\s*=>\s*([^,\]]+)/', $arrayContent, $ruleMatches, PREG_SET_ORDER)) {
                    foreach ($ruleMatches as $match) {
                        $rules[] = [
                            'field' => $match[1],
                            'rules' => $this->parseValidationRule(trim($match[2], "'")),
                        ];
                    }
                }
            }
        }

        return $rules;
    }

    /**
     * @return array<string>
     */
    private function parseValidationRule(string $ruleString): array
    {
        // Handle array format ['required', 'string', 'max:255']
        if (str_starts_with($ruleString, '[')) {
            $ruleString = trim($ruleString, '[]');
            $rules = explode(',', $ruleString);

            return array_map(fn ($rule): string => trim($rule, " '\""), $rules);
        }

        // Handle string format 'required|string|max:255'
        if (str_contains($ruleString, '|')) {
            return explode('|', $ruleString);
        }

        return [$ruleString];
    }

    /**
     * @return array<string, mixed>
     */
    private function extractAuthorization(string $content): array
    {
        $authorization = [
            'has_authorize_method' => false,
            'authorization_logic' => null,
        ];

        // Look for authorize method
        if (preg_match('/function\s+authorize\s*\(\s*\)\s*\{([^}]+)\}/', $content, $matches)) {
            $authorization['has_authorize_method'] = true;
            $authBody = $matches[1];

            // Extract return statement
            if (preg_match('/return\s+([^;]+)/', $authBody, $returnMatches)) {
                $authorization['authorization_logic'] = trim($returnMatches[1]);
            }
        }

        return $authorization;
    }

    /**
     * @return list<array<string, string>>
     */
    private function extractCustomMessages(string $content): array
    {
        $messages = [];

        // Look for messages method
        if (preg_match('/function\s+messages\s*\(\s*\)\s*\{([^}]+)\}/', $content, $matches)) {
            $messagesBody = $matches[1];

            if (preg_match_all('/[\'"]([^\'";]+)[\'"]\s*=>\s*[\'"]([^\'"]+)[\'"]/', $messagesBody, $msgMatches, PREG_SET_ORDER)) {
                foreach ($msgMatches as $match) {
                    $messages[] = [
                        'rule' => $match[1],
                        'message' => $match[2],
                    ];
                }
            }
        }

        return $messages;
    }

    /**
     * @return list<array<string, string>>
     */
    private function extractCustomAttributes(string $content): array
    {
        $attributes = [];

        // Look for attributes method
        if (preg_match('/function\s+attributes\s*\(\s*\)\s*\{([^}]+)\}/', $content, $matches)) {
            $attributesBody = $matches[1];

            if (preg_match_all('/[\'"]([^\'";]+)[\'"]\s*=>\s*[\'"]([^\'"]+)[\'"]/', $attributesBody, $attrMatches, PREG_SET_ORDER)) {
                foreach ($attrMatches as $match) {
                    $attributes[] = [
                        'field' => $match[1],
                        'label' => $match[2],
                    ];
                }
            }
        }

        return $attributes;
    }

    /**
     * @return array<string>
     */
    private function extractDependencies(ReflectionClass $reflection): array
    {
        $dependencies = [];

        // Check constructor dependencies
        $constructor = $reflection->getConstructor();
        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                $type = $parameter->getType();
                if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
                    $dependencies[] = $type->getName();
                }
            }
        }

        return $dependencies;
    }
}
