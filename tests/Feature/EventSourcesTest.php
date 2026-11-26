<?php

declare(strict_types=1);

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Support\Facades\Event;
use LaravelAtlas\Exporters\Html\HtmlLayoutExporter;
use LaravelAtlas\Exporters\Json\JsonExporter;
use LaravelAtlas\Exporters\Markdown\MarkdownExporter;
use LaravelAtlas\Mappers\EventMapper;
use LaravelAtlas\Mappers\ListenerMapper;
use LaravelAtlas\Mappers\ModelMapper;
use LaravelAtlas\Mappers\ObserverMapper;
use LaravelAtlas\Support\EventListenerDiscovery;
use Tests\Fixtures\EventSources\DiscoveredListeners\ArchiveCancelledOrder;
use Tests\Fixtures\EventSources\Events\InvoicePaid;
use Tests\Fixtures\EventSources\Events\OrderCancelled;
use Tests\Fixtures\EventSources\Events\OrderShipped;
use Tests\Fixtures\EventSources\Events\PostPublished;
use Tests\Fixtures\EventSources\ExternalEvents\PaymentFailed;
use Tests\Fixtures\EventSources\Listeners\NotifyFinance;
use Tests\Fixtures\EventSources\Listeners\SendShipmentNotification;
use Tests\Fixtures\EventSources\Listeners\SendShipmentSms;
use Tests\Fixtures\EventSources\Listeners\UpdateInventory;
use Tests\Fixtures\EventSources\Models\Comment;
use Tests\Fixtures\EventSources\Models\Post;
use Tests\Fixtures\EventSources\Observers\AuditObserver;
use Tests\Fixtures\EventSources\Observers\CommentObserver;
use Tests\Fixtures\EventSources\Observers\LoggingObserver;
use Tests\Fixtures\EventSources\Observers\PostObserver;
use Tests\Fixtures\EventSources\Providers\FixtureEventServiceProvider;
use Tests\Fixtures\EventSources\Providers\FixtureObserverServiceProvider;
use Tests\Fixtures\EventSources\Subscribers\InvoiceSubscriber;

function fixturePath(string $directory): string
{
    return dirname(__DIR__) . '/Fixtures/EventSources/' . $directory;
}

/**
 * @param  array<string, mixed>  $result
 *
 * @return array<string, mixed>
 */
function findComponent(array $result, string $class): array
{
    foreach ($result['data'] as $component) {
        if ($component['class'] === $class) {
            return $component;
        }
    }

    throw new RuntimeException("Component [{$class}] not found");
}

/**
 * @param  array<int, array<string, mixed>>  $listeners
 *
 * @return array<string, string> listener class => source
 */
function listenerSources(array $listeners): array
{
    $sources = [];

    foreach ($listeners as $listener) {
        $sources[$listener['class'] === 'Closure' ? $listener['name'] : $listener['class']] = $listener['source'];
    }

    return $sources;
}

function resetEventDiscoveryPaths(): void
{
    (new ReflectionProperty(EventServiceProvider::class, 'eventDiscoveryPaths'))->setValue(null, null);
}

beforeEach(function (): void {
    config()->set('atlas.paths.events', [fixturePath('Events')]);
    config()->set('atlas.paths.listeners', [fixturePath('Listeners')]);

    $this->app->register(FixtureEventServiceProvider::class);
    $this->app->register(FixtureObserverServiceProvider::class);
});

afterEach(function (): void {
    resetEventDiscoveryPaths();
});

