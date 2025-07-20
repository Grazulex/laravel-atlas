<?php

declare(strict_types=1);

namespace Grazulex\LaravelAtlas\Exporters;

use RuntimeException;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class HtmlExporter extends BaseExporter
{
    /**
     * {@inheritdoc}
     */
    public function export(array $data): string
    {
        $templatePath = $this->getTemplatePath();

        if (! file_exists($templatePath)) {
            throw new RuntimeException("HTML template not found at: {$templatePath}");
        }

        $template = file_get_contents($templatePath);

        $loader = new ArrayLoader(['html_template' => $template]);
        $twig = new Environment($loader);

        return $twig->render('html_template', [
            'data' => $data,
            'config' => $this->config,
            'title' => $this->config('title', 'Laravel Atlas Architecture Map'),
        ]);
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
        return __DIR__ . '/../../stubs/html-template.html';
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
