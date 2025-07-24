<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Comment $comment;
    public Post $post;
    public User $commenter;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
        $this->post = $comment->post;
        $this->commenter = $comment->user;
        $this->onQueue('notifications');
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Send email if user wants comment notifications
        if ($notifiable->notification_preferences['comment_notifications'] ?? true) {
            $channels[] = 'mail';
        }

        // Real-time notification for post author
        if ($notifiable->id === $this->post->user_id) {
            $channels[] = 'broadcast';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New comment on your post: ' . $this->post->title)
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line($this->commenter->name . ' commented on your post "' . $this->post->title . '"')
                    ->line('"' . substr($this->comment->content, 0, 200) . '..."')
                    ->action('View Comment', route('posts.show', [$this->post, 'comment' => $this->comment->id]))
                    ->line('Thank you for creating great content!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'new_comment',
            'comment_id' => $this->comment->id,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'commenter_id' => $this->commenter->id,
            'commenter_name' => $this->commenter->name,
            'commenter_avatar' => $this->commenter->profile?->avatar,
            'comment_excerpt' => substr($this->comment->content, 0, 150),
            'action_url' => route('posts.show', [$this->post, 'comment' => $this->comment->id]),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): array
    {
        return [
            'id' => $this->id,
            'type' => 'new_comment',
            'title' => 'New Comment',
            'message' => $this->commenter->name . ' commented on your post',
            'comment' => [
                'id' => $this->comment->id,
                'content' => substr($this->comment->content, 0, 100),
                'created_at' => $this->comment->created_at,
            ],
            'post' => [
                'id' => $this->post->id,
                'title' => $this->post->title,
            ],
            'commenter' => [
                'id' => $this->commenter->id,
                'name' => $this->commenter->name,
                'avatar' => $this->commenter->profile?->avatar,
            ],
            'action' => [
                'text' => 'View Comment',
                'url' => route('posts.show', [$this->post, 'comment' => $this->comment->id]),
            ],
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
        ];
    }
}
