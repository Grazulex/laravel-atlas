<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\ContentService;
use App\Events\PostPublished;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PublishScheduledPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Post $post
    ) {
        $this->onQueue('content');
    }

    /**
     * Execute the job.
     */
    public function handle(ContentService $contentService): void
    {
        Log::info("Publishing scheduled post: {$this->post->title}");

        // Update post status
        $this->post->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Cache the post
        $contentService->cachePost($this->post);

        // Dispatch published event
        event(new PostPublished($this->post, $this->post->tags->pluck('name')->toArray()));

        // Schedule SEO optimization
        OptimizePostSEO::dispatch($this->post)->delay(now()->addMinutes(5));

        Log::info("Scheduled post published successfully: {$this->post->id}");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to publish scheduled post', [
            'post_id' => $this->post->id,
            'error' => $exception->getMessage(),
        ]);

        // Reset post status on failure
        $this->post->update(['status' => 'failed']);
    }
}
