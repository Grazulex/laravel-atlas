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
        if (! class_exists(Dompdf::class) || ! class_exists(Options::class)) {
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
            throw new RuntimeException("Failed to read PDF template at: {$templatePath}");
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
        ];
    }
}
