<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use ReflectionNamedType;
use SplFileInfo;

class RuleMapper extends BaseMapper
{
    public function getType(): string
    {
        return 'rules';
    }

    protected function getDefaultOptions(): array
    {
        return [
            'include_validation_method' => true,
            'include_message_method' => true,
            'include_parameters' => true,
            'include_dependencies' => true,
            'scan_path' => base_path('app/Rules'),
        ];
    }

    public function performScan(): Collection
    {
        $rules = collect();
        $scanPath = $this->config('scan_path', base_path('app/Rules'));

        if (! File::exists($scanPath)) {
            return $rules;
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

            // Check if it's a validation rule class
            if (! $this->looksLikeRuleClass($content)) {
                continue;
            }

            if (! class_exists($className)) {
                continue;
            }

            $reflectionClass = new ReflectionClass($className);

            $rules->put($className, [
                'name' => $reflectionClass->getShortName(),
                'full_name' => $reflectionClass->getName(),
                'file' => $file->getRealPath(),
                'namespace' => $reflectionClass->getNamespaceName(),
                'rule_type' => $this->detectRuleType($content),
                'validation_method' => $this->extractValidationMethod($content),
                'message_method' => $this->extractMessageMethod($content),
                'parameters' => $this->extractParameters($content),
                'dependencies' => $this->extractDependencies($reflectionClass),
                'line_count' => substr_count($content, "\n") + 1,
            ]);
        }

        return $rules;
    }

    private function extractClassName(string $content): ?string
    {
        if (preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatch) &&
            preg_match('/class\s+(\w+)/', $content, $classMatch)) {
            return $namespaceMatch[1] . '\\' . $classMatch[1];
        }

        return null;
    }

    private function looksLikeRuleClass(string $content): bool
    {
        // Check for rule patterns
        $rulePatterns = [
            'implements\s+Rule',
            'implements\s+InvokableRule',
            'implements\s+ValidationRule',
            'use\s+Illuminate\\\\Contracts\\\\Validation\\\\Rule',
            'use\s+Illuminate\\\\Contracts\\\\Validation\\\\InvokableRule',
            'use\s+Illuminate\\\\Contracts\\\\Validation\\\\ValidationRule',
            'function\s+(passes|validate|__invoke)',
            'function\s+message',
            'class\s+\w+Rule\s*\(?:implements|\{\)',
        ];

        foreach ($rulePatterns as $pattern) {
            if (preg_match('/' . $pattern . '/i', $content)) {
                return true;
            }
        }

        return false;
    }

    private function detectRuleType(string $content): string
    {
        if (str_contains($content, 'ValidationRule')) {
            return 'ValidationRule';
        }
        if (str_contains($content, 'InvokableRule')) {
            return 'InvokableRule';
        }
        if (str_contains($content, 'implements Rule')) {
            return 'Rule';
        }

        return 'unknown';
    }

    /**
     * @return array<string, mixed>|null
     */
    private function extractValidationMethod(string $content): ?array
    {
        // Look for passes method (Rule interface)
        if (preg_match('/function\s+passes\s*\([^)]*\)\s*\{([^}]+)\}/', $content, $matches)) {
            return [
                'method' => 'passes',
                'body' => trim($matches[1]),
            ];
        }

        // Look for validate method (ValidationRule interface)
        if (preg_match('/function\s+validate\s*\([^)]*\)\s*\{([^}]+)\}/', $content, $matches)) {
            return [
                'method' => 'validate',
                'body' => trim($matches[1]),
            ];
        }

        // Look for __invoke method (InvokableRule interface)
        if (preg_match('/function\s+__invoke\s*\([^)]*\)\s*\{([^}]+)\}/', $content, $matches)) {
            return [
                'method' => '__invoke',
                'body' => trim($matches[1]),
            ];
        }

        return null;
    }

    private function extractMessageMethod(string $content): ?string
    {
        // Look for message method
        if (preg_match('/function\s+message\s*\(\s*\)\s*\{([^}]+)\}/', $content, $matches)) {
            $messageBody = $matches[1];

            // Extract return statement
            if (preg_match('/return\s+[\'"]([^\'"]+)[\'"]/', $messageBody, $returnMatch)) {
                return $returnMatch[1];
            }
        }

        return null;
    }

    /**
     * @return list<array<string, string>>
     */
    private function extractParameters(string $content): array
    {
        $parameters = [];

        // Look for constructor parameters
        if (preg_match('/function\s+__construct\s*\(([^)]+)\)/', $content, $matches)) {
            $params = $matches[1];

            if (preg_match_all('/(\w+)\s+\$(\w+)/', $params, $paramMatches, PREG_SET_ORDER)) {
                foreach ($paramMatches as $match) {
                    $parameters[] = [
                        'name' => $match[2],
                        'type' => $match[1],
                    ];
                }
            }
        }

        return $parameters;
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
