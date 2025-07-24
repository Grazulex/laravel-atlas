<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\OnboardingStepNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOnboardingSequence implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public int $step = 1
    ) {
        $this->onQueue('notifications');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Sending onboarding step {$this->step} to user: {$this->user->email}");

        // Send current step notification
        $this->user->notify(new OnboardingStepNotification($this->step));

        // Schedule next step if not last
        if ($this->step < 3) {
            self::dispatch($this->user, $this->step + 1)
                ->delay(now()->addDays($this->step));
        }

        Log::info("Onboarding step {$this->step} sent successfully to user: {$this->user->id}");
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return $this->backoff;
    }
}
