<?php

declare(strict_types=1);

namespace Tests\Fixtures\EventSources\Subscribers;

use Illuminate\Events\Dispatcher;
use Tests\Fixtures\EventSources\Events\InvoicePaid;

class InvoiceSubscriber
{
    public function handleInvoicePaid(InvoicePaid $event): void {}

    /**
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            InvoicePaid::class => 'handleInvoicePaid',
        ];
    }
}
