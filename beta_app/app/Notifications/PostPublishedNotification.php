<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use App\Services\ContentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class PostPublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Post $post;
    public User $author;
    protected ContentService $contentService;

    /**
     * Create a new notification instance.
     */
    public function __construct(Post $post, ContentService $contentService = null)
    {
        $this->post = $post;
        $this->author = $post->user;
        $this->contentService = $contentService ?? app(ContentService::class);
        $this->onQueue('notifications');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Only send email if user has subscribed to author or category
        if ($this->shouldSendEmail($notifiable)) {
            $channels[] = 'mail';
        }

        // Real-time notification for followers
        if ($this->isFollowing($notifiable)) {
            $channels[] = 'broadcast';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $unsubscribeUrl = URL::signedRoute('notifications.unsubscribe', [
            'user' => $notifiable->id,
            'author' => $this->author->id,
        ]);

        return (new MailMessage)
                    ->subject($this->author->name . ' published a new post: ' . $this->post->title)
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line($this->author->name . ' has published a new post that might interest you.')
                    ->line('**' . $this->post->title . '**')
                    ->line($this->post->excerpt ?? $this->contentService->generateExcerpt($this->post->content))
                    ->action('Read Post', route('posts.show', $this->post))
                    ->line('Post published in: ' . $this->post->category->name)
                    ->line('Tags: ' . $this->post->tags->pluck('name')->join(', '))
                    ->line('Thank you for following ' . $this->author->name . '!')
                    ->line('If you no longer wish to receive these notifications, click the link below.')
                    ->action('Unsubscribe', $unsubscribeUrl);
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'post_published',
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'post_slug' => $this->post->slug,
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'author_avatar' => $this->author->profile?->avatar,
            'category_id' => $this->post->category_id,
            'category_name' => $this->post->category->name,
            'excerpt' => $this->post->excerpt ?? $this->contentService->generateExcerpt($this->post->content, 150),
            'published_at' => $this->post->published_at,
            'action_url' => route('posts.show', $this->post),
            'tags' => $this->post->tags->pluck('name')->toArray(),
            'estimated_read_time' => $this->contentService->calculateReadTime($this->post->content),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): array
    {
        return [
            'id' => $this->id,
            'type' => 'post_published',
            'title' => 'New Post from ' . $this->author->name,
            'message' => $this->post->title,
            'post' => [
                'id' => $this->post->id,
                'title' => $this->post->title,
                'slug' => $this->post->slug,
                'excerpt' => $this->post->excerpt,
                'featured_image' => $this->post->featured_image,
            ],
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->name,
                'avatar' => $this->author->profile?->avatar,
            ],
            'action' => [
                'text' => 'Read Now',
                'url' => route('posts.show', $this->post),
            ],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Check if user should receive email notification
     */
    protected function shouldSendEmail(object $notifiable): bool
    {
        // Check user preferences
        if (!($notifiable->notification_preferences['email_notifications'] ?? true)) {
            return false;
        }

        // Check if user is following the author
        if ($this->isFollowing($notifiable)) {
            return true;
        }

        // Check if user is subscribed to the category
        if ($notifiable->subscribedCategories()->where('category_id', $this->post->category_id)->exists()) {
            return true;
        }

        // Check if user has interacted with similar posts
        return $this->contentService->hasInterestInContent($notifiable, $this->post);
    }

    /**
     * Check if user is following the author
     */
    protected function isFollowing(object $notifiable): bool
    {
        return $notifiable->following()->where('followed_user_id', $this->author->id)->exists();
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

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('PostPublishedNotification failed', [
            'post_id' => $this->post->id,
            'author_id' => $this->author->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
