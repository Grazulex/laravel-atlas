<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Override;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use SplFileInfo;

class JobMapper extends BaseMapper
{
    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'jobs';
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    protected function getDefaultOptions(): array
    {
        return [
            'include_queue_info' => true,
            'include_dependencies' => true,
            'include_methods' => true,
            'include_properties' => true,
            'scan_path' => app_path('Jobs'),
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
        $jobPath = $this->config('scan_path', app_path('Jobs'));

        if (! is_string($jobPath) || ! File::isDirectory($jobPath)) {
            return $results;
        }

        $jobFiles = File::allFiles($jobPath);

        foreach ($jobFiles as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $jobData = $this->analyzeJobFile($file);
            if ($jobData !== null) {
                $results->put($jobData['class_name'], $jobData);
            }
        }

        /** @var Collection<string, array<string, mixed>> $results */
        return $results;
    }

    /**
     * Analyze a single job file
     *
     * @return array<string, mixed>|null
     */
    protected function analyzeJobFile(SplFileInfo $file): ?array
    {
        $content = File::get($file->getRealPath());
        $className = $this->extractClassName($content, $file);

        if (! $className || ! class_exists($className)) {
            return null;
        }

        try {
            $reflection = new ReflectionClass($className);

            // Check if it's a job class
            if (! $reflection->implementsInterface(ShouldQueue::class) &&
                ! $this->looksLikeJobClass($reflection)) {
                return null;
            }

            $parentClass = $reflection->getParentClass();
            $jobData = [
                'class_name' => $className,
                'file_path' => $file->getRealPath(),
                'namespace' => $reflection->getNamespaceName(),
                'short_name' => $reflection->getShortName(),
                'is_abstract' => $reflection->isAbstract(),
                'parent_class' => $parentClass ? $parentClass->getName() : null,
                'traits' => array_keys($reflection->getTraits()),
                'interfaces' => $reflection->getInterfaceNames(),
                'implements_should_queue' => $reflection->implementsInterface(ShouldQueue::class),
            ];

            // Add queue info if enabled
            if ($this->config('include_queue_info')) {
                $jobData['queue_info'] = $this->extractQueueInfo($reflection, $className);
            }

            // Add dependencies if enabled
            if ($this->config('include_dependencies')) {
                $jobData['dependencies'] = $this->extractDependencies($reflection);
            }

            // Add methods if enabled
            if ($this->config('include_methods')) {
                $jobData['methods'] = $this->extractMethods($reflection);
            }

            // Add properties if enabled
            if ($this->config('include_properties')) {
                $jobData['properties'] = $this->extractProperties($reflection);
            }

            return $jobData;
        } catch (Exception) {
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
     * Check if class looks like a job class
     *
     * @param  ReflectionClass<object>  $reflection
     */
    protected function looksLikeJobClass(ReflectionClass $reflection): bool
    {
        // Check if it has a handle method
        if ($reflection->hasMethod('handle')) {
            return true;
        }

        // Check if class name ends with Job
        if (str_ends_with($reflection->getShortName(), 'Job')) {
            return true;
        }

        // Check if it's in a Jobs namespace
        return str_contains($reflection->getNamespaceName(), 'Jobs');
    }

    /**
     * Extract queue configuration information
     *
     * @param  ReflectionClass<object>  $reflection
     *
     * @return array<string, mixed>
     */
    protected function extractQueueInfo(ReflectionClass $reflection, string $className): array
    {
        $queueInfo = [
            'queue' => null,
            'connection' => null,
            'delay' => null,
            'timeout' => null,
            'tries' => null,
            'retry_until' => null,
            'backoff' => null,
        ];

        // Try to instantiate the job to get queue info
        try {
            // Create a dummy instance to extract properties
            $constructor = $reflection->getConstructor();
            $instance = null;

            if ($constructor === null || $constructor->getNumberOfRequiredParameters() === 0) {
                $instance = new $className;
            }

            if ($instance) {
                // Extract queue properties using reflection
                foreach (array_keys($queueInfo) as $property) {
                    if ($reflection->hasProperty($property)) {
                        $prop = $reflection->getProperty($property);
                        if ($prop->isPublic()) {
                            $queueInfo[$property] = $prop->getValue($instance);
                        }
                    }
                }
            }
        } catch (Exception) {
            // Could not instantiate, return defaults
        }

        return $queueInfo;
    }

    /**
     * Extract job dependencies from constructor
     *
     * @param  ReflectionClass<object>  $reflection
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
                    'default' => $parameter->isDefaultValueAvailable()
                        ? $parameter->getDefaultValue()
                        : null,
                ];
            }
        }

        return $dependencies;
    }

    /**
     * Extract job methods
     *
     * @param  ReflectionClass<object>  $reflection
     *
     * @return array<int, array<string, mixed>>
     */
    protected function extractMethods(ReflectionClass $reflection): array
    {
        $methods = [];
        $classMethods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($classMethods as $method) {
            if ($method->getDeclaringClass()->getName() !== $reflection->getName()) {
                continue; // Skip inherited methods
            }

            $returnType = $method->getReturnType();
            $returnTypeName = 'mixed';
            if ($returnType instanceof ReflectionNamedType) {
                $returnTypeName = $returnType->getName();
            }

            $methods[] = [
                'name' => $method->getName(),
                'parameters' => array_map(
                    function ($param): array {
                        $paramType = $param->getType();
                        $paramTypeName = 'mixed';
                        if ($paramType instanceof ReflectionNamedType) {
                            $paramTypeName = $paramType->getName();
                        }

                        return [
                            'name' => $param->getName(),
                            'type' => $paramTypeName,
                            'optional' => $param->isOptional(),
                        ];
                    },
                    $method->getParameters()
                ),
                'return_type' => $returnTypeName,
                'is_static' => $method->isStatic(),
            ];
        }

        return $methods;
    }

    /**
     * Extract job properties
     *
     * @param  ReflectionClass<object>  $reflection
     *
     * @return array<int, array<string, mixed>>
     */
    protected function extractProperties(ReflectionClass $reflection): array
    {
        $properties = [];
        $classProperties = $reflection->getProperties();

        foreach ($classProperties as $property) {
            if ($property->getDeclaringClass()->getName() !== $reflection->getName()) {
                continue; // Skip inherited properties
            }

            $propertyType = $property->getType();
            $propertyTypeName = 'mixed';
            if ($propertyType instanceof ReflectionNamedType) {
                $propertyTypeName = $propertyType->getName();
            }

            $properties[] = [
                'name' => $property->getName(),
                'type' => $propertyTypeName,
                'visibility' => $this->getPropertyVisibility($property),
                'is_static' => $property->isStatic(),
                'has_default' => $property->hasDefaultValue(),
            ];
        }

        return $properties;
    }

    /**
     * Get property visibility
     */
    protected function getPropertyVisibility(ReflectionProperty $property): string
    {
        if ($property->isPublic()) {
            return 'public';
        }
        if ($property->isProtected()) {
            return 'protected';
        }

        return 'private';
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

        $queuedJobs = $this->results->filter(
            fn ($job): bool => is_array($job) && ($job['implements_should_queue'] ?? false)
        )->count();

        $summary['queued_jobs_count'] = $queuedJobs;
        $summary['sync_jobs_count'] = $this->results->count() - $queuedJobs;

        // Count jobs by queue
        /** @var array<string, int> $queueCounts */
        $queueCounts = [];
        foreach ($this->results as $job) {
            if (is_array($job) && isset($job['queue_info']) && is_array($job['queue_info'])) {
                $queue = $job['queue_info']['queue'] ?? 'default';
                $queueCounts[$queue] = ($queueCounts[$queue] ?? 0) + 1;
            }
        }

        $summary['queue_distribution'] = $queueCounts;

        return $summary;
    }
}
