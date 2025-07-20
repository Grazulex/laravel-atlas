<?php

declare(strict_types=1);

namespace LaravelAtlas\Exporters;

class MarkdownExporter extends BaseExporter
{
    /**
     * {@inheritdoc}
     */
    public function export(array $data): string
    {
        $includeTableOfContents = $this->config('include_toc', true);
        $includeTimestamp = $this->config('include_timestamp', true);
        $includeStats = $this->config('include_stats', true);

        $markdown = $this->generateHeader($data);

        if ($includeTimestamp) {
            $markdown .= $this->generateMetadata($data);
        }

        if ($includeStats && isset($data['summary'])) {
            /** @var array<string, mixed> $summary */
            $summary = $data['summary'];
            $markdown .= $this->generateSummary($summary);
        }

        if ($includeTableOfContents) {
            $markdown .= $this->generateTableOfContents($data);
        }

        if (isset($data['data']) && is_array($data['data'])) {
            foreach ($data['data'] as $type => $typeData) {
                /** @var array<string, mixed> $typeData */
                $markdown .= $this->generateTypeSection((string) $type, $typeData);
            }
        }

        return $markdown;
    }

    /**
     * Generate header section
     *
     * @param  array<string, mixed>  $data
     */
    protected function generateHeader(array $data): string
    {
        /** @var string $title */
        $title = $this->config('title', 'Laravel Atlas Architecture Map');

        return "# {$title}\n\n";
    }

    /**
     * Generate metadata section
     *
     * @param  array<string, mixed>  $data
     */
    protected function generateMetadata(array $data): string
    {
        $metadata = "## 📊 Generation Information\n\n";

        if (isset($data['generated_at'])) {
            $generatedAt = $this->mixedToString($data['generated_at']);
            $metadata .= "- **Generated:** {$generatedAt}\n";
        }

        if (isset($data['generation_time_ms'])) {
            $generationTime = $this->mixedToString($data['generation_time_ms']);
            $metadata .= "- **Generation Time:** {$generationTime}ms\n";
        }

        if (isset($data['atlas_version'])) {
            $atlasVersion = $this->mixedToString($data['atlas_version']);
            $metadata .= "- **Atlas Version:** {$atlasVersion}\n";
        }

        return $metadata . "\n";
    }

    /**
     * Generate summary section
     *
     * @param  array<string, mixed>  $summary
     */
    protected function generateSummary(array $summary): string
    {
        $summaryMd = "## 📈 Summary\n\n";

        if (isset($summary['total_components'])) {
            $totalComponents = is_numeric($summary['total_components']) ? $summary['total_components'] : 0;
            $summaryMd .= "**Total Components:** {$totalComponents}\n\n";
        }

        if (isset($summary['by_type']) && is_array($summary['by_type'])) {
            $summaryMd .= "| Component Type | Count |\n";
            $summaryMd .= "|----------------|-------|\n";

            foreach ($summary['by_type'] as $type => $count) {
                $typeFormatted = ucfirst((string) $type);
                $countFormatted = is_numeric($count) ? $count : 0;
                $summaryMd .= "| {$typeFormatted} | {$countFormatted} |\n";
            }
        }

        return $summaryMd . "\n";
    }

    /**
     * Generate table of contents
     *
     * @param  array<string, mixed>  $data
     */
    protected function generateTableOfContents(array $data): string
    {
        $toc = "## 📋 Table of Contents\n\n";

        if (! isset($data['data']) || ! is_array($data['data'])) {
            return $toc;
        }

        foreach ($data['data'] as $type => $typeData) {
            if (! is_array($typeData)) {
                continue;
            }
            if (! isset($typeData['data'])) {
                continue;
            }
            $count = is_countable($typeData['data']) ? count($typeData['data']) : 0;
            $typeFormatted = ucfirst($type);
            $typeAnchor = strtolower($type);

            $toc .= "- [{$typeFormatted} ({$count})](#{$typeAnchor})\n";
        }

        return $toc . "\n---\n\n";
    }

    /**
     * Generate section for a specific component type
     *
     * @param  array<string, mixed>  $typeData
     */
    protected function generateTypeSection(string $type, array $typeData): string
    {
        if (! isset($typeData['data']) || ! is_array($typeData['data'])) {
            return '';
        }

        $count = count($typeData['data']);
        $typeFormatted = ucfirst($type);
        $section = "## {$typeFormatted} ({$count}) {#" . strtolower($type) . "}\n\n";

        foreach ($typeData['data'] as $key => $item) {
            if (is_array($item)) {
                /** @var array<string, mixed> $item */
                $section .= $this->generateComponentDetails($type, (string) $key, $item);
            }
        }

        return $section;
    }

