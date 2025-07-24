<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Illuminate\Support\Facades\File;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;
use ReflectionMethod;

class ServiceMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'services';
    }

    public function scan(array $options = []): array
    {
        $services = [];
        $paths = $options['paths'] ?? [app_path('Services')];
        $recursive = $options['recursive'] ?? true;
        $seen = [];

        foreach ($paths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            $files = $recursive ? File::allFiles($path) : File::files($path);

            foreach ($files as $file) {
                $fqcn = ClassResolver::resolveFromPath($file->getRealPath());

                if (
                    $fqcn &&
                    class_exists($fqcn) &&
                    ! isset($seen[$fqcn])
                ) {
                    $seen[$fqcn] = true;
                    $services[] = $this->analyzeService($fqcn, $file->getRealPath());
                }
            }
        }

        return [
            'type' => $this->type(),
            'count' => count($services),
            'data' => $services,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeService(string $fqcn, string $filePath): array
    {
        if (! class_exists($fqcn)) {
            return [
                'class' => $fqcn,
                'namespace' => '',
                'name' => class_basename($fqcn),
                'file' => $filePath,
                'methods' => [],
                'dependencies' => [],
                'flow' => [],
            ];
        }

        $reflection = new ReflectionClass($fqcn);
        $source = file_get_contents($filePath);

        if ($source === false) {
            $source = null;
        }

        return [
            'class' => $fqcn,
            'namespace' => $reflection->getNamespaceName(),
            'name' => $reflection->getShortName(),
            'file' => $filePath,
            'methods' => $this->extractPublicMethods($reflection),
            'dependencies' => $this->extractConstructorDependencies($reflection),
            'flow' => $this->analyzeFlow($source),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function extractPublicMethods(ReflectionClass $reflection): array
    {
        $methods = [];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class !== $reflection->getName()) {
                continue; // Ignore inherited
            }

            $methods[] = [
                'name' => $method->getName(),
                'parameters' => array_map(
                    fn ($param): string => '$' . $param->getName(),
                    $method->getParameters()
                ),
            ];
        }

        return $methods;
    }

    /**
     * @return array<int, string|null>
     */
    protected function extractConstructorDependencies(ReflectionClass $reflection): array
    {
        if (! $reflection->hasMethod('__construct')) {
            return [];
        }

        $method = $reflection->getMethod('__construct');

        return array_map(function ($param) {
            $type = $param->getType();

            if ($type === null) {
                return;
            }

            // PHPStan needs us to check if type has isBuiltin method
            if (method_exists($type, 'isBuiltin') && ! $type->isBuiltin()) {
                return (string) $type;
            }
        }, $method->getParameters());
    }

    /**
     * @return array<string, array<int, mixed>|array<int, string>>
     */
    protected function analyzeFlow(?string $source): array
    {
        if (! $source) {
            return [];
        }

        $flow = [
            'jobs' => [],
            'events' => [],
            'dependencies' => [],
            'notifications' => [],
            'logs' => [],
            'mails' => [],
        ];

        // Jobs
        if (preg_match_all('/dispatch(?:Now)?\(\s*([A-Z][\w\\\\]+)::class/', $source, $matches)) {
            foreach ($matches[1] as $fqcn) {
                $flow['jobs'][] = [
                    'class' => $fqcn,
                    'async' => ! str_contains($source, "dispatchNow({$fqcn}"),
                ];
            }
        }

        // Events
        if (preg_match_all('/event\(\s*([A-Z][\w\\\\]+)::class/', $source, $matches)) {
            foreach ($matches[1] as $fqcn) {
                $flow['events'][] = ['class' => $fqcn];
            }
        }

        // Notifications
        if (preg_match_all('/->notify\(\s*new\s+([A-Z][\w\\\\]+)/', $source, $matches)) {
            foreach ($matches[1] as $fqcn) {
                $flow['notifications'][] = $fqcn;
            }
        }

        // Logs
        if (str_contains($source, 'Log::')) {
            $flow['logs'][] = 'Log';
        }

        // Mail
        if (str_contains($source, 'Mail::')) {
            $flow['mails'][] = 'Mail';
        }

        // Dependencies via new X or X::
        if (preg_match_all('/new\s+([A-Z][\w\\\\]+)|([A-Z][\w\\\\]+)::/', $source, $matches)) {
            $found = array_filter(array_merge($matches[1], $matches[2]));
            $flow['dependencies'] = array_values(array_unique(array_filter($found)));
        }

        return $flow;
    }
}
