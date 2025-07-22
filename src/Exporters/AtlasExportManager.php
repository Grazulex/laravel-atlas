<?php

declare(strict_types=1);

namespace LaravelAtlas\Exporters;

use LaravelAtlas\Facades\Atlas;
use LaravelAtlas\Exporters\Html\HtmlLayoutExporter;
use LaravelAtlas\Exporters\Json\JsonExporter;
use InvalidArgumentException;

class AtlasExportManager
{
    public static function export(string $type, string $format, array $options = []): string
    {
        $data = Atlas::scan($type, $options);

        return match ($format) {
            'html' => (new HtmlLayoutExporter())->render([
                $type => $data,
            ]),
            'json' => (new JsonExporter())->render($data),
            default => throw new InvalidArgumentException("Unsupported export format: $format"),
        };
    }
}
