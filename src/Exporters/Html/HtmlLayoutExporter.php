<?php

declare(strict_types=1);

namespace LaravelAtlas\Exporters\Html;

use Illuminate\Support\Facades\View;

class HtmlLayoutExporter
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function render(array $data): string
    {
        return View::make('atlas::exports.layout', [
            'models' => $data,
            'commands' => [], // à remplir plus tard
            'services' => [], // à remplir plus tard
        ])->render();
    }
}