    /**
     * Generate details for a specific component
     *
     * @param  array<string, mixed>  $item
     */
    protected function generateComponentDetails(string $type, string $key, mixed $item): string
    {
        if (! is_array($item)) {
            return '';
        }

        $name = isset($item['short_name']) ? $this->mixedToString($item['short_name']) : class_basename($key);
        $details = "### 🔹 {$name}\n\n";

        // Basic information
        $details .= "- **Full Name:** `{$key}`\n";

        if (isset($item['namespace'])) {
            $namespace = $this->mixedToString($item['namespace']);
            $details .= "- **Namespace:** `{$namespace}`\n";
        }

        if (isset($item['file_path'])) {
            $filePath = $this->mixedToString($item['file_path']);
            $details .= "- **File:** `{$filePath}`\n";
        }

        // Type-specific details
        match ($type) {
            'models' => $details .= $this->generateModelDetails($item),
            'routes' => $details .= $this->generateRouteDetails($item),
            'jobs' => $details .= $this->generateJobDetails($item),
            default => $details . "\n",
        };

        return $details . "\n";
    }

    /**
     * Generate model-specific details
     *
     * @param  array<string, mixed>  $model
     */
    protected function generateModelDetails(array $model): string
    {
        $details = '';

        // Relationships
        if (isset($model['relationships']) && is_array($model['relationships']) && (isset($model['relationships']) && $model['relationships'] !== [])) {
            $details .= "- **Relationships:**\n";
            foreach ($model['relationships'] as $relationName => $relation) {
                if (is_array($relation) && isset($relation['type'])) {
                    $relationType = $this->mixedToString($relation['type']);
                    $details .= "  - `{$relationName}` ({$relationType})\n";
                }
            }
        }

        // Attributes
        if (isset($model['attributes']) && is_array($model['attributes'])) {
            if (isset($model['attributes']['table'])) {
                $tableName = $this->mixedToString($model['attributes']['table']);
                $details .= "- **Table:** `{$tableName}`\n";
            }

            if (isset($model['attributes']['fillable']) && is_array($model['attributes']['fillable']) && (isset($model['attributes']['fillable']) && $model['attributes']['fillable'] !== [])) {
                $fillable = implode('`, `', $model['attributes']['fillable']);
                $details .= "- **Fillable:** `{$fillable}`\n";
            }
        }

        return $details;
    }

    /**
     * Generate route-specific details
     *
     * @param  array<string, mixed>  $route
     */
    protected function generateRouteDetails(array $route): string
    {
        $details = '';

        if (isset($route['uri'])) {
            $uri = $this->mixedToString($route['uri']);
            $details .= "- **URI:** `{$uri}`\n";
        }

        if (isset($route['methods']) && is_array($route['methods'])) {
            $methods = implode('`, `', array_map([$this, 'mixedToString'], $route['methods']));
            $details .= "- **Methods:** `{$methods}`\n";
        }

        if (isset($route['controller']) && is_array($route['controller'])) {
            $controller = $route['controller'];
            if (isset($controller['class'], $controller['method'])) {
                $controllerClass = $this->mixedToString($controller['class']);
                $controllerMethod = $this->mixedToString($controller['method']);
                $details .= "- **Controller:** `{$controllerClass}@{$controllerMethod}`\n";
            }
        }

        if (isset($route['middleware']) && is_array($route['middleware']) && (isset($route['middleware']) && $route['middleware'] !== [])) {
            $middleware = implode('`, `', $route['middleware']);
            $details .= "- **Middleware:** `{$middleware}`\n";
        }

        return $details;
    }

    /**
     * Generate job-specific details
     *
     * @param  array<string, mixed>  $job
     */
    protected function generateJobDetails(array $job): string
    {
        $details = '';

        if (isset($job['implements_should_queue']) && $job['implements_should_queue']) {
            $details .= "- **Type:** Queued Job ⚡\n";

            if (isset($job['queue_info']) && is_array($job['queue_info'])) {
                $queueInfo = $job['queue_info'];

                if (isset($queueInfo['queue']) && $queueInfo['queue']) {
                    $queueName = $this->mixedToString($queueInfo['queue']);
                    $details .= "- **Queue:** `{$queueName}`\n";
                }

                if (isset($queueInfo['connection']) && $queueInfo['connection']) {
                    $connectionName = $this->mixedToString($queueInfo['connection']);
                    $details .= "- **Connection:** `{$connectionName}`\n";
                }
            }
        } else {
            $details .= "- **Type:** Synchronous Job 🔄\n";
        }

        return $details;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension(): string
    {
        return 'md';
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType(): string
    {
        return 'text/markdown';
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    protected function getDefaultConfig(): array
    {
        return [
            'title' => 'Laravel Atlas Architecture Map',
            'include_toc' => true,
            'include_timestamp' => true,
            'include_stats' => true,
        ];
    }

    /**
     * Helper method to safely convert mixed to string
     */
    private function mixedToString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_null($value)) {
            return '';
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value) ?: '';
        }

        // Safe conversion for mixed values
        if (is_null($value)) {
            return '';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        // Fallback for other types
        return '';
    }
}
