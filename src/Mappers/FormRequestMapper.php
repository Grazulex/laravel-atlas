<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use ReflectionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\File;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;
use ReflectionMethod;

class FormRequestMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'form_requests';
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @return array<string, mixed>
     */
    public function scan(array $options = []): array
    {
        $formRequests = [];

        $paths = $options['paths'] ?? [app_path('Http/Requests')];

        foreach ($paths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            $files = File::allFiles($path);

            foreach ($files as $file) {
                $fqcn = $this->resolveClassFromFile($file->getRealPath());

                if ($fqcn && class_exists($fqcn) && is_subclass_of($fqcn, FormRequest::class)) {
                    $formRequests[] = $this->analyzeFormRequest($fqcn, $file->getPathname());
                }
            }
        }

        return [
            'type' => $this->type(),
            'count' => count($formRequests),
            'data' => $formRequests,
        ];
    }

    protected function resolveClassFromFile(string $path): ?string
    {
        return ClassResolver::resolveFromPath($path);
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeFormRequest(string $fqcn, string $filePath): array
    {
        if (! class_exists($fqcn)) {
            return [
                'class' => $fqcn,
                'rules' => [],
                'authorization' => null,
                'attributes' => [],
                'messages' => [],
                'methods' => [],
                'flow' => [],
            ];
        }

        $reflection = new ReflectionClass($fqcn);
        $source = file_get_contents($filePath);

        if ($source === false) {
            $source = null;
        }

        return [
            'class' => $fqcn,
            'rules' => $this->extractRules($source),
            'authorization' => $this->extractAuthorization($source),
            'attributes' => $this->extractAttributes($source),
            'messages' => $this->extractMessages($source),
            'methods' => $this->extractMethods($reflection),
            'flow' => $this->analyzeFlow($source),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function extractRules(?string $source): array
    {
        if (! $source) {
            return [];
        }

        $rules = [];

        // Extract rules method content
        if (preg_match('/function\s+rules\s*\(\s*\)\s*:\s*array\s*\{(.+?)\}/s', $source, $match)) {
            $rulesContent = $match[1];

            // Extract return array content
            if (preg_match('/return\s*\[(.+?)\];/s', $rulesContent, $returnMatch)) {
                $arrayContent = $returnMatch[1];

                // Parse field rules
                if (preg_match_all('/[\'"]([^\'"]+)[\'"]\s*=>\s*\[(.+?)\]/s', $arrayContent, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $field = $match[1];
                        $fieldRules = $match[2];

                        // Extract individual rules
                        $parsedRules = [];
                        if (preg_match_all('/[\'"]([^\'"]+)[\'"]/', $fieldRules, $ruleMatches)) {
                            $parsedRules = array_merge($parsedRules, $ruleMatches[1]);
                        }

                        // Extract Rule:: patterns
                        if (preg_match_all('/Rule::(\w+)\([^)]*\)/', $fieldRules, $ruleClassMatches)) {
                            foreach ($ruleClassMatches[0] as $ruleClass) {
                                $parsedRules[] = $ruleClass;
                            }
                        }

                        // Extract custom rule classes (new SomeRule())
                        if (preg_match_all('/new\s+([A-Z]\w+)\([^)]*\)/', $fieldRules, $customRuleMatches)) {
                            foreach ($customRuleMatches[1] as $customRule) {
                                $parsedRules[] = "new {$customRule}()";
                            }
                        }

                        $rules[$field] = $parsedRules;
                    }
                }
            }
        }

        return $rules;
    }

    /**
     * @return array<string, mixed>
     */
    protected function extractAuthorization(?string $source): ?array
    {
        if (! $source) {
            return null;
        }

        if (preg_match('/function\s+authorize\s*\(\s*\)\s*:\s*bool\s*\{(.+?)\}/s', $source, $match)) {
            $authContent = trim($match[1]);

            // Check for return statement
            if (preg_match('/return\s+(.+?);/', $authContent, $returnMatch)) {
                $returnStatement = trim($returnMatch[1]);

                return [
                    'method_exists' => true,
                    'return_statement' => $returnStatement,
                    'uses_auth' => str_contains($returnStatement, 'Auth::'),
                    'uses_can' => str_contains($returnStatement, '->can('),
                    'always_true' => $returnStatement === 'true',
                    'always_false' => $returnStatement === 'false',
                ];
            }
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    protected function extractAttributes(?string $source): array
    {
        if (! $source) {
            return [];
        }

        $attributes = [];

        if (preg_match('/function\s+attributes\s*\(\s*\)\s*:\s*array\s*\{(.+?)\}/s', $source, $match)) {
            $attributesContent = $match[1];

            if (preg_match('/return\s*\[(.+?)\];/s', $attributesContent, $returnMatch)) {
                $arrayContent = $returnMatch[1];

                if (preg_match_all('/[\'"]([^\'"]+)[\'"]\s*=>\s*[\'"]([^\'"]+)[\'"]/', $arrayContent, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $attributes[$match[1]] = $match[2];
                    }
                }
            }
        }

        return $attributes;
    }

    /**
     * @return array<string, string>
     */
    protected function extractMessages(?string $source): array
    {
        if (! $source) {
            return [];
        }

        $messages = [];

        if (preg_match('/function\s+messages\s*\(\s*\)\s*:\s*array\s*\{(.+?)\}/s', $source, $match)) {
            $messagesContent = $match[1];

            if (preg_match('/return\s*\[(.+?)\];/s', $messagesContent, $returnMatch)) {
                $arrayContent = $returnMatch[1];

                if (preg_match_all('/[\'"]([^\'"]+)[\'"]\s*=>\s*[\'"]([^\'"]*)[\'"]/', $arrayContent, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $messages[$match[1]] = $match[2];
                    }
                }
            }
        }

        return $messages;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function extractMethods(ReflectionClass $reflection): array
    {
        $methods = [];
        $importantMethods = ['authorize', 'rules', 'attributes', 'messages', 'withValidator', 'prepareForValidation'];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED) as $method) {
            if ($method->class !== $reflection->getName()) {
                continue; // Ignore inherited methods
            }

            $isImportant = in_array($method->getName(), $importantMethods);

            $methods[] = [
                'name' => $method->getName(),
                'visibility' => $method->isPublic() ? 'public' : ($method->isProtected() ? 'protected' : 'private'),
                'is_important' => $isImportant,
                'return_type' => $method->getReturnType() instanceof ReflectionType ? (string) $method->getReturnType() : null,
                'parameters' => array_map(
                    fn ($param): array => [
                        'name' => '$' . $param->getName(),
                        'type' => $param->getType() instanceof ReflectionType ? (string) $param->getType() : null,
                    ],
                    $method->getParameters()
                ),
            ];
        }

        return $methods;
    }

    /**
     * @return array<string, array<int, mixed>|array<int, string>>
     */
    protected function analyzeFlow(?string $source): array
    {
        if (! $source) {
            return [];
        }

        $flow = [
            'uses' => [],
            'models' => [],
            'rules' => [],
            'facades' => [],
            'policies' => [],
            'dependencies' => [],
            'validations' => [],
        ];

        // Extract use statements
        if (preg_match_all('/use\s+([A-Z][^;]+);/', $source, $matches)) {
            $flow['uses'] = array_unique($matches[1]);
        }

        // Extract model references
        if (preg_match_all('/([A-Z]\w+)::class/', $source, $matches)) {
            foreach ($matches[1] as $class) {
                if (str_contains($class, 'Model') || in_array($class, ['User', 'Post', 'Category'])) {
                    $flow['models'][] = $class;
                }
            }
            $flow['models'] = array_unique($flow['models']);
        }

        // Extract custom rule classes
        if (preg_match_all('/new\s+([A-Z]\w+Rule)\(/', $source, $matches)) {
            $flow['rules'] = array_unique($matches[1]);
        }

        // Extract facade usage
        if (preg_match_all('/([A-Z]\w+)::(?:check|user|id|can)/', $source, $matches)) {
            foreach ($matches[1] as $facade) {
                if (in_array($facade, ['Auth', 'Rule', 'Password'])) {
                    $flow['facades'][] = $facade;
                }
            }
            $flow['facades'] = array_unique($flow['facades']);
        }

        // Extract policy usage
        if (preg_match_all('/->can\([\'"](\w+)[\'"]/', $source, $matches)) {
            $flow['policies'] = array_unique($matches[1]);
        }

        // Extract validation types
        $validationTypes = [];
        if (preg_match_all('/[\'"](\w+(?:\.\w+)*)[\'"]/', $source, $matches)) {
            foreach ($matches[1] as $rule) {
                if (in_array($rule, ['required', 'string', 'email', 'unique', 'exists', 'confirmed', 'nullable', 'array', 'boolean', 'date', 'url', 'regex', 'min', 'max', 'after', 'before'])) {
                    $validationTypes[] = $rule;
                }
            }
        }
        $flow['validations'] = array_unique($validationTypes);

        return array_filter($flow, fn ($items): bool => ! empty($items));
    }
}
