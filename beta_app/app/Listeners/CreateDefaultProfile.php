<?php

namespace App\Listeners;

use App\Events\UserCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CreateDefaultProfile implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreated $event): void
    {
        // Only create profile if one doesn't exist and no profile data was provided
        if (!$event->user->profile && empty($event->profileData)) {
            $event->user->profile()->create([
                'bio' => 'New member of the community!',
                'preferences' => [
                    'notifications' => true,
                    'newsletter' => true,
                    'theme' => 'light',
                ],
                'social_media' => [],
            ]);

            Log::info("Default profile created for user: {$event->user->id}");
        }

        // Set admin-specific defaults
        if ($event->isAdmin) {
            $event->user->profile()->updateOrCreate([], [
                'bio' => 'Administrator',
                'preferences' => array_merge(
                    $event->user->profile->preferences ?? [],
                    ['admin_notifications' => true]
                ),
            ]);

            Log::info("Admin profile updated for user: {$event->user->id}");
        }
    }
}
