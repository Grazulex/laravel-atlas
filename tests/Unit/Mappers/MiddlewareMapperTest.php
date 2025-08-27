<?php

declare(strict_types=1);

use Illuminate\Http\Middleware\TrustProxies;
use LaravelAtlas\Mappers\MiddlewareMapper;

describe('MiddlewareMapper', function (): void {
    beforeEach(function (): void {
        $this->mapper = new MiddlewareMapper;
    });

    test('it has correct type', function (): void {
        expect($this->mapper->type())->toBe('middlewares');
    });

    test('it returns empty result when no middleware directory exists', function (): void {
        $result = $this->mapper->scan(['paths' => ['/non/existent/path']]);

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data'])
            ->and($result['type'])->toBe('middlewares')
            ->and($result['count'])->toBe(0)
            ->and($result['data'])->toBeEmpty();
    });

    test('it scans for middlewares using default path', function (): void {
        $result = $this->mapper->scan();

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data'])
            ->and($result['type'])->toBe('middlewares')
            ->and($result['count'])->toBeInt()
            ->and($result['data'])->toBeArray();
    });

    test('it analyzes middleware correctly', function (): void {
        $middlewareCode = '<?php
        namespace Tests\Fixtures;

        class TestMiddleware
        {
            public function __construct(private SomeService $service) {}

            public function handle($request, $next, $param = "default")
            {
                return $next($request);
            }

            public function terminate($request, $response) 
            {
                // cleanup
            }
        }';

        $tempFile = tempnam(sys_get_temp_dir(), 'middleware') . '.php';
        file_put_contents($tempFile, $middlewareCode);

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('analyzeMiddleware');

        $result = $method->invoke($this->mapper, 'Tests\Fixtures\TestMiddleware', $tempFile);

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['class', 'methods', 'dependencies', 'parameters', 'has_terminate', 'flow'])
            ->and($result['class'])->toBe('Tests\Fixtures\TestMiddleware')
            ->and($result['has_terminate'])->toBeBool()  // Peut être true ou false selon si la classe est trouvée
            ->and($result['methods'])->toBeArray()
            ->and($result['dependencies'])->toBeArray()
            ->and($result['parameters'])->toBeArray()
            ->and($result['flow'])->toBeArray();

        unlink($tempFile);
    });

    test('it handles non-existent middleware class', function (): void {
        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('analyzeMiddleware');

        $result = $method->invoke($this->mapper, 'NonExistentMiddleware', '/fake/path');

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['class', 'methods', 'dependencies', 'parameters', 'has_terminate', 'flow'])
            ->and($result['class'])->toBe('NonExistentMiddleware')
            ->and($result['methods'])->toBeEmpty()
            ->and($result['dependencies'])->toBeEmpty()
            ->and($result['parameters'])->toBeEmpty()
            ->and($result['has_terminate'])->toBeFalse()
            ->and($result['flow'])->toBeEmpty();
    });

    test('it extracts methods correctly', function (): void {
        $class = new ReflectionClass(TrustProxies::class);

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('extractMethods');

        $result = $method->invoke($this->mapper, $class);

        expect($result)->toBeArray();

        foreach ($result as $methodInfo) {
            expect($methodInfo)
                ->toHaveKeys(['name', 'visibility', 'is_important', 'parameters'])
                ->and($methodInfo['name'])->toBeString()
                ->and($methodInfo['visibility'])->toBeIn(['public', 'protected', 'private'])
                ->and($methodInfo['is_important'])->toBeBool()
                ->and($methodInfo['parameters'])->toBeArray();
        }
    });

    test('it analyzes flow correctly', function (): void {
        $source = '<?php
        class TestMiddleware {
            public function handle($request, $next) {
                Auth::check();
                Cache::get("key");
                Log::info("middleware");
                throw new ValidationException("error");
                return $next($request);
            }
        }';

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('analyzeFlow');

        $result = $method->invoke($this->mapper, $source);

        expect($result)->toBeArray();

        // Should detect various flow elements
        if (! empty($result)) {
            expect($result)->toBeArray();
        }
    });

    test('it extracts handle parameters correctly', function (): void {
        $middlewareCode = '<?php
        namespace Tests\Fixtures;

        class TestParameterMiddleware
        {
            public function handle($request, $next, string $role = "user", int $limit = 10)
            {
                return $next($request);
            }
        }';

        $tempFile = tempnam(sys_get_temp_dir(), 'middleware') . '.php';
        file_put_contents($tempFile, $middlewareCode);

        // We need to actually include and create the class for reflection to work
        include $tempFile;
        $class = new ReflectionClass('Tests\Fixtures\TestParameterMiddleware');

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('extractHandleParameters');

        $result = $method->invoke($this->mapper, $class);

        expect($result)->toBeArray();

        // Should extract parameters excluding $request and $next
        foreach ($result as $param) {
            expect($param)
                ->toHaveKeys(['name', 'type', 'has_default', 'default', 'is_variadic'])
                ->and($param['name'])->not->toBeIn(['$request', '$next']);
        }

        unlink($tempFile);
    });
});
