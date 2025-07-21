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
        $templatePath = $this->getTemplatePath();

        if (! file_exists($templatePath)) {
            throw new RuntimeException("HTML template not found at: {$templatePath}");
        }

        // Use Blade renderer for .blade.php templates, simple renderer for others
        if ($this->isBladeTemplate($templatePath)) {
            return $this->renderWithBlade($templatePath, [
                'data' => $data,
                'config' => $this->config,
                'title' => $this->config('title', 'Laravel Atlas Architecture Map'),
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

            return $renderer->renderFile($templatePath, $variables);
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
}
