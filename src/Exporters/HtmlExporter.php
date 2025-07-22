<?php

declare(strict_types=1);

namespace LaravelAtlas\Exporters;

use LaravelAtlas\Support\BladeRenderer;
use RuntimeException;
use Throwable;

class HtmlExporter extends BaseExporter
{
    /**
     * {@inheritdoc}
     *
     * @param  array<string, mixed>  $data
     */
    public function export(array $data): string
    {
        // Use intelligent template for data with flows/entry points
        if ($this->hasIntelligentData($data)) {
            return $this->exportIntelligentReport($data);
        }

        // Fallback to original template
        $templatePath = $this->getTemplatePath();

        if (! file_exists($templatePath)) {
            throw new RuntimeException("HTML template not found at: {$templatePath}");
        }

        // Use Blade renderer for .blade.php templates, simple renderer for others
        if ($this->isBladeTemplate($templatePath)) {
            // Generate static image if enabled
            $diagramImageBase64 = '';
            if ($this->config('use_static_diagram', true)) {
                $diagramImageBase64 = $this->generateStaticDiagramImage($data);
            }

            return $this->renderWithBlade($templatePath, [
                'data' => $data,
                'config' => $this->config,
                'title' => $this->config('title', 'Laravel Atlas Architecture Map'),
                'diagram_image_base64' => $diagramImageBase64,
                'use_static_diagram' => $this->config('use_static_diagram', true),
            ]);
        }
        $template = file_get_contents($templatePath);
        if ($template === false) {
            throw new RuntimeException("Failed to read HTML template at: {$templatePath}");
        }

        return $this->renderTemplate($template, [
            'data' => $data,
            'config' => $this->config,
            'title' => $this->config('title', 'Laravel Atlas Architecture Map'),
        ]);
    }

    /**
     * Check if template is a Blade template
     */
    protected function isBladeTemplate(string $templatePath): bool
    {
        return str_ends_with($templatePath, '.blade.php') ||
               str_ends_with($templatePath, '.blade.html') ||
               $this->templateContainsBladeDirectives($templatePath);
    }

    /**
     * Check if template contains Blade directives
     */
    protected function templateContainsBladeDirectives(string $templatePath): bool
    {
        $content = file_get_contents($templatePath);
        if ($content === false) {
            return false;
        }

        $result = preg_match('/@(if|foreach|for|while|switch|isset|empty|auth|guest|can|cannot|include|extends|section|yield|push|stack)\b/', $content);

        return $result === 1;
    }

