<?php

declare(strict_types=1);

namespace LaravelAtlas\Exporters;

use InvalidArgumentException;
use LaravelAtlas\Exporters\Blade\BladeLayoutExporter;
use LaravelAtlas\Exporters\Html\HtmlLayoutExporter;
use LaravelAtlas\Exporters\Json\JsonExporter;
use LaravelAtlas\Exporters\Pdf\PdfLayoutExporter;
use LaravelAtlas\Facades\Atlas;

class AtlasExportManager
{
    /**
     * @param  array<string, mixed>  $options
     */
    public static function export(string $type, string $format, array $options = []): string
    {
        $data = Atlas::scan($type, $options);

        return match ($format) {
            'html' => (new HtmlLayoutExporter)->render([
                $type => $data,
            ]),
            'json' => (new JsonExporter)->render($data),
            'pdf' => (new PdfLayoutExporter)->render([
                $type => $data,
            ]),
            'blade' => (new BladeLayoutExporter)->render([
                $type => $data,
            ]),
            default => throw new InvalidArgumentException("Unsupported export format: $format"),
        };
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public static function exportAll(string $format, array $options = []): string
    {
        // Récupérer tous les types de composants disponibles
        $types = [
            'models', 'commands', 'routes', 'services', 'notifications',
            'middlewares', 'form_requests', 'events', 'controllers',
            'resources', 'jobs', 'actions', 'policies', 'rules',
            'listeners', 'observers',
        ];
        $allData = [];

        foreach ($types as $type) {
            $allData[$type] = Atlas::scan($type, $options);
        }

        return match ($format) {
            'html' => (new HtmlLayoutExporter)->render($allData),
            'json' => (new JsonExporter)->render($allData),
            'pdf' => (new PdfLayoutExporter)->render($allData),
            'blade' => (new BladeLayoutExporter)->render($allData),
            default => throw new InvalidArgumentException("Unsupported export format: $format"),
        };
    }
}
