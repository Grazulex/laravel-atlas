<?php

declare(strict_types=1);

namespace Grazulex\LaravelAtlas\Exporters;

use Dompdf\Dompdf;
use Dompdf\Options;
use RuntimeException;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class PdfExporter extends BaseExporter
{
    /**
     * {@inheritdoc}
     */
    public function export(array $data): string
    {
        $htmlContent = $this->generateHtmlContent($data);

        // Configure Dompdf
        $options = new Options;
        $options->set('defaultFont', $this->config('font', 'DejaVu Sans'));
        $options->set('isRemoteEnabled', $this->config('remote_enabled', false));
        $options->set('isHtml5ParserEnabled', true);

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

        $template = file_get_contents($templatePath);

        $loader = new ArrayLoader(['pdf_template' => $template]);
        $twig = new Environment($loader);

        return $twig->render('pdf_template', [
            'data' => $data,
            'config' => $this->config,
            'title' => $this->config('title', 'Laravel Atlas Architecture Map'),
        ]);
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
