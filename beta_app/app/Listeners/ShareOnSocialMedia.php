<?php

namespace App\Listeners;

use App\Events\PostPublished;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ShareOnSocialMedia
{
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
        // Only share if post is published and has tags
        if ($event->post->status === 'published' && !empty($event->tags)) {
            $this->shareOnTwitter($event->post, $event->tags);
            $this->shareOnLinkedIn($event->post);
        }

        Log::info("Social media sharing completed for post: {$event->post->id}");
    }

    /**
     * Share on Twitter
     */
    private function shareOnTwitter($post, array $tags): void
    {
        $hashtags = collect($tags)
            ->map(fn($tag) => '#' . str_replace(' ', '', $tag))
            ->take(3)
            ->join(' ');

        $tweetText = "{$post->title} {$hashtags}";
        
        Log::info("Sharing on Twitter: {$tweetText}");
        
        // Mock Twitter API call
        // Http::withHeaders([
        //     'Authorization' => 'Bearer ' . config('services.twitter.bearer_token'),
        // ])->post('https://api.twitter.com/2/tweets', [
        //     'text' => $tweetText,
        // ]);
    }

    /**
     * Share on LinkedIn
     */
    private function shareOnLinkedIn($post): void
    {
        Log::info("Sharing on LinkedIn: {$post->title}");
        
        // Mock LinkedIn API call
        // Http::withHeaders([
        //     'Authorization' => 'Bearer ' . config('services.linkedin.access_token'),
        // ])->post('https://api.linkedin.com/v2/shares', [
        //     'content' => [
        //         'title' => $post->title,
        //         'description' => $post->excerpt,
        //     ],
        // ]);
    }
}
