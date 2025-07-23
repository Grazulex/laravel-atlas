<?php

declare(strict_types=1);

namespace LaravelAtlas\Exporters;

use InvalidArgumentException;
use LaravelAtlas\Exporters\Html\HtmlLayoutExporter;
use LaravelAtlas\Exporters\Json\JsonExporter;
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
            default => throw new InvalidArgumentException("Unsupported export format: $format"),
        };
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public static function exportAll(string $format, array $options = []): string
    {
        // Récupérer tous les types de composants disponibles
        $types = ['models', 'commands', 'routes']; // Pour l'instant, seuls les models sont supportés
        $allData = [];

        foreach ($types as $type) {
            $allData[$type] = Atlas::scan($type, $options);
        }

        return match ($format) {
            'html' => (new HtmlLayoutExporter)->render($allData),
            'json' => (new JsonExporter)->render($allData),
            default => throw new InvalidArgumentException("Unsupported export format: $format"),
        };
    }
}
