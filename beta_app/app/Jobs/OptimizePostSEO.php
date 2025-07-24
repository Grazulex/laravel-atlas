<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OptimizePostSEO implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Post $post
    ) {
        $this->onQueue('seo');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Optimizing SEO for post: {$this->post->title}");

        // Generate meta descriptions
        $optimizedMeta = $this->generateSEOMetadata();

        // Update post with SEO data
        $this->post->update([
            'meta_title' => $optimizedMeta['title'],
            'meta_description' => $optimizedMeta['description'],
        ]);

        // Submit to search engines (mock)
        $this->submitToSearchEngines();

        Log::info("SEO optimization completed for post: {$this->post->id}");
    }

    /**
     * Generate SEO metadata
     */
    private function generateSEOMetadata(): array
    {
        return [
            'title' => $this->optimizeTitle($this->post->title),
            'description' => $this->optimizeDescription($this->post->content),
        ];
    }

    /**
     * Optimize title for SEO
     */
    private function optimizeTitle(string $title): string
    {
        // Add SEO keywords, limit length
        $optimized = $title;
        if (strlen($optimized) > 60) {
            $optimized = substr($optimized, 0, 57) . '...';
        }
        return $optimized;
    }

    /**
     * Optimize description for SEO
     */
    private function optimizeDescription(string $content): string
    {
        $description = strip_tags($content);
        $description = preg_replace('/\s+/', ' ', $description);
        
        if (strlen($description) > 160) {
            $description = substr($description, 0, 157) . '...';
        }
        
        return trim($description);
    }

    /**
     * Submit to search engines
     */
    private function submitToSearchEngines(): void
    {
        Log::info("Submitting post to search engines: {$this->post->id}");
        
        // Mock search engine submission
        // Http::post('https://www.google.com/ping', [
        //     'sitemap' => url('/sitemap.xml')
        // ]);
    }
}
