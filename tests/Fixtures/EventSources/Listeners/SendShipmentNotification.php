<?php

declare(strict_types=1);

namespace Tests\Fixtures\EventSources\Listeners;

use Tests\Fixtures\EventSources\Events\OrderShipped;

class SendShipmentNotification
{
    public function handle(OrderShipped $event): void {}
}
