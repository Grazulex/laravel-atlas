<?php

declare(strict_types=1);

namespace Tests\Fixtures\EventSources\Observers;

use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    public function created(Model $model): void {}

    public function updated(Model $model): void {}
}
