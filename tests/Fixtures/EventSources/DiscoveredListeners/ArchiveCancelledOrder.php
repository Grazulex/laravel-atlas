<?php

declare(strict_types=1);

namespace Tests\Fixtures\EventSources\DiscoveredListeners;

use Tests\Fixtures\EventSources\Events\OrderCancelled;

class ArchiveCancelledOrder
{
    public function __invoke(OrderCancelled $event): void {}
}
