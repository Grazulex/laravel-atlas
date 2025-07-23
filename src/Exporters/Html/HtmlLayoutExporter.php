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
        $routesData = [];
        $servicesData = [];
        $notificationsData = [];
        $middlewaresData = [];

        if (isset($data['models']) && is_array($data['models']) && isset($data['models']['data'])) {
            $modelsData = $data['models']['data'];
        }

        if (isset($data['commands']) && is_array($data['commands']) && isset($data['commands']['data'])) {
            $commandsData = $data['commands']['data'];
        }

        if (isset($data['routes']) && is_array($data['routes']) && isset($data['routes']['data'])) {
            $routesData = $data['routes']['data'];
        }

        if (isset($data['services']) && is_array($data['services']) && isset($data['services']['data'])) {
            $servicesData = $data['services']['data'];
        }

        if (isset($data['notifications']) && is_array($data['notifications']) && isset($data['notifications']['data'])) {
            $notificationsData = $data['notifications']['data'];
        }

        if (isset($data['middlewares']) && is_array($data['middlewares']) && isset($data['middlewares']['data'])) {
            $middlewaresData = $data['middlewares']['data'];
        }

        return View::make('atlas::exports.layout', [
            'models' => $modelsData,
            'commands' => $commandsData,
            'routes' => $routesData,
            'services' => $servicesData,
            'notifications' => $notificationsData,
            'middlewares' => $middlewaresData,
        ])->render();
    }
}
