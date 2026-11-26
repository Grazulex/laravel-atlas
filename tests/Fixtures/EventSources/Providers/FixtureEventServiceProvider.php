<?php

declare(strict_types=1);

namespace Tests\Fixtures\EventSources\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Tests\Fixtures\EventSources\Events\OrderShipped;
use Tests\Fixtures\EventSources\Listeners\SendShipmentNotification;
use Tests\Fixtures\EventSources\Subscribers\InvoiceSubscriber;

class FixtureEventServiceProvider extends EventServiceProvider
{
    /**
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        OrderShipped::class => [
            SendShipmentNotification::class,
        ],
    ];

    /**
     * @var array<int, class-string>
     */
    protected $subscribe = [
        InvoiceSubscriber::class,
    ];
}
