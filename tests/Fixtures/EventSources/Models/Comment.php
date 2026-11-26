<?php

declare(strict_types=1);

namespace Tests\Fixtures\EventSources\Models;

use Illuminate\Database\Eloquent\Model;
use Tests\Fixtures\EventSources\Observers\AuditObserver;

class Comment extends Model
{
    protected static function booted(): void
    {
        static::observe(AuditObserver::class);
    }
}
