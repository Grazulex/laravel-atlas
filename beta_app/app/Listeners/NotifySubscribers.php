<?php

namespace App\Listeners;

use App\Events\PostPublished;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class NotifySubscribers implements ShouldQueue
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
    public function handle(PostPublished $event): void
    {
        Log::info("Notifying subscribers about new post: {$event->post->title}");
        
        // Get subscribers (mock implementation)
        $subscribers = $this->getSubscribers($event->post->category_id);
        
        foreach ($subscribers as $subscriber) {
            // Send notification
            Log::info("Sending notification to subscriber: {$subscriber['email']}");
            // Mail::to($subscriber['email'])->send(new PostPublishedNotification($event->post));
        }

        // Update post stats
        $this->updatePostStats($event->post);
    }

    /**
     * Get subscribers for category
     */
    private function getSubscribers(?int $categoryId): array
    {
        // Mock subscribers data
        return [
            ['email' => 'subscriber1@example.com', 'name' => 'John Doe'],
            ['email' => 'subscriber2@example.com', 'name' => 'Jane Smith'],
        ];
    }

    /**
     * Update post statistics
     */
    private function updatePostStats($post): void
    {
        Cache::increment("post_notifications_sent.{$post->id}");
        Log::info("Updated notification stats for post: {$post->id}");
    }
}
