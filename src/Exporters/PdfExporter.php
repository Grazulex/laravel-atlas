<?php

declare(strict_types=1);

namespace LaravelAtlas\Exporters;

use Dompdf\Dompdf;
use Dompdf\Options;
use LaravelAtlas\Support\BladeRenderer;
use RuntimeException;
use Throwable;

class PdfExporter extends BaseExporter
{
    /**
     * {@inheritdoc}
     *
     * @param  array<string, mixed>  $data
     */
    public function export(array $data): string
    {
        // Check if required dependencies are available
        if (! class_exists('Dompdf\\Dompdf') || ! class_exists('Dompdf\\Options')) {
            throw new RuntimeException(
                'Dompdf is required for PDF export. Install it with: composer require dompdf/dompdf'
            );
        }

        $htmlContent = $this->generateHtmlContent($data);

        // Using fully qualified class names for better IDE support and to avoid PHPStan errors
        // Configure Dompdf
        /** @var Options $options */
        $options = new Options;
        $options->set('defaultFont', $this->config('font', 'DejaVu Sans'));
        $options->set('isRemoteEnabled', $this->config('remote_enabled', false));
        $options->set('isHtml5ParserEnabled', true);

        /** @var Dompdf $dompdf */
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($htmlContent);

        // Set paper size and orientation
        /** @var string $paperSize */
        $paperSize = $this->config('paper_size', 'A4');
        /** @var string $orientation */
        $orientation = $this->config('orientation', 'portrait');
        $dompdf->setPaper($paperSize, $orientation);

        // Render PDF
        $dompdf->render();

        $output = $dompdf->output();
        if ($output === null) {
            throw new RuntimeException('Failed to generate PDF output');
        }

        return $output;
    }

    /**
     * Generate HTML content for PDF
     *
     * @param  array<string, mixed>  $data
     */
    protected function generateHtmlContent(array $data): string
    {
        $templatePath = $this->getTemplatePath();

        if (! file_exists($templatePath)) {
            throw new RuntimeException("PDF template not found at: {$templatePath}");
        }

        // Generate Mermaid diagram for architecture visualization
        // We don't include the actual Mermaid diagram in PDF, but we prepare component relationships
        // for the relationship section in the PDF template

        // Generate static diagram image for PDF if enabled
        $diagramImageBase64 = '';
        if ($this->config('use_static_diagram', true)) {
            $diagramImageBase64 = $this->generateStaticDiagramImage($data);
        }

        // Prepare data structure specifically for Blade template
        $templateData = [
            'data' => $data,
            'config' => $this->config,
            'title' => $this->config('title', 'Laravel Atlas Architecture Map'),
            'generated_at' => date('Y-m-d H:i:s'),
            'generation_time_ms' => round(microtime(true) * 1000 - $_SERVER['REQUEST_TIME_FLOAT'] * 1000),
            'atlas_version' => '1.0.0',
            'total_components' => $this->countTotalComponents($data),
            'diagram_image_base64' => $diagramImageBase64,
            'use_static_diagram' => $this->config('use_static_diagram', true),
        ];

        // Always use Blade renderer for PDF templates
        return $this->renderWithBlade($templatePath, $templateData);
    }

    /**
     * Check if template is a Blade template
     */
    protected function isBladeTemplate(string $templatePath): bool
    {
        // Always use Blade for PDF templates as they contain Blade directives
        return true;
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
     * Get the path to the PDF template
     */
    protected function getTemplatePath(): string
    {
        /** @var string|null $customTemplate */
        $customTemplate = $this->config('template_path');

        if ($customTemplate && file_exists($customTemplate)) {
            return $customTemplate;
        }

        // Default template path relative to package root
        return __DIR__ . '/../../stubs/pdf-template.html';
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension(): string
    {
        return 'pdf';
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType(): string
    {
        return 'application/pdf';
    }

    /**
     * Generate a static diagram image in base64 format
     * 
     * @param array<string, mixed> $data Analysis data
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
        
        // Convertir en base64 pour intégration dans le PDF
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
     *
     * @return array<string, mixed>
     */
    protected function getDefaultConfig(): array
    {
        return [
            'title' => 'Laravel Atlas Architecture Map',
            'font' => 'DejaVu Sans',
            'paper_size' => 'A4',
            'orientation' => 'portrait',
            'remote_enabled' => false,
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

        foreach ($data as $componentType => $components) {
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
