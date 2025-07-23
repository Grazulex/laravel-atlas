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
        $commandsData = [];
        $servicesData = [];

        if (isset($data['models']) && is_array($data['models']) && isset($data['models']['data'])) {
            $modelsData = $data['models']['data'];
        }

        if (isset($data['commands']) && is_array($data['commands']) && isset($data['commands']['data'])) {
            $commandsData = $data['commands']['data'];
        }

        if (isset($data['services']) && is_array($data['services']) && isset($data['services']['data'])) {
            $servicesData = $data['services']['data'];
        }

        return View::make('atlas::exports.layout', [
            'models' => $modelsData,
            'commands' => $commandsData,
            'services' => $servicesData,
        ])->render();
    }
}
