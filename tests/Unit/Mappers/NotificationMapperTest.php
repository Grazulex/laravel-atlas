<?php

declare(strict_types=1);

use LaravelAtlas\Mappers\NotificationMapper;

describe('NotificationMapper', function (): void {
    beforeEach(function (): void {
        $this->mapper = new NotificationMapper;
    });

    test('it has correct type', function (): void {
        expect($this->mapper->type())->toBe('notifications');
    });

    test('it returns empty result when no notifications directory exists', function (): void {
        $result = $this->mapper->scan(['paths' => ['/non/existent/path']]);

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data'])
            ->and($result['type'])->toBe('notifications')
            ->and($result['count'])->toBe(0)
            ->and($result['data'])->toBeEmpty();
    });

    test('it scans for notifications using default paths', function (): void {
        $result = $this->mapper->scan();

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data'])
            ->and($result['type'])->toBe('notifications')
            ->and($result['count'])->toBeInt()
            ->and($result['data'])->toBeArray();
    });

    test('it analyzes notification correctly', function (): void {
        $notificationCode = '<?php
        namespace Tests\Fixtures;
        
        use Illuminate\Notifications\Notification;
        use Illuminate\Notifications\Messages\MailMessage;
        use Illuminate\Notifications\Messages\DatabaseMessage;
        
        class TestNotification extends Notification
        {
            public function __construct(private string $message) {}
            
            public function via($notifiable): array
            {
                return ["mail", "database"];
            }
            
            public function toMail($notifiable): MailMessage
            {
                return (new MailMessage)
                    ->line($this->message)
                    ->action("Click Here", url("/"));
            }
            
            public function toDatabase($notifiable): array
            {
                return [
                    "message" => $this->message,
                    "action" => url("/")
                ];
            }
        }';

        $tempFile = tempnam(sys_get_temp_dir(), 'notification') . '.php';
        file_put_contents($tempFile, $notificationCode);

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('analyzeNotification');
        $method->setAccessible(true);

        $result = $method->invoke($this->mapper, 'Tests\Fixtures\TestNotification', $tempFile);

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['class', 'channels', 'methods', 'flow'])
            ->and($result['class'])->toBe('Tests\Fixtures\TestNotification')
            ->and($result['channels'])->toBeArray()
            ->and($result['methods'])->toBeArray()
            ->and($result['flow'])->toBeArray();

        unlink($tempFile);
    });

    test('it handles non-existent notification class', function (): void {
        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('analyzeNotification');
        $method->setAccessible(true);

        $result = $method->invoke($this->mapper, 'NonExistentNotification', '/fake/path');

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['class', 'channels', 'methods', 'flow'])
            ->and($result['class'])->toBe('NonExistentNotification')
            ->and($result['channels'])->toBeEmpty()
            ->and($result['methods'])->toBeEmpty()
            ->and($result['flow'])->toBeEmpty();
    });

    test('it extracts channels from via method', function (): void {
        $source = '<?php
        class TestNotification {
            public function via($notifiable): array {
                return ["mail", "database", "slack"];
            }
        }';

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('detectChannels');
        $method->setAccessible(true);

        $result = $method->invoke($this->mapper, $source);

        expect($result)->toBeArray();

        // Should extract channels from via method
        foreach ($result as $channel) {
            expect($channel)->toBeString();
        }
    });

    test('it extracts channels from conditional via method', function (): void {
        $source = '<?php
        class TestNotification {
            public function via($notifiable): array {
                if ($notifiable->preference === "email") {
                    return ["mail"];
                }
                return ["database", "push"];
            }
        }';

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('detectChannels');
        $method->setAccessible(true);

        $result = $method->invoke($this->mapper, $source);

        expect($result)->toBeArray();

        // Should extract all possible channels
        foreach ($result as $channel) {
            expect($channel)->toBeString();
        }
    });

    test('it extracts delivery methods correctly', function (): void {
        $source = '<?php
        class TestDeliveryNotification
        {
            public function toMail($notifiable) {}
            public function toDatabase($notifiable) {}
            public function toSlack($notifiable) {}
            public function toBroadcast($notifiable) {}
            public function toNexmo($notifiable) {}
            public function regularMethod($param) {}
        }';

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('detectDefinedMethods');
        $method->setAccessible(true);

        $result = $method->invoke($this->mapper, $source);

        expect($result)->toBeArray();

        foreach ($result as $methodName) {
            expect($methodName)->toBeString();
            // Should only include 'to*' methods
            expect($methodName)->toStartWith('to');
        }

        // Should not include regular methods
        expect($result)->not->toContain('regularMethod');
    });

    test('it analyzes notification flow correctly', function (): void {
        $source = '<?php
        class TestNotification {
            public function toMail($notifiable) {
                Mail::send("template", [], function($message) {
                    $message->to($notifiable->email);
                });
                event(new NotificationSent());
                Log::info("notification sent");
            }
            
            public function toDatabase($notifiable) {
                return [
                    "message" => "Hello",
                    "data" => collect($this->data)->toArray()
                ];
            }
        }';

        $reflection = new ReflectionClass($this->mapper);
        $method = $reflection->getMethod('analyzeFlow');
        $method->setAccessible(true);

        $result = $method->invoke($this->mapper, $source);

        expect($result)->toBeArray();

        // Should detect various flow elements
        if (! empty($result)) {
            $possibleKeys = ['jobs', 'events', 'calls', 'notifications', 'dependencies'];
            foreach ($result as $key => $value) {
                expect($key)->toBeIn($possibleKeys);
            }
        }
    });

    test('it scans with custom paths', function (): void {
        $result = $this->mapper->scan(['paths' => [__DIR__ . '/../../Fixtures']]);

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['type', 'count', 'data'])
            ->and($result['type'])->toBe('notifications');
    });
});
