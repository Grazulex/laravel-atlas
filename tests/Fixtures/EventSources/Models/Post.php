<?php

declare(strict_types=1);

namespace Tests\Fixtures\EventSources\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Tests\Fixtures\EventSources\Events\PostPublished;
use Tests\Fixtures\EventSources\Observers\PostObserver;

#[ObservedBy([PostObserver::class])]
class Post extends Model
{
    /**
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => PostPublished::class,
    ];
}
