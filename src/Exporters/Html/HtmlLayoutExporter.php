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
        $formRequestsData = [];
        $eventsData = [];
        $controllersData = [];
        $resourcesData = [];
        $jobsData = [];
        $actionsData = [];
        $policiesData = [];
        $rulesData = [];
        $listenersData = [];
        $observersData = [];

        if (isset($data['models']) && is_array($data['models']) && isset($data['models']['data'])) {
            $modelsData = $data['models']['data'];
        }

        if (isset($data['commands']) && is_array($data['commands']) && isset($data['commands']['data'])) {
            $commandsData = $data['commands']['data'];
        }

        if (isset($data['routes']) && is_array($data['routes']) && isset($data['routes']['data'])) {
            $routesData = $data['routes']['data'];
        }

        $routesGrouping = [];
        if (isset($data['routes']) && is_array($data['routes']) && isset($data['routes']['grouping'])) {
            $routesGrouping = $data['routes']['grouping'];
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

        if (isset($data['form_requests']) && is_array($data['form_requests']) && isset($data['form_requests']['data'])) {
            $formRequestsData = $data['form_requests']['data'];
        }

        if (isset($data['events']) && is_array($data['events']) && isset($data['events']['data'])) {
            $eventsData = $data['events']['data'];
        }

        if (isset($data['controllers']) && is_array($data['controllers']) && isset($data['controllers']['data'])) {
            $controllersData = $data['controllers']['data'];
        }

        if (isset($data['resources']) && is_array($data['resources']) && isset($data['resources']['data'])) {
            $resourcesData = $data['resources']['data'];
        }

        if (isset($data['jobs']) && is_array($data['jobs']) && isset($data['jobs']['data'])) {
            $jobsData = $data['jobs']['data'];
        }

        if (isset($data['actions']) && is_array($data['actions']) && isset($data['actions']['data'])) {
            $actionsData = $data['actions']['data'];
        }

        if (isset($data['policies']) && is_array($data['policies']) && isset($data['policies']['data'])) {
            $policiesData = $data['policies']['data'];
        }

        if (isset($data['rules']) && is_array($data['rules']) && isset($data['rules']['data'])) {
            $rulesData = $data['rules']['data'];
        }

        if (isset($data['listeners']) && is_array($data['listeners']) && isset($data['listeners']['data'])) {
            $listenersData = $data['listeners']['data'];
        }

        if (isset($data['observers']) && is_array($data['observers']) && isset($data['observers']['data'])) {
            $observersData = $data['observers']['data'];
        }

        // Get project information from composer.json
        $projectName = 'Laravel Project';
        $projectDescription = 'Atlas - Code Architecture';
        $createdAt = date('d/m/Y H:i');

        $composerPath = base_path('composer.json');
        if (file_exists($composerPath)) {
            $composerContent = file_get_contents($composerPath);
            if ($composerContent !== false) {
                $composer = json_decode($composerContent, true);
                if (is_array($composer)) {
                    if (isset($composer['name'])) {
                        $projectName = $composer['name'];
                    }
                    if (isset($composer['description'])) {
                        $projectDescription = 'Atlas - ' . $composer['description'];
                    }
                }
            }
        }

        return View::make('atlas::exports.layout', [
            'models' => $modelsData,
            'commands' => $commandsData,
            'routes' => $routesData,
            'routes_grouping' => $routesGrouping,
            'services' => $servicesData,
            'notifications' => $notificationsData,
            'middlewares' => $middlewaresData,
            'form_requests' => $formRequestsData,
            'events' => $eventsData,
            'controllers' => $controllersData,
            'resources' => $resourcesData,
            'jobs' => $jobsData,
            'actions' => $actionsData,
            'policies' => $policiesData,
            'rules' => $rulesData,
            'listeners' => $listenersData,
            'observers' => $observersData,
            'project_name' => $projectName,
            'project_description' => $projectDescription,
            'created_at' => $createdAt,
        ])->render();
    }
}
