<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\UserCreated;
use App\Events\PostPublished;
use App\Events\PostScheduled;
use App\Listeners\SendWelcomeEmail;
use App\Listeners\CreateDefaultProfile;
use App\Listeners\NotifySubscribers;
use App\Listeners\ShareOnSocialMedia;
use App\Listeners\SchedulePostPublication;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserCreated::class => [
            SendWelcomeEmail::class,
            CreateDefaultProfile::class,
        ],
        PostPublished::class => [
            NotifySubscribers::class,
            ShareOnSocialMedia::class,
        ],
        PostScheduled::class => [
            SchedulePostPublication::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
