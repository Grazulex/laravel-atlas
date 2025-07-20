<?php

declare(strict_types=1);

namespace Grazulex\LaravelAtlas\Exporters;

class MermaidExporter extends BaseExporter
{
    /**
     * {@inheritdoc}
     */
    public function export(array $data): string
    {
        /** @var string $direction */
        $direction = $this->config('direction', 'TD');
        /** @var string $theme */
        $theme = $this->config('theme', 'default');

        $mermaid = "graph {$direction}\n";

        if ($theme !== 'default') {
            $mermaid .= "    %%{init: {'theme':'{$theme}'}}%%\n";
        }

        $mermaid .= "    %% Laravel Atlas Architecture Map\n";
        $generatedAt = isset($data['generated_at']) ? $this->mixedToString($data['generated_at']) : date('c');
        $mermaid .= "    %% Generated at: {$generatedAt}\n\n";

        if (isset($data['data']) && is_array($data['data'])) {
            foreach ($data['data'] as $type => $typeData) {
                if (is_array($typeData)) {
                    /** @var array<string, mixed> $typeData */
                    $mermaid .= $this->generateTypeSection((string) $type, $typeData);
                }
            }
        }

        return $mermaid;
    }

    /**
     * Generate a section for a specific type
     *
     * @param  array<string, mixed>  $typeData
     */
    protected function generateTypeSection(string $type, array $typeData): string
    {
        $section = "    %% {$type} section\n";
        $includeRelationships = $this->config('include_relationships', true);

        if (! isset($typeData['data']) || ! is_array($typeData['data'])) {
            return $section;
        }

        foreach ($typeData['data'] as $key => $item) {
            if (! is_array($item)) {
                continue;
            }

            $shortName = isset($item['short_name']) ? $this->mixedToString($item['short_name']) : class_basename((string) $key);
            $nodeId = $this->sanitizeNodeId($type . '_' . $shortName);

            // Create node with styling based on type
            $nodeStyle = $this->getNodeStyle($type);
            $section .= "    {$nodeId}[{$shortName}]{$nodeStyle}\n";

            // Add relationships for models
            if ($includeRelationships && $type === 'models' && isset($item['relationships']) && is_array($item['relationships'])) {
                foreach ($item['relationships'] as $relationName => $relation) {
                    if (is_array($relation) && isset($relation['type'])) {
                        $relationType = $this->mixedToString($relation['type']);
                        $targetNodeId = $this->sanitizeNodeId('model_' . $relationName);
                        $section .= "    {$nodeId} --> |{$relationType}| {$targetNodeId}\n";
                    }
                }
            }

            // Add route-controller connections
            if ($type === 'routes' && isset($item['controller']) && is_array($item['controller'])) {
                $controller = $item['controller'];
                if (isset($controller['short_name'])) {
                    $shortName = $this->mixedToString($controller['short_name']);
                    $controllerNodeId = $this->sanitizeNodeId('controller_' . $shortName);
                    $section .= "    {$nodeId} --> {$controllerNodeId}\n";
                }
            }
        }

        return $section . "\n";
    }

    /**
     * Get node styling based on type
     */
    protected function getNodeStyle(string $type): string
    {
        /** @var array<string, string> $styles */
        $styles = $this->config('node_styles', [
            'models' => ':::modelStyle',
            'routes' => ':::routeStyle',
            'jobs' => ':::jobStyle',
        ]);

        return $styles[$type] ?? '';
    }

    /**
     * Sanitize node ID for Mermaid
     */
    protected function sanitizeNodeId(string $id): string
    {
        $result = preg_replace('/[^a-zA-Z0-9_]/', '_', $id);

        return $result ?? $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension(): string
    {
        return 'mmd';
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType(): string
    {
        return 'text/plain';
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    protected function getDefaultConfig(): array
    {
        return [
            'direction' => 'TD', // Top Down, Left Right (LR), etc.
            'theme' => 'default',
            'include_relationships' => true,
            'node_styles' => [
                'models' => ':::modelStyle',
                'routes' => ':::routeStyle',
                'jobs' => ':::jobStyle',
            ],
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
