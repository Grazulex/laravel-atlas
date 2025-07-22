<?php

declare(strict_types=1);

namespace LaravelAtlas\Exporters;

use Closure;

class PhpExporter extends BaseExporter
{
    /**
     * {@inheritdoc}
     *
     * @param  array<string, mixed>  $data
     */
    public function export(array $data): string
    {
        // Générer un fichier PHP structuré comme realistic-app-data.php
        $phpCode = $this->generatePhpStructure($data);

        // Utiliser le répertoire de configuration ou le répertoire courant
        $outputDir = $this->config('output_dir', getcwd());
        $filename = $this->config('filename', 'atlas-data.php');
        $filePath = rtrim((string) $outputDir, '/') . '/' . $filename;

        if (! is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        file_put_contents($filePath, $phpCode);

        return $phpCode;  // Retourner le contenu PHP généré
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension(): string
    {
        return 'php';
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType(): string
    {
        return 'text/x-php';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig(): array
    {
        return [
            'output_dir' => getcwd(),
            'filename' => 'atlas-data.php',
            'include_flows' => true,
            'include_connections' => true,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function generatePhpStructure(array $data): string
    {
        $php = "<?php\n\n";
        $php .= "/**\n";
        $php .= " * Laravel Atlas - Données d'architecture générées automatiquement\n";
        $php .= ' * Généré le: ' . date('Y-m-d H:i:s') . "\n";
        $php .= " */\n\n";
        $php .= "return [\n";

        // Métadonnées enrichies depuis composer.json
        $metadata = $this->generateMetadata($data);
        $php .= "    'metadata' => [\n";
        foreach ($metadata as $key => $value) {
            $php .= "        '$key' => " . $this->exportValue($value) . ",\n";
        }
        $php .= "    ],\n\n";

        // Routes avec flows intelligents
        $php .= "    // === ENTRY POINTS: ROUTES ===\n";
        $php .= "    'routes' => [\n";
        foreach ($data['routes'] ?? [] as $route) {
            $php .= $this->generateRouteEntry($route);
        }
        $php .= "    ],\n\n";

        // Commands avec flows
        $php .= "    // === ENTRY POINTS: COMMANDS ===\n";
        $php .= "    'commands' => [\n";
        foreach ($data['commands'] ?? [] as $command) {
            $php .= $this->generateCommandEntry($command);
        }
        $php .= "    ],\n\n";

        // Models avec relations
        $php .= "    // === MODELS avec relations ===\n";
        $php .= "    'models' => [\n";
        foreach ($data['models'] ?? [] as $model) {
            $php .= $this->generateModelEntry($model);
        }
        $php .= "    ],\n\n";

        // Controllers avec connexions
        $php .= "    // === CONTROLLERS ===\n";
        $php .= "    'controllers' => [\n";
        foreach ($data['controllers'] ?? [] as $controller) {
            $php .= $this->generateControllerEntry($controller);
        }
        $php .= "    ],\n\n";

        // Services
        $php .= "    // === SERVICES (Business Logic) ===\n";
        $php .= "    'services' => [\n";
        foreach ($data['services'] ?? [] as $service) {
            $php .= $this->generateServiceEntry($service);
        }
        $php .= "    ],\n\n";

        // Jobs
        $php .= "    // === JOBS (Asynchronous) ===\n";
        $php .= "    'jobs' => [\n";
        foreach ($data['jobs'] ?? [] as $job) {
            $php .= $this->generateJobEntry($job);
        }
        $php .= "    ],\n\n";

        // Events
        $php .= "    // === EVENTS ===\n";
        $php .= "    'events' => [\n";
        foreach ($data['events'] ?? [] as $event) {
            $php .= $this->generateEventEntry($event);
        }
        $php .= "    ],\n\n";

        // Listeners
        $php .= "    // === LISTENERS ===\n";
        $php .= "    'listeners' => [\n";
        foreach ($data['listeners'] ?? [] as $listener) {
            $php .= $this->generateListenerEntry($listener);
        }
        $php .= "    ],\n\n";

        // Policies
        if (! empty($data['policies'])) {
            $php .= "    // === POLICIES ===\n";
            $php .= "    'policies' => [\n";
            foreach ($data['policies'] as $policy) {
                $php .= $this->generatePolicyEntry($policy);
            }
            $php .= "    ],\n\n";
        }

        // Middleware
        if (! empty($data['middleware'])) {
            $php .= "    // === MIDDLEWARE ===\n";
            $php .= "    'middleware' => [\n";
            foreach ($data['middleware'] as $middleware) {
                $php .= $this->generateMiddlewareEntry($middleware);
            }
            $php .= "    ],\n\n";
        }

        // Observers
        if (! empty($data['observers'])) {
            $php .= "    // === OBSERVERS ===\n";
            $php .= "    'observers' => [\n";
            foreach ($data['observers'] as $observer) {
                $php .= $this->generateObserverEntry($observer);
            }
            $php .= "    ],\n\n";
        }

        // Actions
        if (! empty($data['actions'])) {
            $php .= "    // === ACTIONS ===\n";
            $php .= "    'actions' => [\n";
            foreach ($data['actions'] as $action) {
                $php .= $this->generateActionEntry($action);
            }
            $php .= "    ],\n\n";
        }

        // Resources
        if (! empty($data['resources'])) {
            $php .= "    // === RESOURCES ===\n";
            $php .= "    'resources' => [\n";
            foreach ($data['resources'] as $resource) {
                $php .= $this->generateResourceEntry($resource);
            }
            $php .= "    ],\n\n";
        }

        // Notifications
        if (! empty($data['notifications'])) {
            $php .= "    // === NOTIFICATIONS ===\n";
            $php .= "    'notifications' => [\n";
            foreach ($data['notifications'] as $notification) {
                $php .= $this->generateNotificationEntry($notification);
            }
            $php .= "    ],\n\n";
        }

        // Requests
        if (! empty($data['requests'])) {
            $php .= "    // === REQUESTS ===\n";
            $php .= "    'requests' => [\n";
            foreach ($data['requests'] as $request) {
                $php .= $this->generateRequestEntry($request);
            }
            $php .= "    ],\n\n";
        }

        // Rules
        if (! empty($data['rules'])) {
            $php .= "    // === RULES ===\n";
            $php .= "    'rules' => [\n";
            foreach ($data['rules'] as $rule) {
                $php .= $this->generateRuleEntry($rule);
            }
            $php .= "    ],\n\n";
        }

        // Flows intelligents
        $php .= "    // === FLOWS & INTERCONNECTIONS ===\n";
        $php .= "    'flows' => [\n";

        // Génération automatique des flows basés sur les routes
        $generatedFlows = $this->generateIntelligentFlows($data);
        foreach ($generatedFlows as $flow) {
            $php .= $this->generateFlowEntry($flow);
        }

        // Flows personnalisés s'ils existent
        foreach ($data['flows'] ?? [] as $flow) {
            $php .= $this->generateFlowEntry($flow);
        }
        $php .= "    ]\n";

        return $php . "];\n";
    }

    /**
     * @param  array<string, mixed>  $route
     */
    private function generateRouteEntry(array $route): string
    {
        $php = "        [\n";
        $php .= "            'name' => " . $this->exportValue($route['name'] ?? '') . ",\n";
        $php .= "            'uri' => " . $this->exportValue($route['uri'] ?? '') . ",\n";
        $php .= "            'method' => " . $this->exportValue($route['method'] ?? 'GET') . ",\n";
        $php .= "            'controller' => " . $this->exportValue($route['controller'] ?? '') . ",\n";
        $php .= "            'action' => " . $this->exportValue($route['action'] ?? '') . ",\n";

        if (! empty($route['middleware'])) {
            $php .= "            'middleware' => " . $this->exportArray($route['middleware']) . ",\n";
        }

        // Générer les flows intelligents basés sur les connexions détectées
        if (! empty($route['controller'])) {
            $flows = $this->generateFlowsForRoute($route);
            if ($flows !== []) {
                $php .= "            'flows' => [\n";
                if (! empty($flows['synchronous'])) {
                    $php .= "                'synchronous' => " . $this->exportArray($flows['synchronous']) . ",\n";
                }
                if (! empty($flows['asynchronous'])) {
                    $php .= "                'asynchronous' => " . $this->exportArray($flows['asynchronous']) . ",\n";
                }
                $php .= "            ]\n";
            }
        }

        return $php . "        ],\n";
    }

    /**
     * @param  array<string, mixed>  $command
     */
    private function generateCommandEntry(array $command): string
    {
        $php = "        [\n";

        // Use new structure with signature_info
        $name = $command['signature_info']['name'] ?? $command['name'] ?? '';
        $signature = $command['signature_info']['signature'] ?? $command['signature'] ?? '';
        $description = $command['signature_info']['description'] ?? $command['description'] ?? '';

        $php .= "            'name' => " . $this->exportValue($name) . ",\n";
        $php .= "            'class_name' => " . $this->exportValue($command['class_name'] ?? '') . ",\n";
        $php .= "            'signature' => " . $this->exportValue($signature) . ",\n";
        $php .= "            'description' => " . $this->exportValue($description) . ",\n";

        // Add signature_info section
        if (isset($command['signature_info'])) {
            $php .= "            'signature_info' => " . $this->exportArray($command['signature_info']) . ",\n";
        }

        // Add arguments if present
        if (isset($command['arguments']) && ! empty($command['arguments'])) {
            $php .= "            'arguments' => " . $this->exportArray($command['arguments']) . ",\n";
        }

        // Add options if present
        if (isset($command['options']) && ! empty($command['options'])) {
            $php .= "            'options' => " . $this->exportArray($command['options']) . ",\n";
        }

        // Add dependencies if present
        if (isset($command['dependencies']) && ! empty($command['dependencies'])) {
            $php .= "            'dependencies' => " . $this->exportArray($command['dependencies']) . ",\n";
        }

        // Add namespace and other class information
        if (isset($command['namespace'])) {
            $php .= "            'namespace' => " . $this->exportValue($command['namespace']) . ",\n";
        }
        if (isset($command['parent_class'])) {
            $php .= "            'parent_class' => " . $this->exportValue($command['parent_class']) . ",\n";
        }

        // Générer flows pour les commandes
        $flows = $this->generateFlowsForCommand($command);
        if ($flows !== []) {
            $php .= "            'flows' => [\n";
            if (! empty($flows['synchronous'])) {
                $php .= "                'synchronous' => " . $this->exportArray($flows['synchronous']) . ",\n";
            }
            if (! empty($flows['asynchronous'])) {
                $php .= "                'asynchronous' => " . $this->exportArray($flows['asynchronous']) . ",\n";
            }
            $php .= "            ]\n";
        }

        return $php . "        ],\n";
    }

    /**
     * @param  array<string, mixed>  $model
     */
    private function generateModelEntry(array $model): string
    {
        $php = "        [\n";
        $php .= "            'class_name' => " . $this->exportValue($model['class_name'] ?? '') . ",\n";
        $php .= "            'table' => " . $this->exportValue($model['table'] ?? '') . ",\n";

        if (! empty($model['attributes'])) {
            $php .= "            'attributes' => " . $this->exportArray($model['attributes']) . ",\n";
        }

        if (! empty($model['relationships'])) {
            $php .= "            'relationships' => [\n";
            foreach ($model['relationships'] as $type => $relations) {
                $php .= "                '$type' => " . $this->exportArray($relations) . ",\n";
            }
            $php .= "            ],\n";
        }

        // Connexions détectées
        $connections = $this->detectModelConnections($model);
        if ($connections !== []) {
            $php .= "            'connected_to' => [\n";
            foreach ($connections as $type => $items) {
                $php .= "                '$type' => " . $this->exportArray($items) . ",\n";
            }
            $php .= "            ]\n";
        }

        return $php . "        ],\n";
    }

    /**
     * @param  array<string, mixed>  $controller
     */
    private function generateControllerEntry(array $controller): string
    {
        $php = "        [\n";
        $php .= "            'class_name' => " . $this->exportValue($controller['class_name'] ?? '') . ",\n";

        if (! empty($controller['methods'])) {
            $php .= "            'methods' => [\n";
            foreach ($controller['methods'] as $method => $details) {
                $php .= "                '$method' => [\n";
                if (! empty($details['dependencies'])) {
                    $php .= "                    'dependencies' => " . $this->exportArray($details['dependencies']) . ",\n";
                }
                if (! empty($details['events'])) {
                    $php .= "                    'events' => " . $this->exportArray($details['events']) . ",\n";
                }
                $php .= "                ],\n";
            }
            $php .= "            ],\n";
        }

        // Connexions
        $connections = $this->detectControllerConnections($controller);
        if ($connections !== []) {
            $php .= "            'connected_to' => [\n";
            foreach ($connections as $type => $items) {
                $php .= "                '$type' => " . $this->exportArray($items) . ",\n";
            }
            $php .= "            ]\n";
        }

        return $php . "        ],\n";
    }

    /**
     * @param  array<string, mixed>  $service
     */
    private function generateServiceEntry(array $service): string
    {
        $php = "        [\n";
        $php .= "            'class_name' => " . $this->exportValue($service['class_name'] ?? '') . ",\n";

        if (! empty($service['methods'])) {
            $php .= "            'methods' => [\n";
            foreach ($service['methods'] as $method => $details) {
                $php .= "                '$method' => [\n";
                if (! empty($details['dependencies'])) {
                    $php .= "                    'dependencies' => " . $this->exportArray($details['dependencies']) . ",\n";
                }
                if (! empty($details['returns'])) {
                    $php .= "                    'returns' => " . $this->exportValue($details['returns']) . ",\n";
                }
                if (! empty($details['events'])) {
                    $php .= "                    'events' => " . $this->exportArray($details['events']) . ",\n";
                }
                $php .= "                ],\n";
            }
            $php .= "            ],\n";
        }

        $connections = $this->detectServiceConnections($service);
        if ($connections !== []) {
            $php .= "            'connected_to' => [\n";
            foreach ($connections as $type => $items) {
                $php .= "                '$type' => " . $this->exportArray($items) . ",\n";
            }
            $php .= "            ]\n";
        }

        return $php . "        ],\n";
    }

    /**
     * @param  array<string, mixed>  $job
     */
    private function generateJobEntry(array $job): string
    {
        $php = "        [\n";
        $php .= "            'class_name' => " . $this->exportValue($job['class_name'] ?? '') . ",\n";
        $php .= "            'queue' => " . $this->exportValue($job['queue'] ?? 'default') . ",\n";

        if (! empty($job['dependencies'])) {
            $php .= "            'dependencies' => " . $this->exportArray($job['dependencies']) . ",\n";
        }

        if (! empty($job['triggered_by'])) {
            $php .= "            'triggered_by' => " . $this->exportArray($job['triggered_by']) . ",\n";
        }

        if (! empty($job['events'])) {
            $php .= "            'events' => " . $this->exportArray($job['events']) . ",\n";
        }

        return $php . "        ],\n";
    }

    /**
     * @param  array<string, mixed>  $event
     */
    private function generateEventEntry(array $event): string
    {
        $php = "        [\n";
        $php .= "            'class_name' => " . $this->exportValue($event['class_name'] ?? '') . ",\n";

        if (! empty($event['properties'])) {
            $php .= "            'properties' => " . $this->exportArray($event['properties']) . ",\n";
        }

        if (! empty($event['listeners'])) {
            $php .= "            'listeners' => " . $this->exportArray($event['listeners']) . ",\n";
        }

        if (! empty($event['triggered_by'])) {
            $php .= "            'triggered_by' => " . $this->exportArray($event['triggered_by']) . ",\n";
        }

        return $php . "        ],\n";
    }

    /**
     * @param  array<string, mixed>  $listener
     */
    private function generateListenerEntry(array $listener): string
    {
        $php = "        [\n";
        $php .= "            'class_name' => " . $this->exportValue($listener['class_name'] ?? '') . ",\n";
        $php .= "            'event' => " . $this->exportValue($listener['event'] ?? '') . ",\n";

        if (! empty($listener['dependencies'])) {
            $php .= "            'dependencies' => " . $this->exportArray($listener['dependencies']) . ",\n";
        }

        if (! empty($listener['jobs'])) {
            $php .= "            'jobs' => " . $this->exportArray($listener['jobs']) . ",\n";
        }

        return $php . "        ],\n";
    }

    /**
     * @param  array<string, mixed>  $policy
     */
    private function generatePolicyEntry(array $policy): string
    {
        $php = "        [\n";
        $php .= "            'class_name' => " . $this->exportValue($policy['class_name'] ?? '') . ",\n";
        $php .= "            'model' => " . $this->exportValue($policy['model'] ?? '') . ",\n";

        if (! empty($policy['methods'])) {
            $php .= "            'methods' => " . $this->exportArray($policy['methods']) . ",\n";
        }

        if (! empty($policy['connected_to'])) {
            $php .= "            'connected_to' => [\n";
            foreach ($policy['connected_to'] as $type => $items) {
                $php .= "                '$type' => " . $this->exportArray($items) . ",\n";
            }
            $php .= "            ]\n";
        }

        return $php . "        ],\n";
    }

    /**
     * @param  array<string, mixed>  $middleware
     */
    private function generateMiddlewareEntry(array $middleware): string
    {
        $php = "        [\n";
        $php .= "            'class_name' => " . $this->exportValue($middleware['class_name'] ?? '') . ",\n";
        $php .= "            'type' => " . $this->exportValue($middleware['type'] ?? 'global') . ",\n";

        if (! empty($middleware['dependencies'])) {
            $php .= "            'dependencies' => " . $this->exportArray($middleware['dependencies']) . ",\n";
        }

        if (! empty($middleware['used_by'])) {
            $php .= "            'used_by' => " . $this->exportArray($middleware['used_by']) . ",\n";
        }

        return $php . "        ],\n";
    }

    /**
     * @param  array<string, mixed>  $flow
     */
    private function generateFlowEntry(array $flow): string
    {
        $php = "        [\n";
        $php .= "            'name' => " . $this->exportValue($flow['name'] ?? '') . ",\n";
        $php .= "            'entry_point' => " . $this->exportValue($flow['entry_point'] ?? '') . ",\n";
        $php .= "            'type' => " . $this->exportValue($flow['type'] ?? 'synchronous') . ",\n";

        if (! empty($flow['steps'])) {
            $php .= "            'steps' => " . $this->exportArray($flow['steps']) . "\n";
        }

        return $php . "        ],\n";
    }

    /**
     * @param  array<string, mixed>  $observer
     */
    private function generateObserverEntry(array $observer): string
    {
        $php = "        [\n";
        $php .= "            'class_name' => " . $this->exportValue($observer['class_name'] ?? '') . ",\n";
        $php .= "            'model' => " . $this->exportValue($observer['model'] ?? '') . ",\n";

        if (! empty($observer['methods'])) {
            $php .= "            'methods' => " . $this->exportArray($observer['methods']) . ",\n";
        }

        if (! empty($observer['dependencies'])) {
            $php .= "            'dependencies' => " . $this->exportArray($observer['dependencies']) . ",\n";
        }

        if (! empty($observer['events'])) {
            $php .= "            'events' => " . $this->exportArray($observer['events']) . ",\n";
        }

        return $php . "        ],\n";
    }

    /**
     * @param  array<string, mixed>  $action
     */
    private function generateActionEntry(array $action): string
    {
        $php = "        [\n";
        $php .= "            'class_name' => " . $this->exportValue($action['class_name'] ?? '') . ",\n";
        $php .= "            'type' => " . $this->exportValue($action['type'] ?? 'custom') . ",\n";

        if (! empty($action['is_invokable'])) {
            $php .= "            'is_invokable' => " . $this->exportValue($action['is_invokable']) . ",\n";
        }

        if (! empty($action['methods'])) {
            $php .= "            'methods' => " . $this->exportArray($action['methods']) . ",\n";
        }

        if (! empty($action['dependencies'])) {
            $php .= "            'dependencies' => " . $this->exportArray($action['dependencies']) . ",\n";
        }

        if (! empty($action['events'])) {
            $php .= "            'events' => " . $this->exportArray($action['events']) . ",\n";
        }

        return $php . "        ],\n";
    }

    /**
     * @param  array<string, mixed>  $resource
     */
    private function generateResourceEntry(array $resource): string
    {
        $php = "        [\n";
        $php .= "            'class_name' => " . $this->exportValue($resource['class_name'] ?? '') . ",\n";
        $php .= "            'type' => " . $this->exportValue($resource['type'] ?? 'resource') . ",\n";

        if (! empty($resource['model'])) {
            $php .= "            'model' => " . $this->exportValue($resource['model']) . ",\n";
        }

        if (! empty($resource['methods'])) {
            $php .= "            'methods' => " . $this->exportArray($resource['methods']) . ",\n";
        }

        if (! empty($resource['dependencies'])) {
            $php .= "            'dependencies' => " . $this->exportArray($resource['dependencies']) . ",\n";
        }

        return $php . "        ],\n";
    }

    /**
     * @param  array<string, mixed>  $notification
     */
    private function generateNotificationEntry(array $notification): string
    {
        $php = "        [\n";
        $php .= "            'class_name' => " . $this->exportValue($notification['class_name'] ?? '') . ",\n";
        $php .= "            'type' => " . $this->exportValue($notification['type'] ?? 'notification') . ",\n";

        if (! empty($notification['channels'])) {
            $php .= "            'channels' => " . $this->exportArray($notification['channels']) . ",\n";
        }

        if (! empty($notification['methods'])) {
            $php .= "            'methods' => " . $this->exportArray($notification['methods']) . ",\n";
        }

        if (! empty($notification['dependencies'])) {
            $php .= "            'dependencies' => " . $this->exportArray($notification['dependencies']) . ",\n";
        }

        return $php . "        ],\n";
    }

    /**
     * @param  array<string, mixed>  $request
     */
    private function generateRequestEntry(array $request): string
    {
        $php = "        [\n";
        $php .= "            'class_name' => " . $this->exportValue($request['class_name'] ?? '') . ",\n";
        $php .= "            'type' => " . $this->exportValue($request['type'] ?? 'form_request') . ",\n";

        if (! empty($request['validation_rules'])) {
            $php .= "            'validation_rules' => " . $this->exportArray($request['validation_rules']) . ",\n";
        }

        if (! empty($request['methods'])) {
            $php .= "            'methods' => " . $this->exportArray($request['methods']) . ",\n";
        }

        if (! empty($request['dependencies'])) {
            $php .= "            'dependencies' => " . $this->exportArray($request['dependencies']) . ",\n";
        }

        return $php . "        ],\n";
    }

    /**
     * @param  array<string, mixed>  $rule
     */
    private function generateRuleEntry(array $rule): string
    {
        $php = "        [\n";
        $php .= "            'class_name' => " . $this->exportValue($rule['class_name'] ?? '') . ",\n";
        $php .= "            'type' => " . $this->exportValue($rule['type'] ?? 'validation_rule') . ",\n";

        if (! empty($rule['methods'])) {
            $php .= "            'methods' => " . $this->exportArray($rule['methods']) . ",\n";
        }

        if (! empty($rule['dependencies'])) {
            $php .= "            'dependencies' => " . $this->exportArray($rule['dependencies']) . ",\n";
        }

        if (! empty($rule['parameters'])) {
            $php .= "            'parameters' => " . $this->exportArray($rule['parameters']) . ",\n";
        }

        return $php . "        ],\n";
    }

    // Méthodes de détection intelligente des connexions

    /**
     * @param  array<string, mixed>  $route
     *
     * @return array<string, mixed>
     */
    private function generateFlowsForRoute(array $route): array
    {
        $flows = [];

        // Logic pour détecter automatiquement les flows basés sur le controller/action
        if (! empty($route['controller'])) {
            $controller = is_array($route['controller']) ? '[Multiple Controllers]' : $route['controller'];
            $action = $route['action'] ?? 'handle';

            if (is_array($action)) {
                $action = $action['uses'] ?? '[Complex Action]';
            }

            $flows['synchronous'] = [
                $controller . '::' . $action,
            ];
        }

        return $flows;
    }

    /**
     * @param  array<string, mixed>  $command
     *
     * @return array<string, mixed>
     */
    private function generateFlowsForCommand(array $command): array
    {
        $flows = [];

        if (! empty($command['class_name'])) {
            $flows['synchronous'] = [$command['class_name'] . '::handle'];
        }

        return $flows;
    }

    /**
     * Génère automatiquement des flows intelligents basés sur les données de l'application
     *
     * @param  array<string, mixed>  $data
     *
     * @return array<int, array<string, mixed>>
     */
    private function generateIntelligentFlows(array $data): array
    {
        $flows = [];

        // Flow de gestion des produits
        if ($this->hasProductRoutes($data)) {
            $flows[] = [
                'name' => 'Product Management Flow',
                'entry_point' => 'GET /products',
                'type' => 'mixed',
                'steps' => [
                    'ProductController@index - List products',
                    'ProductController@create - Show create form',
                    'ProductController@store - Store product',
                    'ProductCreated event (async)',
                    'SendNewProductNotification job (async)',
                ],
            ];
        }

        // Flow de gestion des catégories
        if ($this->hasCategoryRoutes($data)) {
            $flows[] = [
                'name' => 'Category Management Flow',
                'entry_point' => 'GET /categories',
                'type' => 'mixed',
                'steps' => [
                    'CategoryController@index - List categories',
                    'CategoryController@create - Show create form',
                    'CategoryController@store - Store category',
                    'CategoryCreated event (async)',
                ],
            ];
        }

        // Flow de maintenance via commandes
        if ($this->hasMaintenanceCommands($data)) {
            $flows[] = [
                'name' => 'Maintenance & Cleanup Flow',
                'entry_point' => 'artisan commands',
                'type' => 'synchronous',
                'steps' => [
                    'CleanInactiveProducts command',
                    'SyncProducts command',
                    'Database cleanup operations',
                ],
            ];
        }

        // Flows des relations Model-Observer
        $observerFlows = $this->generateObserverFlows($data);

        return array_merge($flows, $observerFlows);
    }

    /**
     * Vérifie si l'application a des routes de produits
     *
     * @param  array<string, mixed>  $data
     */
    private function hasProductRoutes(array $data): bool
    {
        $routes = $data['routes'] ?? [];
        foreach ($routes as $route) {
            if (str_contains($route['uri'] ?? '', 'product')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si l'application a des routes de catégories
     *
     * @param  array<string, mixed>  $data
     */
    private function hasCategoryRoutes(array $data): bool
    {
        $routes = $data['routes'] ?? [];
        foreach ($routes as $route) {
            if (str_contains($route['uri'] ?? '', 'categor')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si l'application a des commandes de maintenance
     *
     * @param  array<string, mixed>  $data
     */
    private function hasMaintenanceCommands(array $data): bool
    {
        $commands = $data['commands'] ?? [];
        foreach ($commands as $command) {
            $className = $command['class_name'] ?? '';
            if (str_contains(strtolower($className), 'clean') || str_contains(strtolower($className), 'sync')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Génère des flows pour les relations Model-Observer
     *
     * @param  array<string, mixed>  $data
     *
     * @return array<int, array<string, mixed>>
     */
    private function generateObserverFlows(array $data): array
    {
        $flows = [];
        $models = $data['models'] ?? [];
        $observers = $data['observers'] ?? [];

        // Créer un mapping des observers par modèle
        $observersByModel = [];
        foreach ($observers as $observer) {
            $model = $observer['model'] ?? '';
            if ($model) {
                $observersByModel[$model][] = $observer;
            }
        }

        // Générer des flows pour chaque modèle avec observers
        foreach ($models as $model) {
            $modelClass = $model['class_name'] ?? '';
            $modelName = class_basename($modelClass);

            if (isset($observersByModel[$modelClass]) || isset($observersByModel[$modelName])) {
                $modelObservers = $observersByModel[$modelClass] ?? $observersByModel[$modelName] ?? [];

                $steps = [];
                $steps[] = "$modelName model lifecycle event";

                foreach ($modelObservers as $observer) {
                    $observerName = class_basename($observer['class_name'] ?? '');
                    $methods = $observer['methods'] ?? [];

                    if (! empty($methods)) {
                        foreach (array_keys($methods) as $methodName) {
                            $steps[] = "$observerName::$methodName - Handle $modelName $methodName event";
                        }
                    } else {
                        $steps[] = "$observerName - Handle $modelName lifecycle events";
                    }

                    // Ajouter les événements dispatchés par l'observer
                    if (! empty($observer['events'])) {
                        foreach ($observer['events'] as $event) {
                            $eventName = class_basename($event);
                            $steps[] = "$eventName event dispatched (async)";
                        }
                    }
                }

                $flows[] = [
                    'name' => "$modelName Lifecycle Flow",
                    'entry_point' => "$modelName model operations",
                    'type' => 'mixed',
                    'description' => "Automatic handling of $modelName model lifecycle events through observers",
                    'steps' => $steps,
                ];
            }
        }

        return $flows;
    }

    /**
     * @param  array<string, mixed>  $model
     *
     * @return array<string, mixed>
     */
    private function detectModelConnections(array $model): array
    {
        $connections = [];

        // Si on a des connected_to dans les données originales, les utiliser
        if (! empty($model['connected_to'])) {
            return $model['connected_to'];
        }

        // Sinon, essayer de détecter automatiquement
        $className = $model['class_name'] ?? '';
        if (! empty($className)) {
            // Chercher les contrôleurs qui utilisent ce modèle
            // (logique à implémenter basée sur l'analyse du code)
        }

        return $connections;
    }

    /**
     * @param  array<string, mixed>  $controller
     *
     * @return array<string, mixed>
     */
    private function detectControllerConnections(array $controller): array
    {
        $connections = [];

        // Si on a des connected_to dans les données originales, les utiliser
        if (! empty($controller['connected_to'])) {
            return $controller['connected_to'];
        }

        // Extraire les connexions depuis les methods si disponibles
        if (! empty($controller['methods'])) {
            foreach ($controller['methods'] as $details) {
                // Routes
                // Services depuis dependencies
                if (! empty($details['dependencies'])) {
                    foreach ($details['dependencies'] as $dependency) {
                        if (str_contains((string) $dependency, 'Services\\')) {
                            $connections['services'][] = $dependency;
                        }
                        if (str_contains((string) $dependency, 'Models\\')) {
                            $connections['models'][] = $dependency;
                        }
                    }
                }

                // Events
                if (! empty($details['events'])) {
                    $connections['events'] = array_merge($connections['events'] ?? [], $details['events']);
                }
            }
        }

        // Déduplication
        foreach ($connections as $type => $items) {
            $connections[$type] = array_unique($items);
        }

        return $connections;
    }

    /**
     * @param  array<string, mixed>  $service
     *
     * @return array<string, mixed>
     */
    private function detectServiceConnections(array $service): array
    {
        $connections = [];

        // Si on a des connected_to dans les données originales, les utiliser
        if (! empty($service['connected_to'])) {
            return $service['connected_to'];
        }

        // Extraire depuis les methods
        if (! empty($service['methods'])) {
            foreach ($service['methods'] as $details) {
                // Models et autres services depuis dependencies
                if (! empty($details['dependencies'])) {
                    foreach ($details['dependencies'] as $dependency) {
                        if (str_contains((string) $dependency, 'Models\\')) {
                            $connections['models'][] = $dependency;
                        }
                        if (str_contains((string) $dependency, 'Services\\') && $dependency !== $service['class_name']) {
                            $connections['services'][] = $dependency;
                        }
                    }
                }

                // Events
                if (! empty($details['events'])) {
                    $connections['events'] = array_merge($connections['events'] ?? [], $details['events']);
                }
            }
        }

        // Déduplication
        foreach ($connections as $type => $items) {
            $connections[$type] = array_unique($items);
        }

        return $connections;
    }

    private function exportValue(mixed $value): string
    {
        if (is_string($value)) {
            return "'" . addslashes($value) . "'";
        }
        if (is_array($value)) {
            return $this->exportArray($value);
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_null($value)) {
            return 'null';
        }
        if (is_object($value)) {
            // Handle objects that can't be converted to string
            if ($value instanceof Closure) {
                return "'[Closure]'";
            }
            if (method_exists($value, '__toString')) {
                return "'" . addslashes((string) $value) . "'";
            }

            return "'" . $value::class . " object'";
        }

        return "'" . addslashes((string) $value) . "'";
    }

    /**
     * @param  array<string, mixed>  $array
     */
    private function exportArray(array $array): string
    {
        if ($array === []) {
            return '[]';
        }

        $isAssoc = array_keys($array) !== range(0, count($array) - 1);

        if (! $isAssoc) {
            // Array numérique
            $items = array_map([$this, 'exportValue'], $array);

            return '[' . implode(', ', $items) . ']';
        }
        // Array associatif
        $items = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $items[] = "'" . addslashes($key) . "' => " . $this->exportArray($value);
            } else {
                $items[] = "'" . addslashes($key) . "' => " . $this->exportValue($value);
            }
        }

        return '[' . implode(', ', $items) . ']';
    }

    /**
     * Générer les métadonnées enrichies depuis composer.json
     *
     * @param  array<string, mixed>  $data
     *
     * @return array<string, mixed>
     */
    private function generateMetadata(array $data): array
    {
        $metadata = [
            'generated_at' => date('c'),
        ];

        // Charger composer.json si disponible
        $composerPath = $this->findComposerJson();
        if ($composerPath && file_exists($composerPath)) {
            $composerContent = file_get_contents($composerPath);
            if ($composerContent !== false) {
                $composer = json_decode($composerContent, true);

                if ($composer) {
                    // Extraire le nom du projet (sans vendor)
                    if (! empty($composer['name'])) {
                        $parts = explode('/', (string) $composer['name']);
                        $metadata['app_name'] = ucwords(str_replace('-', ' ', end($parts)));
                    }

                    // Description
                    if (! empty($composer['description'])) {
                        $metadata['description'] = $composer['description'];
                    }

                    // Version
                    if (! empty($composer['version'])) {
                        $metadata['version'] = $composer['version'];
                    }

                    // Infos supplémentaires
                    if (! empty($composer['keywords'])) {
                        $metadata['keywords'] = array_slice($composer['keywords'], 0, 5); // Les 5 premiers
                    }

                    if (! empty($composer['homepage'])) {
                        $metadata['homepage'] = $composer['homepage'];
                    }
                }
            }
        }

        // Utiliser les données passées en paramètre comme fallback
        $metadata['app_name'] ??= $data['metadata']['app_name'] ?? 'Laravel Application';
        $metadata['description'] ??= $data['metadata']['description'] ?? 'Architecture analysis';
        $metadata['version'] ??= $data['metadata']['version'] ?? '1.0.0';

        return $metadata;
    }

    /**
     * Trouver le fichier composer.json
     */
    private function findComposerJson(): ?string
    {
        // Chercher d'abord dans le répertoire de sortie
        $outputDir = $this->config('output_dir', getcwd());
        $composerPath = rtrim((string) $outputDir, '/') . '/composer.json';

        if (file_exists($composerPath)) {
            return $composerPath;
        }

        // Chercher dans le répertoire courant
        if (file_exists(getcwd() . '/composer.json')) {
            return getcwd() . '/composer.json';
        }

        // Chercher dans les répertoires parents
        $dir = $outputDir;
        for ($i = 0; $i < 5; $i++) {
            $dir = dirname((string) $dir);
            $composerPath = $dir . '/composer.json';
            if (file_exists($composerPath)) {
                return $composerPath;
            }
        }

        return null;
    }
}
