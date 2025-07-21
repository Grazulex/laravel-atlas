<?php

declare(strict_types=1);

namespace LaravelAtlas\Support;

use Illuminate\View\Factory;
use Dompdf\Dompdf;
use Dompdf\Options;
use League\HTMLToMarkdown\HtmlConverter;
use RuntimeException;

class DependencyChecker
{
    /**
     * Check if Blade view engine is available for HTML/PDF exports
     */
    public static function checkBlade(string $context = 'export'): void
    {
        if (! class_exists(Factory::class)) {
            throw new RuntimeException(
                "Laravel Blade view engine is required for {$context}. Install it with: composer require illuminate/view"
            );
        }
    }

    /**
     * Check if Dompdf is available for PDF exports
     *
     * @return bool Returns true if Dompdf is available, false otherwise
     */
    public static function checkDompdf(): bool
    {
        return class_exists(Dompdf::class) && class_exists(Options::class);
    }

    /**
     * Check if HTML to Markdown converter is available
     */
    public static function checkHtmlToMarkdown(): void
    {
        if (! class_exists(HtmlConverter::class)) {
            throw new RuntimeException(
                'HTML to Markdown converter is required. Install it with: composer require league/html-to-markdown'
            );
        }
    }

    /**
     * Get available export formats based on installed dependencies
     *
     * @return array<int, string>
     */
    public static function getAvailableFormats(): array
    {
        $formats = ['json', 'markdown', 'mermaid'];

        // Blade is included by default in Laravel, so HTML should always be available
        $formats[] = 'html';

        if (class_exists(Dompdf::class)) {
            $formats[] = 'pdf';
        }

        return $formats;
    }

    /**
     * Get missing dependencies for a specific format
     *
     * @return array<int, string>
     */
    public static function getMissingDependencies(string $format): array
    {
        $missing = [];

        switch ($format) {
            case 'html':
                // Blade is included by default, no additional dependencies needed
                break;

            case 'pdf':
                if (! class_exists(Dompdf::class)) {
                    $missing[] = 'dompdf/dompdf';
                }
                break;
        }

        return $missing;
    }

    /**
     * Get installation command for missing dependencies
     *
     * @param  array<int, string>  $dependencies
     */
    public static function getInstallCommand(array $dependencies): string
    {
        if ($dependencies === []) {
            return '';
        }

        return 'composer require ' . implode(' ', $dependencies);
    }
}
