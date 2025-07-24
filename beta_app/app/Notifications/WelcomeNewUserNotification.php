<?php

namespace App\Notifications;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class WelcomeNewUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $newUser;
    protected NotificationService $notificationService;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $newUser, NotificationService $notificationService = null)
    {
        $this->newUser = $newUser;
        $this->notificationService = $notificationService ?? app(NotificationService::class);
        $this->onQueue('notifications');
        $this->delay(now()->addMinutes(2)); // Small delay for welcome experience
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Check user preferences for email notifications
        if ($this->newUser->notification_preferences['email_notifications'] ?? true) {
            $channels[] = 'mail';
        }

        // Add broadcast for real-time notifications if user is online
        if ($this->notificationService->isUserOnline($this->newUser->id)) {
            $channels[] = 'broadcast';
        }

        // Add SMS for premium users or admins
        if ($this->newUser->is_premium || $this->newUser->is_admin) {
            $channels[] = 'nexmo'; // or your SMS provider
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $this->newUser->id, 'hash' => sha1($this->newUser->email)]
        );

        return (new MailMessage)
                    ->subject('Welcome to ' . config('app.name') . '!')
                    ->greeting('Welcome, ' . $this->newUser->name . '!')
                    ->line('Thank you for joining our community. We\'re excited to have you on board.')
                    ->line('Your account has been successfully created with the email: ' . $this->newUser->email)
                    ->action('Verify Your Email', $verificationUrl)
                    ->line('If you did not create this account, please ignore this email.')
                    ->line('Welcome aboard!')
                    ->with([
                        'user' => $this->newUser,
                        'onboarding_tips' => $this->notificationService->getOnboardingTips($this->newUser),
                    ]);
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'welcome',
            'user_id' => $this->newUser->id,
            'user_name' => $this->newUser->name,
            'user_email' => $this->newUser->email,
            'message' => "Welcome {$this->newUser->name}! Your account has been successfully created.",
            'action_url' => route('profile.show', $this->newUser),
            'created_at' => now(),
            'metadata' => [
                'registration_ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'referrer' => request()->headers->get('referer'),
            ],
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'type' => 'welcome',
            'title' => 'Welcome to ' . config('app.name'),
            'message' => "Welcome {$this->newUser->name}! Your account is ready.",
            'user' => [
                'id' => $this->newUser->id,
                'name' => $this->newUser->name,
                'avatar' => $this->newUser->profile?->avatar,
            ],
            'action' => [
                'text' => 'View Profile',
                'url' => route('profile.show', $this->newUser),
            ],
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toNexmo(object $notifiable): array
    {
        return [
            'content' => "Welcome to " . config('app.name') . ", {$this->newUser->name}! Your account is ready. Verify your email to get started.",
        ];
    }

    /**
     * Determine which queues should be used for each notification channel.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'notifications',
            'database' => 'notifications',
            'broadcast' => 'broadcasting',
            'nexmo' => 'sms',
        ];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Log the failure and create a fallback notification
        \Log::error('WelcomeNewUserNotification failed', [
            'user_id' => $this->newUser->id,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // Create a simple database notification as fallback
        $this->newUser->notify(new SimpleWelcomeNotification($this->newUser));
    }
}
