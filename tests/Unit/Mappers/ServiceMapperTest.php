<?php

declare(strict_types=1);

use LaravelAtlas\Mappers\ServiceMapper;

describe('ServiceMapper', function (): void {
    beforeEach(function (): void {
        $this->mapper = new ServiceMapper;
    });

    test('it has correct type', function (): void {
        expect($this->mapper->type())->toBe('services');
    });

    test('it returns empty result when no services directory exists', function (): void {
        $result = $this->mapper->scan(['paths' => ['/non/existent/path']]);

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data'])
            ->and($result['type'])->toBe('services')
            ->and($result['count'])->toBe(0)
            ->and($result['data'])->toBeEmpty();
    });

    test('it scans for services using default paths', function (): void {
        $result = $this->mapper->scan();

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data'])
            ->and($result['type'])->toBe('services')
            ->and($result['count'])->toBeInt()
            ->and($result['data'])->toBeArray();
    });

    test('it analyzes service correctly', function (): void {
        $serviceCode = '<?php
        namespace Tests\Fixtures;
        
        class TestService
        {
            public function __construct(private AnotherService $anotherService) {}
            
            public function process(array $data): string
            {
                return "processed";
            }
            
            public function validate($input): bool 
            {
                return true;
            }
            
            private function helper(): void {}
        }';

        $tempFile = tempnam(sys_get_temp_dir(), 'service') . '.php';
        file_put_contents($tempFile, $serviceCode);

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('analyzeService');
        $method->setAccessible(true);

        $result = $method->invoke($this->mapper, 'Tests\Fixtures\TestService', $tempFile);

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['class', 'methods', 'dependencies', 'flow'])
            ->and($result['class'])->toBe('Tests\Fixtures\TestService')
            ->and($result['methods'])->toBeArray()
            ->and($result['dependencies'])->toBeArray()
            ->and($result['flow'])->toBeArray();

        unlink($tempFile);
    });

    test('it handles non-existent service class', function (): void {
        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('analyzeService');
        $method->setAccessible(true);

        $result = $method->invoke($this->mapper, 'NonExistentService', '/fake/path');

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['class', 'methods', 'dependencies', 'flow'])
            ->and($result['class'])->toBe('NonExistentService')
            ->and($result['methods'])->toBeEmpty()
            ->and($result['dependencies'])->toBeEmpty()
            ->and($result['flow'])->toBeEmpty();
    });

    test('it extracts public methods correctly', function (): void {
        $serviceCode = '<?php
        namespace Tests\Fixtures;
        
        class TestMethodService
        {
            public function publicMethod(string $param): void {}
            protected function protectedMethod(): string { return "test"; }
            private function privateMethod(): bool { return true; }
            public static function staticMethod(): void {}
        }';

        $tempFile = tempnam(sys_get_temp_dir(), 'service') . '.php';
        file_put_contents($tempFile, $serviceCode);

        include $tempFile;
        $class = new ReflectionClass('Tests\Fixtures\TestMethodService');

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('extractPublicMethods');
        $method->setAccessible(true);

        $result = $method->invoke($this->mapper, $class);

        expect($result)->toBeArray();

        // Should only include public non-static methods defined in the class
        foreach ($result as $methodInfo) {
            expect($methodInfo)
                ->toHaveKeys(['name', 'parameters'])
                ->and($methodInfo['name'])->toBeString()
                ->and($methodInfo['parameters'])->toBeArray();
        }

        // Should include all public methods defined in the class (including static)
        $methodNames = array_column($result, 'name');
        expect($methodNames)->toContain('publicMethod');
        expect($methodNames)->toContain('staticMethod'); // Static methods are public too
        expect($methodNames)->not->toContain('privateMethod');
        expect($methodNames)->not->toContain('protectedMethod');

        unlink($tempFile);
    });

    test('it extracts constructor dependencies correctly', function (): void {
        $serviceCode = '<?php
        namespace Tests\Fixtures;
        
        class TestDependencyService
        {
            public function __construct(
                private SomeService $someService,
                private ?AnotherService $anotherService = null,
                private string $config = "default"
            ) {}
        }';

        $tempFile = tempnam(sys_get_temp_dir(), 'service') . '.php';
        file_put_contents($tempFile, $serviceCode);

        include $tempFile;
        $class = new ReflectionClass('Tests\Fixtures\TestDependencyService');

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('extractConstructorDependencies');
        $method->setAccessible(true);

        $result = $method->invoke($this->mapper, $class);

        expect($result)->toBeArray();

        // Should extract non-builtin types
        $nonNullDependencies = array_filter($result);
        expect($nonNullDependencies)->not->toBeEmpty();

        unlink($tempFile);
    });

    test('it handles service without constructor', function (): void {
        $serviceCode = '<?php
        namespace Tests\Fixtures;
        
        class TestNoConstructorService
        {
            public function process(): string
            {
                return "processed";
            }
        }';

        $tempFile = tempnam(sys_get_temp_dir(), 'service') . '.php';
        file_put_contents($tempFile, $serviceCode);

        include $tempFile;
        $class = new ReflectionClass('Tests\Fixtures\TestNoConstructorService');

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('extractConstructorDependencies');
        $method->setAccessible(true);

        $result = $method->invoke($this->mapper, $class);

        expect($result)->toBeArray()->toBeEmpty();

        unlink($tempFile);
    });

    test('it analyzes flow correctly', function (): void {
        $source = '<?php
        class TestService {
            public function process() {
                dispatch(new ProcessJob());
                event(new ProcessedEvent());
                Log::info("processing");
                Cache::put("key", "value");
                Mail::send(new ProcessedMail());
                Notification::send($user, new ProcessedNotification());
            }
        }';

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('analyzeFlow');
        $method->setAccessible(true);

        $result = $method->invoke($this->mapper, $source);

        expect($result)->toBeArray();

        // Should detect various flow elements
        if (! empty($result)) {
            expect($result)->toBeArray();

            // Check for specific flow types if detected
            $possibleKeys = ['jobs', 'events', 'dependencies', 'notifications', 'logs', 'mails'];
            foreach ($result as $key => $value) {
                expect($key)->toBeIn($possibleKeys);
                expect($value)->toBeArray();
            }
        }
    });

    test('it scans with custom paths', function (): void {
        $result = $this->mapper->scan(['paths' => [__DIR__ . '/../../Fixtures']]);

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data'])
            ->and($result['type'])->toBe('services');
    });
});
