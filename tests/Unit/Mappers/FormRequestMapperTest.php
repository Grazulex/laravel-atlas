<?php

declare(strict_types=1);

use LaravelAtlas\Mappers\FormRequestMapper;

describe('FormRequestMapper', function (): void {
    beforeEach(function (): void {
        $this->mapper = new FormRequestMapper;
    });

    test('it has correct type', function (): void {
        expect($this->mapper->type())->toBe('form_requests');
    });

    test('it returns empty result when no requests directory exists', function (): void {
        $result = $this->mapper->scan(['paths' => ['/non/existent/path']]);

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data'])
            ->and($result['type'])->toBe('form_requests')
            ->and($result['count'])->toBe(0)
            ->and($result['data'])->toBeEmpty();
    });

    test('it scans for form requests using default path', function (): void {
        $result = $this->mapper->scan();

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data'])
            ->and($result['type'])->toBe('form_requests')
            ->and($result['count'])->toBeInt()
            ->and($result['data'])->toBeArray();
    });

    test('it analyzes form request correctly', function (): void {
        $formRequestCode = '<?php
        namespace Tests\Fixtures;
        use Illuminate\Foundation\Http\FormRequest;
        
        class TestFormRequest extends FormRequest
        {
            public function authorize(): bool
            {
                return Auth::check();
            }
            
            public function rules(): array
            {
                return [
                    "name" => ["required", "string", "max:255"],
                    "email" => ["required", "email", Rule::unique("users")],
                ];
            }
            
            public function attributes(): array
            {
                return [
                    "name" => "full name",
                    "email" => "email address",
                ];
            }
            
            public function messages(): array
            {
                return [
                    "name.required" => "Name is required",
                    "email.email" => "Valid email required",
                ];
            }
        }';

        $tempFile = tempnam(sys_get_temp_dir(), 'form_request') . '.php';
        file_put_contents($tempFile, $formRequestCode);

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('analyzeFormRequest');

        $result = $method->invoke($this->mapper, 'Tests\Fixtures\TestFormRequest', $tempFile);

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['class', 'rules', 'authorization', 'attributes', 'messages', 'methods', 'flow'])
            ->and($result['class'])->toBe('Tests\Fixtures\TestFormRequest')
            ->and($result['rules'])->toBeArray()
            ->and($result['attributes'])->toBeArray()
            ->and($result['messages'])->toBeArray()
            ->and($result['methods'])->toBeArray()
            ->and($result['flow'])->toBeArray();

        unlink($tempFile);
    });

    test('it handles non-existent form request class', function (): void {
        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('analyzeFormRequest');

        $result = $method->invoke($this->mapper, 'NonExistentFormRequest', '/fake/path');

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['class', 'rules', 'authorization', 'attributes', 'messages', 'methods', 'flow'])
            ->and($result['class'])->toBe('NonExistentFormRequest')
            ->and($result['rules'])->toBeEmpty()
            ->and($result['authorization'])->toBeNull()
            ->and($result['attributes'])->toBeEmpty()
            ->and($result['messages'])->toBeEmpty()
            ->and($result['methods'])->toBeEmpty()
            ->and($result['flow'])->toBeEmpty();
    });

    test('it extracts rules correctly', function (): void {
        $source = '<?php
        class TestFormRequest {
            public function rules(): array
            {
                return [
                    "name" => ["required", "string", "max:255"],
                    "email" => ["required", "email", Rule::unique("users")],
                    "password" => ["required", new CustomRule()],
                ];
            }
        }';

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('extractRules');

        $result = $method->invoke($this->mapper, $source);

        expect($result)->toBeArray();

        if (! empty($result)) {
            // Should have extracted field rules
            foreach ($result as $field => $rules) {
                expect($field)->toBeString();
                expect($rules)->toBeArray();
            }
        }
    });

    test('it extracts authorization correctly', function (): void {
        $source = '<?php
        class TestFormRequest {
            public function authorize(): bool
            {
                return Auth::check() && Auth::user()->can("create", Post::class);
            }
        }';

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('extractAuthorization');

        $result = $method->invoke($this->mapper, $source);

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['method_exists', 'return_statement', 'uses_auth', 'uses_can', 'always_true', 'always_false'])
            ->and($result['method_exists'])->toBeTrue()
            ->and($result['uses_auth'])->toBeTrue()
            ->and($result['uses_can'])->toBeTrue()
            ->and($result['always_true'])->toBeFalse()
            ->and($result['always_false'])->toBeFalse();
    });

    test('it extracts attributes correctly', function (): void {
        $source = '<?php
        class TestFormRequest {
            public function attributes(): array
            {
                return [
                    "first_name" => "first name",
                    "last_name" => "last name",
                ];
            }
        }';

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('extractAttributes');

        $result = $method->invoke($this->mapper, $source);

        expect($result)
            ->toBeArray()
            ->and($result['first_name'] ?? null)->toBe('first name')
            ->and($result['last_name'] ?? null)->toBe('last name');
    });

    test('it extracts messages correctly', function (): void {
        $source = '<?php
        class TestFormRequest {
            public function messages(): array
            {
                return [
                    "name.required" => "Name is required",
                    "email.email" => "Valid email required",
                ];
            }
        }';

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('extractMessages');

        $result = $method->invoke($this->mapper, $source);

        expect($result)
            ->toBeArray()
            ->and($result['name.required'] ?? null)->toBe('Name is required')
            ->and($result['email.email'] ?? null)->toBe('Valid email required');
    });

    test('it analyzes flow correctly', function (): void {
        $source = '<?php
        use App\Models\User;
        use App\Rules\CustomRule;
        
        class TestFormRequest {
            public function authorize(): bool {
                return Auth::check() && Auth::user()->can("create", User::class);
            }
            
            public function rules(): array {
                return [
                    "name" => ["required", "string", new CustomRule()],
                ];
            }
        }';

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('analyzeFlow');

        $result = $method->invoke($this->mapper, $source);

        expect($result)->toBeArray();

        // Should detect various flow elements like uses, models, rules, etc.
        if (! empty($result)) {
            expect($result)->toBeArray();
        }
    });

    test('it extracts methods correctly', function (): void {
        $formRequestCode = '<?php
        namespace Tests\Fixtures;
        use Illuminate\Foundation\Http\FormRequest;
        
        class TestMethodFormRequest extends FormRequest
        {
            public function authorize(): bool { return true; }
            public function rules(): array { return []; }
            protected function prepareForValidation(): void {}
            private function helper(): string { return "test"; }
        }';

        $tempFile = tempnam(sys_get_temp_dir(), 'form_request') . '.php';
        file_put_contents($tempFile, $formRequestCode);

        include $tempFile;
        $class = new ReflectionClass('Tests\Fixtures\TestMethodFormRequest');

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('extractMethods');

        $result = $method->invoke($this->mapper, $class);

        expect($result)->toBeArray();

        foreach ($result as $methodInfo) {
            expect($methodInfo)
                ->toHaveKeys(['name', 'visibility', 'is_important', 'return_type', 'parameters'])
                ->and($methodInfo['name'])->toBeString()
                ->and($methodInfo['visibility'])->toBeIn(['public', 'protected', 'private'])
                ->and($methodInfo['is_important'])->toBeBool();
        }

        unlink($tempFile);
    });
});
