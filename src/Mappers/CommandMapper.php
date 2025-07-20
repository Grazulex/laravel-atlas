<?php

declare(strict_types=1);

namespace Grazulex\LaravelAtlas\Mappers;

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
            if (! is_string($scanPath) || ! File::isDirectory($scanPath)) {
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

            $commandData = [
                'class_name' => $className,
                'file_path' => $file->getRealPath(),
                'namespace' => $reflection->getNamespaceName(),
                'short_name' => $reflection->getShortName(),
                'is_abstract' => $reflection->isAbstract(),
                'parent_class' => $reflection->getParentClass()?->getName(),
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

        // Try to extract signature from $signature property
        if (preg_match('/protected\s+\$signature\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
            $signatureInfo['signature'] = $matches[1];

            // Extract command name from signature
            if (preg_match('/^([^\s{]+)/', $matches[1], $nameMatches)) {
                $signatureInfo['name'] = $nameMatches[1];
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

        // Look for argument definitions in signature
        if (preg_match_all('/\{([^}]+)\}/', $content, $matches)) {
            foreach ($matches[1] as $argument) {
                $argInfo = $this->parseArgumentOrOption($argument);
                if ($argInfo && ! str_starts_with($argInfo['name'], '--')) {
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

        // Look for option definitions in signature
        if (preg_match_all('/\{([^}]+)\}/', $content, $matches)) {
            foreach ($matches[1] as $option) {
                $optInfo = $this->parseArgumentOrOption($option);
                if ($optInfo && str_starts_with($optInfo['name'], '--')) {
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

        if (empty($definition)) {
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

        // Check for default values
        if (str_contains($definition, '=')) {
            $parts = explode('=', $definition, 2);
            $definition = trim($parts[0]);
            $info['has_default'] = true;
            $info['default_value'] = trim($parts[1]);
        }

        // Extract name and description
        if (str_contains($definition, ' : ')) {
            $parts = explode(' : ', $definition, 2);
            $info['name'] = trim($parts[0]);
            $info['description'] = trim($parts[1]);
        } else {
            $info['name'] = $definition;
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
