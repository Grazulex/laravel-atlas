<?php

declare(strict_types=1);

namespace LaravelAtlas\Mappers;

use Closure;
use DateTimeZone;
use Illuminate\Console\Application;
use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use LaravelAtlas\Contracts\ComponentMapper;
use LaravelAtlas\Support\ScanOptions;
use ReflectionFunction;
use ReflectionProperty;
use Throwable;

/**
 * Maps the tasks registered on the console scheduler
 * (`routes/console.php`, `withSchedule()`, `Console\Kernel::schedule()`).
 */
class ScheduleMapper implements ComponentMapper
{
    /**
     * Human readable labels for the cron expressions produced by the
     * scheduler frequency helpers.
     *
     * @var array<string, string>
     */
    protected const FREQUENCIES = [
        '* * * * *' => 'everyMinute',
        '*/2 * * * *' => 'everyTwoMinutes',
        '*/3 * * * *' => 'everyThreeMinutes',
        '*/4 * * * *' => 'everyFourMinutes',
        '*/5 * * * *' => 'everyFiveMinutes',
        '*/10 * * * *' => 'everyTenMinutes',
        '*/15 * * * *' => 'everyFifteenMinutes',
        '0,30 * * * *' => 'everyThirtyMinutes',
        '*/30 * * * *' => 'everyThirtyMinutes',
        '0 * * * *' => 'hourly',
        '0 */2 * * *' => 'everyTwoHours',
        '0 */3 * * *' => 'everyThreeHours',
        '0 */4 * * *' => 'everyFourHours',
        '0 */6 * * *' => 'everySixHours',
        '0 0 * * *' => 'daily',
        '0 0 * * 0' => 'weekly',
        '0 0 1 * *' => 'monthly',
        '0 0 1 1-12/3 *' => 'quarterly',
        '0 0 1 1 *' => 'yearly',
    ];

    /**
     * @param  Schedule|null  $schedule  Schedule to inspect; defaults to the application schedule.
     */
    public function __construct(protected ?Schedule $schedule = null) {}

    public function type(): string
    {
        return 'schedules';
    }

    public function scan(array $options = []): array
    {
        $tasks = [];

        foreach ($this->resolveSchedule()->events() as $event) {
            $tasks[] = ScanOptions::filter($this->analyzeEvent($event), $options, [
                'include_descriptions' => ['description'],
                'include_next_due' => ['next_due'],
            ]);
        }

        return [
            'type' => $this->type(),
            'count' => count($tasks),
            'data' => $tasks,
        ];
    }

    protected function resolveSchedule(): Schedule
    {
        if ($this->schedule instanceof Schedule) {
            return $this->schedule;
        }

        $this->loadConsoleSchedule();

        return app(Schedule::class);
    }

