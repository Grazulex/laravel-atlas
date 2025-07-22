<?php

declare(strict_types=1);

namespace LaravelAtlas\Exporters\Html;

use Illuminate\Support\Facades\View;

class ModelHtmlExporter
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function render(array $data): string
    {
        return View::make('atlas::exports.models', [
            'models' => $data,
        ])->render();
    }
}
