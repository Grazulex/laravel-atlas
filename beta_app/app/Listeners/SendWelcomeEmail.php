<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\Profile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
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
        Log::info("Sending welcome email to user: {$event->user->email}");
        
        // Send welcome email
        // Mail::to($event->user)->send(new WelcomeEmail($event->user));
        
        // Log the action
        Log::channel('user-activity')->info('Welcome email sent', [
            'user_id' => $event->user->id,
            'user_email' => $event->user->email,
            'is_admin' => $event->isAdmin,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(UserCreated $event, \Throwable $exception): void
    {
        Log::error('Failed to send welcome email', [
            'user_id' => $event->user->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
