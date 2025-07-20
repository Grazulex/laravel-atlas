<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use SplFileInfo;
use Throwable;

class ResourceMapper extends BaseMapper
{
    public function getType(): string
    {
        return 'resources';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultOptions(): array
    {
        return [
            'include_attributes' => true,
            'include_relationships' => true,
            'include_model_binding' => true,
            'include_transformations' => true,
            'scan_path' => app_path('Http/Resources'),
        ];
    }

    /**
     * @return Collection<string, array<string, mixed>>
     */
    protected function performScan(): Collection
    {
        $results = collect();
        $scanPath = $this->config('scan_path', app_path('Http/Resources'));

        if (! is_string($scanPath) || ! File::isDirectory($scanPath)) {
            return $results;
        }

        $resourceFiles = File::allFiles($scanPath);

        foreach ($resourceFiles as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $resourceData = $this->analyzeResourceFile($file);
            if ($resourceData !== null) {
                $results->put($resourceData['class_name'], $resourceData);
            }
        }

        return $results;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function analyzeResourceFile(SplFileInfo $file): ?array
    {
        $filePath = $file->getPathname();
        $className = $this->extractClassName($filePath);

        if (! $className || ! $this->isResourceClass($filePath)) {
            return null;
        }

        return $this->analyzeResourceClass($className, $filePath);
    }

    private function isResourceClass(string $filePath): bool
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return false;
        }

        return str_contains($content, 'use Illuminate\Http\Resources\Json\JsonResource') ||
               str_contains($content, 'use Illuminate\Http\Resources\Json\ResourceCollection') ||
               str_contains($content, 'extends JsonResource') ||
               str_contains($content, 'extends ResourceCollection');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function analyzeResourceClass(string $className, string $filePath): ?array
    {
        try {
            if (! class_exists($className)) {
                return null;
            }

            $reflection = new ReflectionClass($className);
            $content = file_get_contents($filePath);

            if ($content === false) {
                return null;
            }

            return [
                'class_name' => $reflection->getShortName(),
                'name' => $reflection->getShortName(),
                'full_name' => $reflection->getName(),
                'file' => $filePath,
                'namespace' => $reflection->getNamespaceName(),
                'resource_type' => $this->determineResourceType($className, $content),
                'model_binding' => $this->config('include_model_binding', true) ? $this->extractModelBinding($content) : null,
                'attributes' => $this->config('include_attributes', true) ? $this->extractAttributes($content) : [],
                'relationships' => $this->config('include_relationships', true) ? $this->extractRelationships($content) : [],
                'transformations' => $this->config('include_transformations', true) ? $this->extractTransformations($content) : [],
                'line_count' => substr_count($content, "\n") + 1,
            ];
        } catch (Throwable) {
            return null;
        }
    }

    private function determineResourceType(string $className, string $content): string
    {
        if (str_contains($content, 'extends ResourceCollection') || str_contains($className, 'Collection')) {
            return 'collection';
        }

        return 'single';
    }

    private function extractModelBinding(string $content): ?string
    {
        // Look for model type hints or usage
        if (! preg_match('/\$this->resource(?:->\w+)*/', $content)) {
            return null;
        }
        // Look for imports to determine model type
        if (preg_match('/use\s+App\\\\Models\\\\(\w+)/', $content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function extractAttributes(string $content): array
    {
        $attributes = [];

        // Look for toArray method
        if (preg_match('/function\s+toArray\s*\([^)]*\)\s*\{([^}]+)\}/', $content, $matches)) {
            $toArrayBody = $matches[1];

            // Extract array keys
            if (preg_match_all('/[\'"](\w+)[\'"]\s*=>\s*([^,\]]+)/', $toArrayBody, $attrMatches, PREG_SET_ORDER)) {
                foreach ($attrMatches as $match) {
                    $attributes[] = [
                        'name' => $match[1],
                        'expression' => trim($match[2]),
                        'is_resource_attribute' => str_contains($match[2], '$this->'),
                    ];
                }
            }
        }

        return $attributes;
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function extractRelationships(string $content): array
    {
        $relationships = [];

        // Look for resource relationships like ->load(), ->with(), etc.
        if (preg_match_all('/\$this->[\'"]?(\w+)[\'"]?\s*(?:,|\]|$)/', $content, $matches)) {
            foreach ($matches[1] as $relationship) {
                if (! in_array($relationship, ['resource', 'additional', 'with', 'withoutWrapping'])) {
                    $relationships[] = [
                        'name' => $relationship,
                        'type' => 'resource_relationship',
                    ];
                }
            }
        }

        return $relationships;
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function extractTransformations(string $content): array
    {
        $transformations = [];

        // Look for data transformations
        if (preg_match_all('/(\w+)\s*\(\s*\$this->(\w+)\s*\)/', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $transformations[] = [
                    'function' => $match[1],
                    'attribute' => $match[2],
                ];
            }
        }

        return $transformations;
    }

    private function extractClassName(string $filePath): ?string
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return null;
        }

        // Extract namespace
        $namespace = '';
        if (preg_match('/namespace\s+([^;]+)/', $content, $matches)) {
            $namespace = $matches[1] . '\\';
        }

        // Extract class name
        if (preg_match('/class\s+(\w+)/', $content, $matches)) {
            return $namespace . $matches[1];
        }

        return null;
    }
}
