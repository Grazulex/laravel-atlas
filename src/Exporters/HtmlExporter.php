<?php

declare(strict_types=1);

namespace LaravelAtlas\Exporters;

use Throwable;
use LaravelAtlas\Support\BladeRenderer;
use RuntimeException;

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
        // Simple foreach rendering for each component type
        foreach ($data as $componentType => $componentData) {
            $sectionStart = "<!-- START:{$componentType} -->";
            $sectionEnd = "<!-- END:{$componentType} -->";

            $startPos = strpos($template, $sectionStart);
            $endPos = strpos($template, $sectionEnd);

            if ($startPos !== false && $endPos !== false) {
                $sectionTemplate = substr($template, $startPos + strlen($sectionStart), $endPos - $startPos - strlen($sectionStart));
                $renderedSection = '';

                if (isset($componentData['data']) && is_array($componentData['data'])) {
                    foreach ($componentData['data'] as $item) {
                        $itemHtml = $sectionTemplate;
                        if (is_array($item)) {
                            foreach ($item as $key => $value) {
                                $encodedValue = is_string($value) ? $value : json_encode($value);
                                if ($encodedValue !== false) {
                                    $itemHtml = str_replace('{{ $' . $key . ' }}', $encodedValue, $itemHtml);
                                }
                            }
                        }
                        $renderedSection .= $itemHtml;
                    }
                }

                $template = str_replace($sectionStart . $sectionTemplate . $sectionEnd, $renderedSection, $template);
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
}
