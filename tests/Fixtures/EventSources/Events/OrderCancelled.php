<?php

declare(strict_types=1);

namespace Tests\Fixtures\EventSources\Events;

use Illuminate\Foundation\Events\Dispatchable;

class OrderCancelled
{
    use Dispatchable;
}
