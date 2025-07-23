<?php

namespace LaravelAtlas\Mappers;

use Illuminate\Support\Str;
use LaravelAtlas\Support\ClassFinder;
use LaravelAtlas\Contracts\ComponentMapper;

class NotificationMapper implements ComponentMapper
{
    public function type(): string
    {
        return 'notifications';
    }

    public function map(array $options = []): array
    {
        $notifications = [];
        $classes = ClassFinder::inAppNamespace('Notifications');

        foreach ($classes as $class) {
            if (!is_subclass_of($class, \Illuminate\Notifications\Notification::class)) {
                continue;
            }

            $reflection = new \ReflectionClass($class);
            $channels = [];
            $methods = [];
            $jobs = [];
            $events = [];
            $dependencies = [];

            // via() detection
            if ($reflection->hasMethod('via')) {
                $channels = $this->extractChannels($class);
            }

            // toMail(), toDatabase(), toBroadcast(), etc.
            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if (Str::startsWith($method->getName(), 'to')) {
                    $methods[] = $method->getName();
                }
            }

            // Flow detection (basic)
            $source = file_get_contents($reflection->getFileName());
            preg_match_all('/\b(new|dispatch|dispatchSync|event|broadcast|Log::|Mail::to)\s*\(([^;]*)\)/', $source, $matches);

            foreach ($matches[0] as $match) {
                if (Str::contains($match, 'dispatch') && preg_match('/new\s+([A-Z][\w\\\\]+)/', $match, $jobMatch)) {
                    $jobs[] = ['class' => $jobMatch[1], 'async' => !Str::contains($match, 'dispatchSync')];
                }
                if (Str::contains($match, 'event') && preg_match('/\(([^;$]+)/', $match, $eventMatch)) {
                    $events[] = ['class' => trim($eventMatch[1], " ()")];
                }
            }

            // Dependencies via constructor
            $constructor = $reflection->getConstructor();
            if ($constructor) {
                foreach ($constructor->getParameters() as $param) {
                    if ($param->getType() && class_exists((string) $param->getType())) {
                        $dependencies[] = (string) $param->getType();
                    }
                }
            }

            $notifications[] = [
                'class' => $class,
                'channels' => $channels,
                'methods' => $methods,
                'flow' => [
                    'jobs' => $jobs,
                    'events' => $events,
                    'dependencies' => $dependencies,
                ],
            ];
        }

        return $notifications;
    }

    protected function extractChannels(string $class): array
    {
        try {
            $instance = new $class(...array_fill(0, (new \ReflectionClass($class))->getConstructor()?->getNumberOfParameters() ?? 0, null));
            return method_exists($instance, 'via') ? $instance->via(new class {
                public $notification_preferences = ['email_notifications' => true];
                public function following() { return new class { public function where() { return new class { public function exists() { return true; } }; } }; }
                public function subscribedCategories() { return new class { public function where() { return new class { public function exists() { return true; } }; } }; }
            }) : [];
        } catch (\Throwable) {
            return [];
        }
    }
}
