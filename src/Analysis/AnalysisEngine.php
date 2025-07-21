<?php

declare(strict_types=1);

namespace LaravelAtlas\Analysis;

use Exception;
use LaravelAtlas\AtlasManager;

class AnalysisEngine
{
    /** @var array<string, mixed> */
    private array $componentData = [];

    public function __construct(private readonly AtlasManager $manager) {}

    /**
     * Analyze relationships between all components
     *
     * @param  array<string>  $paths
     *
     * @return array<string, mixed>
     */
    public function analyzeComponentRelationships(array $paths): array
    {
        // First, collect data from all mappers
        $this->loadComponentData($paths);

        return [
            'component_summary' => $this->generateComponentSummary(),
            'relationships' => $this->mapComponentRelationships(),
            'dependency_graph' => $this->buildDependencyGraph(),
            'architecture_patterns' => $this->detectArchitecturalPatterns(),
            'complexity_metrics' => $this->calculateComplexityMetrics(),
            'recommendations' => $this->generateRecommendations(),
        ];
    }

    /**
     * @param  array<string>  $paths
     */
    private function loadComponentData(array $paths): void
    {
        $types = $this->manager->getAvailableTypes();

        foreach ($types as $type) {
            try {
                $this->componentData[$type] = $this->manager->scan($type, $paths);
            } catch (Exception $e) {
                $this->componentData[$type] = ['error' => $e->getMessage()];
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function generateComponentSummary(): array
    {
        $summary = [];

        foreach ($this->componentData as $type => $data) {
            if (isset($data['error'])) {
                $summary[$type] = ['count' => 0, 'error' => $data['error']];

                continue;
            }

            $summary[$type] = [
                'count' => count($data),
                'details' => $this->summarizeComponentType($type, $data),
            ];
        }

        return $summary;
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @return array<string, mixed>
     */
    private function summarizeComponentType(string $type, array $data): array
    {
        return match ($type) {
            'models' => $this->summarizeModels($data),
            'controllers' => $this->summarizeControllers($data),
            'services' => $this->summarizeServices($data),
            'jobs' => $this->summarizeJobs($data),
            'events' => $this->summarizeEvents($data),
            default => ['total' => count($data)],
        };
    }

    /**
     * @param  array<string, mixed>  $models
     *
     * @return array<string, mixed>
     */
    private function summarizeModels(array $models): array
    {
        $relationshipCount = 0;
        foreach ($models as $model) {
            if (isset($model['relationships'])) {
                $relationshipCount += count($model['relationships']);
            }
        }

        return [
            'total' => count($models),
            'with_relationships' => $relationshipCount,
        ];
    }

    /**
     * @param  array<string, mixed>  $controllers
     *
     * @return array<string, mixed>
     */
    private function summarizeControllers(array $controllers): array
    {
        $totalActions = 0;
        foreach ($controllers as $controller) {
            if (isset($controller['actions'])) {
                $totalActions += count($controller['actions']);
            }
        }

        return [
            'total' => count($controllers),
            'total_actions' => $totalActions,
        ];
    }

    /**
     * @param  array<string, mixed>  $services
     *
     * @return array<string, mixed>
     */
    private function summarizeServices(array $services): array
    {
        return ['total' => count($services)];
    }

    /**
     * @param  array<string, mixed>  $jobs
     *
     * @return array<string, mixed>
     */
    private function summarizeJobs(array $jobs): array
    {
        return ['total' => count($jobs)];
    }

    /**
     * @param  array<string, mixed>  $events
     *
     * @return array<string, mixed>
     */
    private function summarizeEvents(array $events): array
    {
        return ['total' => count($events)];
    }

    /**
     * @return array<string, mixed>
     */
    private function mapComponentRelationships(): array
    {
        $relationships = [];

        // Map models and their relationships
        if (isset($this->componentData['models'])) {
            foreach ($this->componentData['models'] as $modelName => $model) {
                if (isset($model['relationships'])) {
                    $relationships[$modelName] = [
                        'type' => 'model',
                        'relates_to' => $model['relationships'],
                    ];
                }
            }
        }

        // Map controllers and their actions
        if (isset($this->componentData['controllers'])) {
            foreach ($this->componentData['controllers'] as $controllerName => $controller) {
                $relatesTo = [];

                // Map controller to routes
                if (isset($this->componentData['routes'])) {
                    foreach ($this->componentData['routes'] as $route) {
                        if (isset($route['action']) && strpos($route['action'], $controllerName) === 0) {
                            $relatesTo[] = [
                                'name' => $route['name'] ?? $route['uri'] ?? 'unknown-route',
                                'type' => 'route',
                            ];
                        }
                    }
                }

                // Map controller to models (based on dependencies)
                if (isset($controller['dependencies'])) {
                    foreach ($controller['dependencies'] as $dependency) {
                        if (isset($this->componentData['models'][$dependency])) {
                            $relatesTo[] = [
                                'name' => $dependency,
                                'type' => 'model',
                            ];
                        }
                    }
                }

                if (! empty($relatesTo)) {
                    $relationships[$controllerName] = [
                        'type' => 'controller',
                        'relates_to' => $relatesTo,
                    ];
                }
            }
        }

        return $relationships;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildDependencyGraph(): array
    {
        $graph = [];
        foreach ($this->componentData as $type => $components) {
            if (is_array($components)) {
                foreach ($components as $component) {
                    $dependencies = $component['dependencies'] ?? [];
                    $componentName = $component['name'] ?? 'unknown';
                    $graph[$componentName] = [
                        'component' => $componentName,
                        'type' => $type,
                        'dependencies' => $dependencies,
                        'dependency_count' => count($dependencies),
                    ];
                }
            }
        }

        return $graph;
    }

    /**
     * @return array<string, mixed>
     */
    private function detectArchitecturalPatterns(): array
    {
        return [
            'service_layer_pattern' => [
                'detected' => false,
                'confidence' => 'low',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function calculateComplexityMetrics(): array
    {
        return [
            'total_components' => $this->getTotalComponentCount(),
            'coupling_score' => 'low',
        ];
    }

    private function getTotalComponentCount(): int
    {
        $count = 0;
        foreach ($this->componentData as $components) {
            if (is_array($components) && ! isset($components['error'])) {
                $count += count($components);
            }
        }

        return $count;
    }

    /**
     * @return array<string, mixed>
     */
    private function generateRecommendations(): array
    {
        return [];
    }
}