    /**
     * Outside of the console, `routes/console.php` and the schedule callbacks
     * are not loaded yet: bootstrap the console kernel so they get registered.
     */
    protected function loadConsoleSchedule(): void
    {
        if (app()->runningInConsole() || ! app()->bound(ConsoleKernel::class)) {
            return;
        }

        try {
            app(ConsoleKernel::class)->all();
        } catch (Throwable) {
            // The map is still built from whatever the schedule already holds.
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeEvent(Event $event): array
    {
        $target = $this->describeTarget($event);
        $description = is_string($event->description) && $event->description !== '' ? $event->description : null;
        $expression = (string) $event->expression;
        $repeatSeconds = is_int($event->repeatSeconds) ? $event->repeatSeconds : null;

        return [
            'name' => $description ?? $target['target'],
            'type' => $target['type'],
            'target' => $target['target'],
            'description' => $description,
            'expression' => $expression,
            'frequency' => $this->frequencyFor($expression, $repeatSeconds),
            'repeat_seconds' => $repeatSeconds,
            'timezone' => $this->timezoneFor($event),
            'environments' => array_values($event->environments),
            'without_overlapping' => (bool) $event->withoutOverlapping,
            'on_one_server' => (bool) $event->onOneServer,
            'run_in_background' => (bool) $event->runInBackground,
            'even_in_maintenance_mode' => (bool) $event->evenInMaintenanceMode,
            'next_due' => $this->nextDueFor($event),
            'file' => $target['file'],
            'line' => $target['line'],
        ];
    }

    /**
     * @return array{type: string, target: string, file: string|null, line: int|null}
     */
    protected function describeTarget(Event $event): array
    {
        if ($event instanceof CallbackEvent) {
            return $this->describeCallback($event);
        }

        $command = (string) $event->command;
        $artisan = Application::artisanBinary();

        $position = $artisan !== '' ? strpos($command, $artisan) : false;

        if ($position !== false) {
            return [
                'type' => 'command',
                'target' => trim(substr($command, $position + strlen($artisan))),
                'file' => null,
                'line' => null,
            ];
        }

        return ['type' => 'exec', 'target' => $command, 'file' => null, 'line' => null];
    }

    /**
     * @return array{type: string, target: string, file: string|null, line: int|null}
     */
    protected function describeCallback(CallbackEvent $event): array
    {
        $callback = $this->callbackOf($event);

        if ($callback instanceof Closure) {
            try {
                $reflection = new ReflectionFunction($callback);
            } catch (Throwable) {
                return ['type' => 'call', 'target' => 'Closure', 'file' => null, 'line' => null];
            }

            // `Schedule::job()` wraps the job dispatch in a closure declared by the scheduler.
            if ($reflection->getClosureScopeClass()?->getName() === Schedule::class) {
                return [
                    'type' => 'job',
                    'target' => is_string($event->description) && $event->description !== '' ? $event->description : 'Unknown',
                    'file' => null,
                    'line' => null,
                ];
            }

            $file = $reflection->getFileName();
            $line = $reflection->getStartLine();

            return [
                'type' => 'call',
                'target' => 'Closure',
                'file' => $file !== false ? $file : null,
                'line' => $line !== false ? $line : null,
            ];
        }

        return ['type' => 'call', 'target' => $this->stringifyCallable($callback), 'file' => null, 'line' => null];
    }

    protected function callbackOf(CallbackEvent $event): mixed
    {
        try {
            $property = new ReflectionProperty(CallbackEvent::class, 'callback');

            return $property->getValue($event);
        } catch (Throwable) {
            return null;
        }
    }

    protected function stringifyCallable(mixed $callback): string
    {
        if (is_string($callback)) {
            return $callback;
        }

        if (is_array($callback) && isset($callback[0], $callback[1])) {
            $class = is_object($callback[0]) ? $callback[0]::class : (is_string($callback[0]) ? $callback[0] : 'Unknown');
            $method = is_string($callback[1]) ? $callback[1] : 'Unknown';

            return $class . '@' . $method;
        }

        if (is_object($callback)) {
            return $callback::class;
        }

        return 'Unknown';
    }

    protected function frequencyFor(string $expression, ?int $repeatSeconds): string
    {
        if ($repeatSeconds !== null && $repeatSeconds > 0 && $repeatSeconds < 60) {
            return match ($repeatSeconds) {
                1 => 'everySecond',
                2 => 'everyTwoSeconds',
                5 => 'everyFiveSeconds',
                10 => 'everyTenSeconds',
                15 => 'everyFifteenSeconds',
                20 => 'everyTwentySeconds',
                30 => 'everyThirtySeconds',
                default => "every {$repeatSeconds} seconds",
            };
        }

        return self::FREQUENCIES[$expression] ?? $expression;
    }

    protected function timezoneFor(Event $event): ?string
    {
        $timezone = $event->timezone;

        if ($timezone instanceof DateTimeZone) {
            return $timezone->getName();
        }

        return is_string($timezone) && $timezone !== '' ? $timezone : null;
    }

    protected function nextDueFor(Event $event): ?string
    {
        try {
            return $event->nextRunDate()->toIso8601String();
        } catch (Throwable) {
            return null;
        }
    }
}
