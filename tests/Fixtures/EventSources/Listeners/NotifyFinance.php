<?php

declare(strict_types=1);

namespace Tests\Fixtures\EventSources\Listeners;

use Tests\Fixtures\EventSources\ExternalEvents\PaymentFailed;

class NotifyFinance
{
    public function handle(PaymentFailed $event): void {}
}
