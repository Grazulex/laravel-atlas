<?php

declare(strict_types=1);

namespace Tests\Fixtures\EventSources\Listeners;

/**
 * No type-hint: only known through Event::listen().
 */
class SendShipmentSms
{
    public function handle(object $event): void {}
}
