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
        // Extraire les données selon le type
        $modelsData = [];
        
        if (isset($data['models']) && is_array($data['models']) && isset($data['models']['data'])) {
            $modelsData = $data['models']['data'];
        }
        
        return View::make('atlas::exports.layout', [
            'models' => $modelsData,
            'commands' => [], // à remplir plus tard
            'services' => [], // à remplir plus tard
        ])->render();
    }
}
