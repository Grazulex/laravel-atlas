<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\NotificationService;
use App\Events\UserCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessNewUserRegistration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $maxExceptions = 2;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public array $registrationData = []
    ) {
        $this->onQueue('users');
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        Log::info("Processing new user registration: {$this->user->email}");

        // Send welcome email
        $notificationService->sendWelcomeNotification($this->user);

        // Create default settings
        $this->createUserSettings();

        // Dispatch follow-up event
        event(new UserCreated($this->user, $this->user->is_admin, $this->registrationData));

        // Schedule follow-up jobs
        SendOnboardingSequence::dispatch($this->user)->delay(now()->addMinutes(30));
        
        Log::info("User registration processing completed: {$this->user->id}");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to process user registration', [
            'user_id' => $this->user->id,
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * Create default user settings
     */
    private function createUserSettings(): void
    {
        // Create default preferences if not exist
        if (!$this->user->profile) {
            $this->user->profile()->create([
                'bio' => 'New member!',
                'preferences' => [
                    'email_notifications' => true,
                    'newsletter' => true,
                    'theme' => 'light',
                    'language' => 'en',
                ],
            ]);
        }
    }
}
