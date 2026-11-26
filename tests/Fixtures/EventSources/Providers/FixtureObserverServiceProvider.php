<?php

declare(strict_types=1);

namespace Tests\Fixtures\EventSources\Providers;

use Illuminate\Support\ServiceProvider;
use Tests\Fixtures\EventSources\Models\Comment;
use Tests\Fixtures\EventSources\Observers\CommentObserver;

class FixtureObserverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Comment::observe(CommentObserver::class);
    }
}
