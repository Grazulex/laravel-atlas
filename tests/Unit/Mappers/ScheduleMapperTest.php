<?php

declare(strict_types=1);

use Illuminate\Console\Scheduling\Schedule;
use LaravelAtlas\AtlasManager;
use LaravelAtlas\Exporters\AtlasExportManager;
use LaravelAtlas\Facades\Atlas;
use LaravelAtlas\Mappers\ScheduleMapper;

/**
 * @param  array<string, mixed>  $options
 *
 * @return array<int, array<string, mixed>>
 */
function atlasScheduleTasks(Schedule $schedule, array $options = []): array
{
    return (new ScheduleMapper($schedule))->scan($options)['data'];
}

describe('ScheduleMapper', function (): void {
    test('it has correct type', function (): void {
        expect((new ScheduleMapper)->type())->toBe('schedules');
    });

    test('it is registered on the atlas manager', function (): void {
        expect((new AtlasManager)->registry()->get('schedules'))->toBeInstanceOf(ScheduleMapper::class);
    });

    test('it returns an empty map when nothing is scheduled', function (): void {
        $result = (new ScheduleMapper(new Schedule))->scan();

        expect($result)
            ->toBe(['type' => 'schedules', 'count' => 0, 'data' => []]);
    });

    test('it maps artisan commands with their frequency', function (): void {
        $schedule = new Schedule;
        $schedule->command('inspire')->hourly();
        $schedule->command('reports:send', ['--force'])->dailyAt('02:30');

        $tasks = atlasScheduleTasks($schedule);

        expect($tasks)->toHaveCount(2)
            ->and($tasks[0])->toMatchArray([
                'name' => 'inspire',
                'type' => 'command',
                'target' => 'inspire',
                'description' => null,
                'expression' => '0 * * * *',
                'frequency' => 'hourly',
                'repeat_seconds' => null,
            ])
            ->and($tasks[1])->toMatchArray([
                'type' => 'command',
                'target' => 'reports:send --force',
                'expression' => '30 2 * * *',
                'frequency' => '30 2 * * *',
            ]);
    });

    test('it maps queued jobs', function (): void {
        $schedule = new Schedule;
        $schedule->job('App\\Jobs\\PruneReports')->daily();

        $task = atlasScheduleTasks($schedule)[0];

        expect($task)->toMatchArray([
            'name' => 'App\\Jobs\\PruneReports',
            'type' => 'job',
            'target' => 'App\\Jobs\\PruneReports',
            'expression' => '0 0 * * *',
            'frequency' => 'daily',
            'file' => null,
            'line' => null,
        ]);
    });

    test('it maps closures with their location', function (): void {
        $schedule = new Schedule;
        $schedule->call(function (): void {})->description('Clean temporary files')->weekly();

        $task = atlasScheduleTasks($schedule)[0];

        expect($task)->toMatchArray([
            'name' => 'Clean temporary files',
            'type' => 'call',
            'target' => 'Closure',
            'description' => 'Clean temporary files',
            'frequency' => 'weekly',
            'file' => __FILE__,
        ])
            ->and($task['line'])->toBeInt();
    });

    test('it maps non-closure callables', function (): void {
        $schedule = new Schedule;
        $schedule->call([new ArrayObject, 'count'])->everyFiveMinutes();
        $schedule->call('strtolower')->monthly();

        $tasks = atlasScheduleTasks($schedule);

        expect($tasks[0])->toMatchArray(['type' => 'call', 'target' => 'ArrayObject@count', 'frequency' => 'everyFiveMinutes'])
            ->and($tasks[1])->toMatchArray(['type' => 'call', 'target' => 'strtolower', 'frequency' => 'monthly']);
    });

    test('it maps shell commands', function (): void {
        $schedule = new Schedule;
        $schedule->exec('node /var/www/scripts/sync.js')->everyFifteenMinutes();

        expect(atlasScheduleTasks($schedule)[0])->toMatchArray([
            'type' => 'exec',
            'target' => 'node /var/www/scripts/sync.js',
            'frequency' => 'everyFifteenMinutes',
        ]);
    });

    test('it reports sub-minute frequencies', function (): void {
        $schedule = new Schedule;
        $schedule->command('queue:heartbeat')->everyThirtySeconds();

        expect(atlasScheduleTasks($schedule)[0])->toMatchArray([
            'expression' => '* * * * *',
            'frequency' => 'everyThirtySeconds',
            'repeat_seconds' => 30,
        ]);
    });

    test('it reports the task constraints', function (): void {
        $schedule = new Schedule;
        $schedule->command('backup:run')
            ->daily()
            ->timezone('Europe/Brussels')
            ->environments(['production'])
            ->withoutOverlapping()
            ->onOneServer()
            ->runInBackground()
            ->evenInMaintenanceMode();

        $task = atlasScheduleTasks($schedule)[0];

        expect($task)->toMatchArray([
            'timezone' => 'Europe/Brussels',
            'environments' => ['production'],
            'without_overlapping' => true,
            'on_one_server' => true,
            'run_in_background' => true,
            'even_in_maintenance_mode' => true,
        ])
            ->and($task['next_due'])->toBeString();
    });

    test('include options drop the matching keys', function (): void {
        $schedule = new Schedule;
        $schedule->command('inspire')->hourly()->description('Show a quote');

        $task = atlasScheduleTasks($schedule, ['include_descriptions' => false, 'include_next_due' => false]);

        expect($task[0])->not->toHaveKeys(['description', 'next_due'])
            ->and($task[0]['name'])->toBe('Show a quote');
    });

    test('it reads the application schedule by default', function (): void {
        $schedule = new Schedule;
        $schedule->command('inspire')->hourly();
        app()->instance(Schedule::class, $schedule);

        $result = Atlas::scan('schedules');

        expect($result['type'])->toBe('schedules')
            ->and($result['count'])->toBe(1)
            ->and($result['data'][0]['target'])->toBe('inspire');
    });

    test('schedules are part of the full export', function (): void {
        $schedule = new Schedule;
        $schedule->command('inspire')->hourly();
        app()->instance(Schedule::class, $schedule);

        $markdown = AtlasExportManager::exportAll('markdown');

        expect($markdown)->toContain('## Schedules')
            ->and($markdown)->toContain('### inspire');
    });
});
