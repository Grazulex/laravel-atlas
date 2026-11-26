<?php

declare(strict_types=1);

namespace Tests\Fixtures\EventSources\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Tests\Fixtures\EventSources\Models\Post;

class PostPublished
{
    use Dispatchable;

    public function __construct(public Post $post) {}
}