describe('event sources', function (): void {
    test('it reads $listen arrays of the registered event service providers', function (): void {
        $event = findComponent((new EventMapper)->scan(), OrderShipped::class);

        expect(listenerSources($event['listeners']))
            ->toHaveKey(SendShipmentNotification::class, EventListenerDiscovery::SOURCE_PROVIDER);
    });

    test('it reads the listeners of event subscribers', function (): void {
        $event = findComponent((new EventMapper)->scan(), InvoicePaid::class);

        expect($event['listeners'])->toHaveCount(1)
            ->and($event['listeners'][0])->toMatchArray([
                'class' => InvoiceSubscriber::class,
                'method' => 'handleInvoicePaid',
                'source' => EventListenerDiscovery::SOURCE_SUBSCRIBER,
            ]);
    });

    test('it keeps listeners found through handle() type-hints', function (): void {
        $event = findComponent((new EventMapper)->scan(), OrderShipped::class);

        expect(listenerSources($event['listeners']))
            ->toHaveKey(UpdateInventory::class, 'handle() type-hint');
    });

    test('it discovers invokable listeners in the app event discovery paths', function (): void {
        EventServiceProvider::setEventDiscoveryPaths([fixturePath('DiscoveredListeners')]);

        $event = findComponent((new EventMapper)->scan(), OrderCancelled::class);

        expect($event['listeners'])->toHaveCount(1)
            ->and($event['listeners'][0])->toMatchArray([
                'class' => ArchiveCancelledOrder::class,
                'method' => '__invoke',
                'source' => EventListenerDiscovery::SOURCE_DISCOVERY,
            ]);
    });

    test('it captures Event::listen() registrations, closures included', function (): void {
        Event::listen(OrderShipped::class, SendShipmentSms::class);
        Event::listen(function (OrderShipped $event): void {});

        $event = findComponent((new EventMapper)->scan(), OrderShipped::class);
        $sources = listenerSources($event['listeners']);

        expect($sources)->toHaveKey(SendShipmentSms::class, EventListenerDiscovery::SOURCE_DISPATCHER);

        $closures = array_filter($event['listeners'], fn (array $listener): bool => $listener['class'] === 'Closure');
        expect($closures)->toHaveCount(1)
            ->and(array_values($closures)[0]['name'])->toStartWith('Closure (EventSourcesTest.php:');
    });

    test('it reports string and wildcard events registered on the dispatcher', function (): void {
        Event::listen('orders.archived', SendShipmentSms::class);
        Event::listen('orders.*', SendShipmentSms::class);

        $result = (new EventMapper)->scan();

        expect(findComponent($result, 'orders.archived'))
            ->toMatchArray(['name' => 'orders.archived', 'origin' => 'listener'])
            ->and(findComponent($result, 'orders.*')['listeners'][0]['class'])->toBe(SendShipmentSms::class);
    });

    test('it reports listened events living outside the event paths', function (): void {
        $event = findComponent((new EventMapper)->scan(), PaymentFailed::class);

        expect($event['origin'])->toBe('listener')
            ->and(listenerSources($event['listeners']))->toHaveKey(NotifyFinance::class);
    });

    test('it links events to the models dispatching them through $dispatchesEvents', function (): void {
        $event = findComponent(
            (new EventMapper)->scan(['model_paths' => [fixturePath('Models')]]),
            PostPublished::class,
        );

        expect($event['origin'])->toBe('scanned')
            ->and($event['dispatched_by'])->toBe([['model' => Post::class, 'event' => 'created']]);
    });

    test('it ignores framework listeners and model events', function (): void {
        $result = (new EventMapper)->scan();

        foreach ($result['data'] as $event) {
            expect($event['class'])->not->toStartWith('eloquent.');

            foreach ($event['listeners'] as $listener) {
                expect($listener['class'])->not->toStartWith('Illuminate\\');
            }
        }
    });

    test('scanned events keep their existing keys and get origin and dispatched_by', function (): void {
        $event = findComponent((new EventMapper)->scan(), OrderShipped::class);

        expect($event)->toHaveKeys([
            'class', 'namespace', 'name', 'file', 'traits', 'properties',
            'broadcastable', 'channels', 'listeners', 'flow', 'origin', 'dispatched_by',
        ])->and($event['origin'])->toBe('scanned');

        foreach ($event['listeners'] as $listener) {
            expect($listener)->toHaveKeys(['class', 'name', 'source', 'method']);
        }
    });

    test('listeners report the events they listen to', function (): void {
        Event::listen(OrderShipped::class, SendShipmentSms::class);

        $result = (new ListenerMapper)->scan();

        expect(findComponent($result, UpdateInventory::class)['events'])->toBe([OrderShipped::class])
            ->and(findComponent($result, SendShipmentSms::class)['events'])->toBe([OrderShipped::class])
            ->and(findComponent($result, NotifyFinance::class)['events'])->toBe([PaymentFailed::class]);
    });
});

describe('observer sources', function (): void {
    test('it maps observers to the models they observe, with the registration source', function (): void {
        Post::observe(LoggingObserver::class);

        $result = (new ObserverMapper)->scan([
            'paths' => [fixturePath('Observers')],
            'model_paths' => [fixturePath('Models')],
        ]);

        expect(findComponent($result, PostObserver::class)['models'])
            ->toBe([['model' => Post::class, 'source' => '#[ObservedBy]']])
            ->and(findComponent($result, CommentObserver::class)['models'])
            ->toBe([['model' => Comment::class, 'source' => 'Model::observe()']])
            ->and(findComponent($result, AuditObserver::class)['models'])
            ->toBe([['model' => Comment::class, 'source' => 'Model::observe()']])
            ->and(findComponent($result, LoggingObserver::class)['models'])
            ->toBe([['model' => Post::class, 'source' => 'dispatcher']])
            ->and(findComponent($result, PostObserver::class)['model'])->toBe(Post::class);
    });

    test('it finds observers declared on models outside the observer paths', function (): void {
        $result = (new ObserverMapper)->scan([
            'paths' => [],
            'model_paths' => [fixturePath('Models')],
        ]);

        $classes = array_column($result['data'], 'class');

        expect($classes)->toContain(PostObserver::class, CommentObserver::class, AuditObserver::class);
    });

    test('models report their observers and dispatched events', function (): void {
        $result = (new ModelMapper)->scan(['paths' => [fixturePath('Models')]]);

        $post = findComponent($result, Post::class);
        $comment = findComponent($result, Comment::class);

        expect($post['observers'])->toContain(PostObserver::class)
            ->and($post['dispatches_events'])->toBe(['created' => PostPublished::class])
            ->and($comment['observers'])->toContain(AuditObserver::class, CommentObserver::class);
    });
});

describe('exports', function (): void {
    test('the exporters include listener sources, dispatching models and observed models', function (): void {
        $data = [
            'events' => (new EventMapper)->scan(['model_paths' => [fixturePath('Models')]]),
            'observers' => (new ObserverMapper)->scan([
                'paths' => [fixturePath('Observers')],
                'model_paths' => [fixturePath('Models')],
            ]),
        ];

        $html = (new HtmlLayoutExporter)->render($data);
        $markdown = (new MarkdownExporter)->render($data);
        $json = (new JsonExporter)->render($data);

        expect($html)
            ->toContain('subscriber')
            ->toContain('Dispatched by Models')
            ->toContain('#[ObservedBy]')
            ->and($markdown)->toContain('dispatched_by')->toContain('Model::observe()')
            ->and($json)->toContain('"origin"')->toContain('"models"');
    });
});
