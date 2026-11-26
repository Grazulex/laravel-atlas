<?php

declare(strict_types=1);

namespace Tests\Fixtures\EventSources\Events;

use Illuminate\Foundation\Events\Dispatchable;

class OrderShipped
{
    use Dispatchable;

    public function __construct(public int $orderId) {}
}
