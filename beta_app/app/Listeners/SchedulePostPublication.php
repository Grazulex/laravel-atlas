<?php

namespace App\Listeners;

use App\Events\PostScheduled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SchedulePostPublication implements ShouldQueue
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
    public function handle(PostScheduled $event): void
    {
        Log::info("Scheduling post publication: {$event->post->title} for {$event->scheduledFor->format('Y-m-d H:i:s')}");
        
        // Store scheduled post in cache for processing
        $scheduledPosts = Cache::get('scheduled_posts', []);
        $scheduledPosts[] = [
            'post_id' => $event->post->id,
            'scheduled_for' => $event->scheduledFor->toISOString(),
            'title' => $event->post->title,
            'user_id' => $event->post->user_id,
        ];
        
        Cache::put('scheduled_posts', $scheduledPosts, now()->addDays(30));
        
        // Create a job to be executed at the scheduled time
        // PublishScheduledPostJob::dispatch($event->post)->delay($event->scheduledFor);
        
        Log::info("Post scheduled successfully: {$event->post->id}");
    }

    /**
     * Handle a job failure.
     */
    public function failed(PostScheduled $event, \Throwable $exception): void
    {
        Log::error('Failed to schedule post publication', [
            'post_id' => $event->post->id,
            'scheduled_for' => $event->scheduledFor->toISOString(),
            'error' => $exception->getMessage(),
        ]);
    }
}
