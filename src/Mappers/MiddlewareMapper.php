<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use ReflectionType;
use Illuminate\Support\Facades\File;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ClassResolver;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

class MiddlewareMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'middlewares';
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @return array<string, mixed>
     */
    public function scan(array $options = []): array
    {
        $middlewares = [];

        // Find middleware files in app/Http/Middleware
        $paths = $options['paths'] ?? [app_path('Http/Middleware')];

        foreach ($paths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            $files = File::allFiles($path);

            foreach ($files as $file) {
                $fqcn = $this->resolveClassFromFile($file->getRealPath());

                if ($fqcn && class_exists($fqcn)) {
                    $middlewares[] = $this->analyzeMiddleware($fqcn, $file->getPathname());
                }
            }
        }

        return [
            'type' => $this->type(),
            'count' => count($middlewares),
            'data' => $middlewares,
        ];
    }

    protected function resolveClassFromFile(string $path): ?string
    {
        return ClassResolver::resolveFromPath($path);
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeMiddleware(string $fqcn, string $filePath): array
    {
        if (! class_exists($fqcn)) {
            return [
                'class' => $fqcn,
                'methods' => [],
                'dependencies' => [],
                'parameters' => [],
                'has_terminate' => false,
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
            'methods' => $this->extractMethods($reflection),
            'dependencies' => $this->extractConstructorDependencies($reflection),
            'parameters' => $this->extractHandleParameters($reflection),
            'has_terminate' => $reflection->hasMethod('terminate'),
            'flow' => $this->analyzeFlow($source),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function extractMethods(ReflectionClass $reflection): array
    {
        $methods = [];
        $importantMethods = ['handle', 'terminate', '__construct'];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED) as $method) {
            if ($method->class !== $reflection->getName()) {
                continue; // Ignore inherited methods
            }

            $isImportant = in_array($method->getName(), $importantMethods);

            $methods[] = [
                'name' => $method->getName(),
                'visibility' => $method->isPublic() ? 'public' : ($method->isProtected() ? 'protected' : 'private'),
                'is_important' => $isImportant,
                'parameters' => array_map(
                    fn (ReflectionParameter $param): array => [
                        'name' => '$' . $param->getName(),
                        'type' => $param->getType() instanceof ReflectionType ? (string) $param->getType() : null,
                        'has_default' => $param->isDefaultValueAvailable(),
                        'default' => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null,
                    ],
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

        return array_map(function (ReflectionParameter $param) {
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
     * @return array<int, array<string, mixed>>
     */
    protected function extractHandleParameters(ReflectionClass $reflection): array
    {
        if (! $reflection->hasMethod('handle')) {
            return [];
        }

        $method = $reflection->getMethod('handle');
        $parameters = [];

        foreach ($method->getParameters() as $param) {
            // Skip $request and $next parameters (standard middleware params)
            if (in_array($param->getName(), ['request', 'next'])) {
                continue;
            }

            $parameters[] = [
                'name' => '$' . $param->getName(),
                'type' => $param->getType() instanceof ReflectionType ? (string) $param->getType() : 'mixed',
                'has_default' => $param->isDefaultValueAvailable(),
                'default' => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null,
                'is_variadic' => $param->isVariadic(),
            ];
        }

        return $parameters;
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
            'facades' => [],
            'services' => [],
            'logs' => [],
            'cache' => [],
            'auth' => [],
            'events' => [],
            'exceptions' => [],
        ];

        // Detect facade usage
        if (preg_match_all('/(?:use\s+)?(?:Illuminate\\\\Support\\\\Facades\\\\|\\\\)(\w+)(?:\s*;|\s*::|->)/', $source, $matches)) {
            $flow['facades'] = array_unique($matches[1]);
        }

        // Detect service injection in constructor
        if (preg_match_all('/private\s+(\w+Service)\s+\$\w+/', $source, $matches)) {
            $flow['services'] = array_unique($matches[1]);
        }

        // Detect logging
        if (preg_match_all('/Log::(?:channel\([\'"]([^\'"]+)[\'"]\)->)?(\w+)\(/', $source, $matches)) {
            foreach ($matches[1] as $index => $channel) {
                $level = $matches[2][$index];
                $flow['logs'][] = $channel !== '' && $channel !== '0' ? "$channel.$level" : $level;
            }
            $flow['logs'] = array_unique($flow['logs']);
        }

        // Detect cache usage
        if (preg_match_all('/Cache::(\w+)\(/', $source, $matches)) {
            $flow['cache'] = array_unique($matches[1]);
        }

        // Detect auth usage
        if (preg_match_all('/Auth::(\w+)\(/', $source, $matches)) {
            $flow['auth'] = array_unique($matches[1]);
        }

        // Detect event dispatching
        if (preg_match_all('/(?:event\(|Event::dispatch\(|dispatch\()\s*new\s+(\w+)/', $source, $matches)) {
            $flow['events'] = array_unique($matches[1]);
        }

        // Detect exceptions
        if (preg_match_all('/throw\s+new\s+(\w+Exception)/', $source, $matches)) {
            $flow['exceptions'] = array_unique($matches[1]);
        }

        // Detect job dispatching
        if (preg_match_all('/dispatch\(\s*new\s+(\w+)/', $source, $matches)) {
            $flow['jobs'] = array_unique($matches[1]);
        }

        // Detect notification sending
        if (preg_match_all('/notify\(\s*new\s+(\w+)/', $source, $matches)) {
            $flow['notifications'] = array_unique($matches[1]);
        }

        return array_filter($flow, fn ($items): bool => ! empty($items));
    }
}