    /**
     * Render template using Blade
     *
     * @param  array<string, mixed>  $variables
     */
    protected function renderWithBlade(string $templatePath, array $variables): string
    {
        try {
            $renderer = new BladeRenderer;

            // Add global variables that should be available in the template
            $globalVars = [
                'generated_at' => date('Y-m-d H:i:s'),
                'generation_time_ms' => round(microtime(true) * 1000 - $_SERVER['REQUEST_TIME_FLOAT'] * 1000),
                'atlas_version' => '1.0.0',
                'total_components' => $this->countTotalComponents($variables['data'] ?? []),
            ];

            // Merge with variables
            $allVariables = array_merge($variables, $globalVars);

            return $renderer->renderFile($templatePath, $allVariables);
        } catch (Throwable $e) {
            throw new RuntimeException('Blade template rendering failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Simple template renderer using variable replacement
     *
     * @param  array<string, mixed>  $variables
     */
    protected function renderTemplate(string $template, array $variables): string
    {
        // Simple variable replacement for basic templating
        foreach ($variables as $key => $value) {
            if (is_string($value)) {
                $template = str_replace('{{ $' . $key . ' }}', $value, $template);
            } elseif (is_array($value)) {
                // Handle metadata array specially
                if ($key === 'data' && isset($value['metadata'])) {
                    foreach ($value['metadata'] as $metaKey => $metaValue) {
                        $stringValue = is_string($metaValue) ? $metaValue : (string) $metaValue;
                        $template = str_replace('{{ $' . $metaKey . ' }}', $stringValue, $template);
                    }
                }

                $encodedValue = json_encode($value, JSON_PRETTY_PRINT);
                if ($encodedValue !== false) {
                    $template = str_replace('{{ $' . $key . ' }}', $encodedValue, $template);
                }
            } else {
                $template = str_replace('{{ $' . $key . ' }}', (string) $value, $template);
            }
        }

        // Add global variables that should be available in the template
        $globalVars = [
            'generated_at' => date('Y-m-d H:i:s'),
            'generation_time_ms' => round(microtime(true) * 1000 - $_SERVER['REQUEST_TIME_FLOAT'] * 1000),
            'atlas_version' => '1.0.0',
            'total_components' => $this->countTotalComponents($variables['data'] ?? []),
        ];

        // Add global variables to template
        foreach ($globalVars as $key => $value) {
            $stringValue = is_string($value) ? $value : (string) $value;
            $template = str_replace('{{ $' . $key . ' }}', $stringValue, $template);
        }

        // Handle data iteration for components
        if (isset($variables['data']) && is_array($variables['data'])) {
            return $this->renderDataSections($template, $variables['data']);
        }

        return $template;
    }

    /**
     * Render data sections in the template
     *
     * @param  array<string, mixed>  $data
     */
    protected function renderDataSections(string $template, array $data): string
    {
        // First, find all section markers in the template
        preg_match_all('/<!-- START:([a-z0-9_]+) -->/', $template, $matches);
        $allComponentTypes = $matches[1];

        // Process all sections that exist in the template
        foreach ($allComponentTypes as $componentType) {
            $sectionStart = "<!-- START:{$componentType} -->";
            $sectionEnd = "<!-- END:{$componentType} -->";

            $startPos = strpos($template, $sectionStart);
            $endPos = strpos($template, $sectionEnd);

            if ($startPos !== false && $endPos !== false) {
                // Extract the section template between the markers
                $sectionTemplate = substr(
                    $template,
                    $startPos + strlen($sectionStart),
                    $endPos - $startPos - strlen($sectionStart)
                );

                $renderedSection = '';

                // Process only if we have data for this component type
                if (isset($data[$componentType]) && is_array($data[$componentType])) {
                    $componentItems = $data[$componentType];

                    // Handle both array of items and nested data structure
                    $itemsToProcess = isset($componentItems['data']) && is_array($componentItems['data'])
                        ? $componentItems['data']
                        : $componentItems;

                    foreach ($itemsToProcess as $item) {
                        $itemHtml = $sectionTemplate;

                        if (is_array($item)) {
                            foreach ($item as $key => $value) {
                                $stringValue = is_string($value) ? $value : json_encode($value);
                                if ($stringValue !== false) {
                                    $itemHtml = str_replace('{{ $' . $key . ' }}', $stringValue, $itemHtml);
                                }
                            }
                        }

                        $renderedSection .= $itemHtml;
                    }
                } else {
                    // No data for this section, replace template variables with empty strings
                    preg_match_all('/{{ \$([\w_]+) }}/', $sectionTemplate, $varMatches);
                    $emptySection = $sectionTemplate;
                    foreach ($varMatches[0] as $match) {
                        $emptySection = str_replace($match, '', $emptySection);
                    }
                    $renderedSection = $emptySection;
                }

                // Replace the section in the template
                $template = str_replace(
                    $sectionStart . $sectionTemplate . $sectionEnd,
                    $renderedSection,
                    $template
                );
            }
        }

        return $template;
    }

    /**
     * Get the template path for the HTML template
     */
    protected function getTemplatePath(): string
    {
        /** @var string|null $customTemplate */
        $customTemplate = $this->config['template_path'] ?? null;

        if ($customTemplate && file_exists($customTemplate)) {
            return $customTemplate;
        }

        return $this->getDefaultTemplatePath();
    }

    /**
     * Get the default template path
     */
    protected function getDefaultTemplatePath(): string
    {
        // Try Blade template first, then fall back to simple template
        $bladeTemplate = __DIR__ . '/../../stubs/html-template-blade.html';
        if (file_exists($bladeTemplate)) {
            return $bladeTemplate;
        }

        return __DIR__ . '/../../stubs/html-template-simple.html';
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension(): string
    {
        return 'html';
    }

    /**
     * Generate a static diagram image in base64 format
     *
     * @param  array<string, mixed>  $data  Analysis data
     *
     * @return string Base64 encoded image data
     */
    protected function generateStaticDiagramImage(array $data): string
    {
        $imageData = $this->generateDiagramImage(
            $data,
            $this->config('diagram_image_format', 'png'),
            $this->config('diagram_image_width', 1200),
            $this->config('diagram_image_height', 800)
        );

        // Convertir en base64 pour intégration dans le HTML
        $base64Image = base64_encode($imageData);
        $mimeType = match ($this->config('diagram_image_format', 'png')) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            default => 'image/png',
        };

        return "data:{$mimeType};base64,{$base64Image}";
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType(): string
    {
        return 'text/html';
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
            'template_path' => null, // Custom template path
            'use_static_diagram' => true, // Utiliser une image statique pour le diagramme
            'diagram_image_format' => 'png', // Format de l'image du diagramme
            'diagram_image_width' => 1200, // Largeur de l'image du diagramme
            'diagram_image_height' => 800, // Hauteur de l'image du diagramme
        ];
    }

    /**
     * Count total components in the data array
     *
     * @param  array<string, mixed>  $data
     */
    protected function countTotalComponents(array $data): int
    {
        $total = 0;

        foreach ($data as $components) {
            if (is_array($components)) {
                // If it's a direct array of items
                if (isset($components[0])) {
                    $total += count($components);
                }
                // If it has a 'data' key with components
                elseif (isset($components['data']) && is_array($components['data'])) {
                    $total += count($components['data']);
                }
            }
        }

        return $total;
    }

    /**
     * Check if data contains intelligent flow information
     *
     * @param  array<string, mixed>  $data
     */
    protected function hasIntelligentData(array $data): bool
    {
        return isset($data['flows']) ||
               isset($data['metadata']) ||
               (isset($data['routes']) && is_array($data['routes']) && (isset($data['routes']) && $data['routes'] !== []) && isset($data['routes'][0]['flows']));
    }

    /**
     * Export intelligent report with flows and entry points
     *
     * @param  array<string, mixed>  $data
     */
    protected function exportIntelligentReport(array $data): string
    {
        $templatePath = $this->getIntelligentTemplatePath();

        if (! file_exists($templatePath)) {
            throw new RuntimeException("Intelligent HTML template not found at: {$templatePath}");
        }

        // Générer les flows d'observers automatiquement
        $observerFlows = $this->generateObserverFlows($data);
        if ($observerFlows !== []) {
            $data['flows'] = array_merge($data['flows'] ?? [], $observerFlows);
        }

        return $this->renderWithBlade($templatePath, [
            'data' => $data,
            'config' => $this->config,
            'title' => $data['metadata']['app_name'] ?? 'Laravel Atlas - Architecture Report',
        ]);
    }

    /**
     * Génère des flows pour les relations Model-Observer
     *
     * @param  array<string, mixed>  $data
     *
     * @return array<int, array<string, mixed>>
     */
    protected function generateObserverFlows(array $data): array
    {
        $flows = [];
        $models = $data['models'] ?? [];
        $observers = $data['observers'] ?? [];
        $events = $data['events'] ?? [];
        $listeners = $data['listeners'] ?? [];

        // Créer un mapping des observers par modèle
        $observersByModel = [];
        foreach ($observers as $observer) {
            $model = $observer['model'] ?? '';
            if ($model) {
                $observersByModel[$model][] = $observer;
            }
        }

        // Générer des flows pour chaque modèle avec observers
        foreach ($models as $model) {
            $modelClass = $model['class_name'] ?? '';
            $modelName = class_basename($modelClass);

            if (isset($observersByModel[$modelClass]) || isset($observersByModel[$modelName])) {
                $modelObservers = $observersByModel[$modelClass] ?? $observersByModel[$modelName] ?? [];

                $steps = [];
                $steps[] = "$modelName model lifecycle event (creating, created, updating, updated, etc.)";

                foreach ($modelObservers as $observer) {
                    $observerName = class_basename($observer['class_name'] ?? '');
                    $methods = $observer['methods'] ?? [];

                    if (! empty($methods)) {
                        foreach (array_keys($methods) as $methodName) {
                            $steps[] = "$observerName::$methodName - Handle $modelName $methodName event";
                        }
                    } else {
                        $steps[] = "$observerName - Handle $modelName lifecycle events";
                    }

                    // Ajouter les événements dispatchés par l'observer
                    if (! empty($observer['events'])) {
                        foreach ($observer['events'] as $event) {
                            $eventName = class_basename($event);
                            $steps[] = "$eventName event dispatched (async)";

                            // Chercher les listeners pour cet événement
                            $eventListeners = $this->findListenersForEvent($event, $listeners);
                            foreach ($eventListeners as $listener) {
                                $listenerName = class_basename($listener['class_name'] ?? '');
                                $steps[] = "$listenerName listener - Handle $eventName";

                                // Chercher les jobs dispatchés par ce listener
                                $listenerJobs = $listener['jobs'] ?? [];
                                foreach ($listenerJobs as $job) {
                                    $jobName = class_basename($job);
                                    $steps[] = "$jobName job queued (async)";
                                }
                            }
                        }
                    }
                }

                // Chercher les événements liés au modèle par convention de nommage
                $modelEvents = $this->findModelEvents($modelName, $events);
                foreach ($modelEvents as $event) {
                    $eventName = class_basename($event['class_name'] ?? '');
                    if (! in_array("$eventName event dispatched (async)", $steps)) {
                        $steps[] = "$eventName event dispatched (async)";

                        // Chercher les listeners pour cet événement
                        $eventListeners = $this->findListenersForEvent($event['class_name'], $listeners);
                        foreach ($eventListeners as $listener) {
                            $listenerName = class_basename($listener['class_name'] ?? '');
                            $steps[] = "$listenerName listener - Handle $eventName";

                            // Chercher les jobs dispatchés par ce listener
                            $listenerJobs = $listener['jobs'] ?? [];
                            foreach ($listenerJobs as $job) {
                                $jobName = class_basename($job);
                                $steps[] = "$jobName job queued (async)";
                            }
                        }
                    }
                }

                $flows[] = [
                    'name' => "$modelName Lifecycle Flow",
                    'entry_point' => "$modelName model operations",
                    'type' => 'mixed',
                    'description' => "Complete lifecycle handling for $modelName including observers, events, listeners, and background jobs",
                    'steps' => $steps,
                ];
            }
        }

        return $flows;
    }

    /**
     * Trouve les listeners pour un événement donné
     *
     * @param  array<string, mixed>  $listeners
     *
     * @return array<int, array<string, mixed>>
     */
    protected function findListenersForEvent(string $eventClass, array $listeners): array
    {
        $eventListeners = [];

        foreach ($listeners as $listener) {
            $listenerEvent = $listener['event'] ?? '';
            if ($listenerEvent === $eventClass || class_basename($listenerEvent) === class_basename($eventClass)) {
                $eventListeners[] = $listener;
            }
        }

        return $eventListeners;
    }

    /**
     * Trouve les événements liés à un modèle par convention de nommage
     *
     * @param  array<string, mixed>  $events
     *
     * @return array<int, array<string, mixed>>
     */
    protected function findModelEvents(string $modelName, array $events): array
    {
        $modelEvents = [];

        foreach ($events as $event) {
            $eventName = class_basename($event['class_name'] ?? '');
            // Chercher les événements qui contiennent le nom du modèle
            if (str_contains($eventName, $modelName)) {
                $modelEvents[] = $event;
            }
        }

        return $modelEvents;
    }

    /**
     * Get path to intelligent template
     */
    protected function getIntelligentTemplatePath(): string
    {
        $customPath = $this->config('intelligent_template');
        if ($customPath && file_exists($customPath)) {
            return $customPath;
        }

        // Default intelligent template (consolidated version)
        return __DIR__ . '/../../stubs/intelligent-html-template-consolidated.blade.php';
    }

    /**
     * Export HTML from a PHP data file (generated by PhpExporter)
     */
    public function exportFromPhpFile(string $phpFilePath): string
    {
        if (! file_exists($phpFilePath)) {
            throw new RuntimeException("PHP data file not found: $phpFilePath");
        }

        $data = require $phpFilePath;

        if (! is_array($data)) {
            throw new RuntimeException('PHP file must return an array');
        }

        return $this->export($data);
    }
}
