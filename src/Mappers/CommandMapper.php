<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Override;
use ReflectionClass;
use ReflectionNamedType;
use SplFileInfo;

class CommandMapper extends BaseMapper
{
    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'commands';
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    protected function getDefaultOptions(): array
    {
        return [
            'include_signature' => true,
            'include_arguments' => true,
            'include_options' => true,
            'include_dependencies' => true,
            'scan_paths' => [
                app_path('Console/Commands'),
                app_path('Commands'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return Collection<string, array<string, mixed>>
     */
    protected function performScan(): Collection
    {
        $results = collect();
        $scanPaths = $this->config('scan_paths', [app_path('Console/Commands')]);

        foreach ($scanPaths as $scanPath) {
            if (! is_string($scanPath)) {
                continue;
            }
            if (! File::isDirectory($scanPath)) {
                continue;
            }
            $commandFiles = File::allFiles($scanPath);

            foreach ($commandFiles as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $commandData = $this->analyzeCommandFile($file);
                if ($commandData !== null) {
                    $results->put($commandData['class_name'], $commandData);
                }
            }
        }

        /** @var Collection<string, array<string, mixed>> $results */
        return $results;
    }

    /**
     * Analyze a single command file
     *
     * @return array<string, mixed>|null
     */
    protected function analyzeCommandFile(SplFileInfo $file): ?array
    {
        $content = File::get($file->getRealPath());
        $className = $this->extractClassName($content, $file);

        if (! $className || ! class_exists($className)) {
            return null;
        }

        try {
            $reflection = new ReflectionClass($className);

            // Check if it's a command class
            if (! $reflection->isSubclassOf(Command::class)) {
                return null;
            }

            $parentClass = $reflection->getParentClass();
            $commandData = [
                'class_name' => $className,
                'file_path' => $file->getRealPath(),
                'namespace' => $reflection->getNamespaceName(),
                'short_name' => $reflection->getShortName(),
                'is_abstract' => $reflection->isAbstract(),
                'parent_class' => $parentClass ? $parentClass->getName() : null,
                'traits' => array_keys($reflection->getTraits()),
                'interfaces' => $reflection->getInterfaceNames(),
            ];

            // Add signature information if enabled
            if ($this->config('include_signature')) {
                $commandData['signature_info'] = $this->extractSignatureInfo($content, $reflection);
            }

            // Add arguments if enabled
            if ($this->config('include_arguments')) {
                $commandData['arguments'] = $this->extractArguments($content);
            }

            // Add options if enabled
            if ($this->config('include_options')) {
                $commandData['options'] = $this->extractOptions($content);
            }

            // Add dependencies if enabled
            if ($this->config('include_dependencies')) {
                $commandData['dependencies'] = $this->extractDependencies($reflection);
            }

            return $commandData;
        } catch (Exception) {
            // Skip commands that can't be analyzed
            return null;
        }
    }

    /**
     * Extract class name from file content
     */
    protected function extractClassName(string $content, SplFileInfo $file): ?string
    {
        // Extract namespace
        preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches);
        $namespace = $namespaceMatches[1] ?? '';

        // Extract class name
        preg_match('/class\s+(\w+)/', $content, $classMatches);
        $className = $classMatches[1] ?? '';

        if ($className === '' || $className === '0') {
            return null;
        }

        return $namespace !== '' && $namespace !== '0' ? $namespace . '\\' . $className : $className;
    }

    /**
     * Extract command signature information
     *
     * @return array<string, mixed>
     */
    protected function extractSignatureInfo(string $content, ReflectionClass $reflection): array
    {
        $signatureInfo = [
            'signature' => null,
            'name' => null,
            'description' => null,
        ];

        // Try to extract signature from $signature property (handle multiline signatures)
        if (preg_match('/protected\s+\$signature\s*=\s*[\'"]([^\'";]*(?:\s+[^\'";]*)*)[\'"]/', $content, $matches)) {
            // Clean up multiline signature - remove extra whitespace and newlines
            $signature = preg_replace('/\s+/', ' ', trim($matches[1]));
            if ($signature !== null) {
                $signatureInfo['signature'] = $signature;

                // Extract command name from signature
                if (preg_match('/^([^\s{]+)/', $signature, $nameMatches)) {
                    $signatureInfo['name'] = $nameMatches[1];
                }
            }
        }

        // If multiline signature failed, try alternative regex for multiline
        if (! $signatureInfo['signature']) {
            // Match multiline signature with single quotes
            if (preg_match("/protected\s+\\\$signature\s*=\s*'([^']*(?:\s+[^']*)*?)'/s", $content, $matches)) {
                $signature = preg_replace('/\s+/', ' ', trim($matches[1]));
                if ($signature !== null) {
                    $signatureInfo['signature'] = $signature;
                    
                    if (preg_match('/^([^\s{]+)/', $signature, $nameMatches)) {
                        $signatureInfo['name'] = $nameMatches[1];
                    }
                }
            }
            // Match multiline signature with double quotes
            elseif (preg_match('/protected\s+\$signature\s*=\s*"([^"]*(?:\s+[^"]*)*?)"/s', $content, $matches)) {
                $signature = preg_replace('/\s+/', ' ', trim($matches[1]));
                if ($signature !== null) {
                    $signatureInfo['signature'] = $signature;
                    
                    if (preg_match('/^([^\s{]+)/', $signature, $nameMatches)) {
                        $signatureInfo['name'] = $nameMatches[1];
                    }
                }
            }
        }

        // Try to extract description
        if (preg_match('/protected\s+\$description\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
            $signatureInfo['description'] = $matches[1];
        }

        // If no signature found, try to instantiate and get it
        if (! $signatureInfo['signature']) {
            try {
                $instance = $reflection->newInstanceWithoutConstructor();
                if ($instance instanceof Command) {
                    $signatureInfo['name'] = $instance->getName();
                    $signatureInfo['description'] = $instance->getDescription();
                }
            } catch (Exception) {
                // Could not instantiate
            }
        }

        return $signatureInfo;
    }

    /**
     * Extract command arguments
     *
     * @return array<int, array<string, mixed>>
     */
    protected function extractArguments(string $content): array
    {
        $arguments = [];

        // Extract full signature first (handle multiline)
        $fullSignature = '';
        if (preg_match("/protected\s+\\\$signature\s*=\s*'([^']*?)'/s", $content, $matches)) {
            $fullSignature = $matches[1];
        } elseif (preg_match('/protected\s+\$signature\s*=\s*"([^"]*?)"/s', $content, $matches)) {
            $fullSignature = $matches[1];
        }

        // Clean up signature - remove extra whitespace and line breaks
        $fullSignature = preg_replace('/\s+/', ' ', trim($fullSignature));

        // Look for argument definitions in signature (not starting with --)
        if ($fullSignature && preg_match_all('/\{([^}]+)\}/', $fullSignature, $matches)) {
            foreach ($matches[1] as $argument) {
                $argument = trim($argument);
                // Skip if it starts with -- (it's an option)
                if (str_starts_with($argument, '--')) {
                    continue;
                }
                
                $argInfo = $this->parseArgumentOrOption($argument);
                if ($argInfo && isset($argInfo['name']) && is_string($argInfo['name'])) {
                    $arguments[] = $argInfo;
                }
            }
        }

        return $arguments;
    }

    /**
     * Extract command options
     *
     * @return array<int, array<string, mixed>>
     */
    protected function extractOptions(string $content): array
    {
        $options = [];

        // Extract full signature first (handle multiline)
        $fullSignature = '';
        if (preg_match("/protected\s+\\\$signature\s*=\s*'([^']*?)'/s", $content, $matches)) {
            $fullSignature = $matches[1];
        } elseif (preg_match('/protected\s+\$signature\s*=\s*"([^"]*?)"/s', $content, $matches)) {
            $fullSignature = $matches[1];
        }

        // Clean up signature - remove extra whitespace and line breaks
        $fullSignature = preg_replace('/\s+/', ' ', trim($fullSignature));

        // Look for option definitions in signature (starting with --)
        if ($fullSignature && preg_match_all('/\{([^}]+)\}/', $fullSignature, $matches)) {
            foreach ($matches[1] as $option) {
                $option = trim($option);
                // Only process if it starts with -- (it's an option)
                if (! str_starts_with($option, '--')) {
                    continue;
                }
                
                $optInfo = $this->parseArgumentOrOption($option);
                if ($optInfo && isset($optInfo['name']) && is_string($optInfo['name'])) {
                    $options[] = $optInfo;
                }
            }
        }

        return $options;
    }

    /**
     * Parse argument or option definition
     *
     * @return array<string, mixed>|null
     */
    protected function parseArgumentOrOption(string $definition): ?array
    {
        // Clean up the definition
        $definition = trim($definition);

        if ($definition === '' || $definition === '0') {
            return null;
        }

        $info = [
            'name' => '',
            'optional' => false,
            'has_default' => false,
            'default_value' => null,
            'description' => null,
        ];

        // Check for optional arguments/options
        if (str_contains($definition, '?')) {
            $info['optional'] = true;
            $definition = str_replace('?', '', $definition);
        }

        // Check for default values first, before extracting description
        if (str_contains($definition, '=')) {
            $parts = explode('=', $definition, 2);
            $namepart = trim($parts[0]);
            $valueAndDesc = trim($parts[1]);
            
            $info['has_default'] = true;
            
            // Check if the value part contains a description
            if (str_contains($valueAndDesc, ' : ')) {
                $valueParts = explode(' : ', $valueAndDesc, 2);
                $info['default_value'] = trim($valueParts[0]);
                $info['description'] = trim($valueParts[1]);
                $info['name'] = $namepart;
            } else {
                $info['default_value'] = $valueAndDesc;
                $info['name'] = $namepart;
            }
        } else {
            // Extract name and description when no default value
            if (str_contains($definition, ' : ')) {
                $parts = explode(' : ', $definition, 2);
                $info['name'] = trim($parts[0]);
                $info['description'] = trim($parts[1]);
            } else {
                $info['name'] = $definition;
            }
        }

        return $info;
    }

    /**
     * Extract command dependencies from constructor
     *
     * @return array<int, array<string, mixed>>
     */
    protected function extractDependencies(ReflectionClass $reflection): array
    {
        $dependencies = [];
        $constructor = $reflection->getConstructor();

        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                $type = $parameter->getType();
                $typeName = 'mixed';
                if ($type instanceof ReflectionNamedType) {
                    $typeName = $type->getName();
                }

                $dependencies[] = [
                    'name' => $parameter->getName(),
                    'type' => $typeName,
                    'optional' => $parameter->isOptional(),
                    'is_service' => $this->looksLikeService($typeName),
                ];
            }
        }

        return $dependencies;
    }

    /**
     * Check if a type looks like a service
     */
    protected function looksLikeService(string $typeName): bool
    {
        return str_ends_with($typeName, 'Service') ||
               str_ends_with($typeName, 'Repository') ||
               str_ends_with($typeName, 'Manager') ||
               str_contains($typeName, 'Service');
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    #[Override]
    protected function getSummary(): array
    {
        $summary = parent::getSummary();

        $commandsWithSignature = 0;
        $totalArguments = 0;
        $totalOptions = 0;
        $commandsWithDependencies = 0;

        foreach ($this->results as $command) {
            if (is_array($command)) {
                if (isset($command['signature_info']['signature'])) {
                    $commandsWithSignature++;
                }
                if (isset($command['arguments'])) {
                    $totalArguments += count($command['arguments']);
                }
                if (isset($command['options'])) {
                    $totalOptions += count($command['options']);
                }
                if (isset($command['dependencies']) && ! empty($command['dependencies'])) {
                    $commandsWithDependencies++;
                }
            }
        }

        $summary['commands_with_signature'] = $commandsWithSignature;
        $summary['total_arguments'] = $totalArguments;
        $summary['total_options'] = $totalOptions;
        $summary['commands_with_dependencies'] = $commandsWithDependencies;

        return $summary;
    }
}
