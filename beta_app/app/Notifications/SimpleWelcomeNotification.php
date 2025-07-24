<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class SimpleWelcomeNotification extends Notification
{
    public User $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'simple_welcome',
            'user_id' => $this->user->id,
            'message' => "Welcome {$this->user->name}! Your account is ready.",
            'action_url' => route('profile.show', $this->user),
        ];
    }
}
