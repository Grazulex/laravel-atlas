<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ContentService
{
    /**
     * Generate SEO-optimized content
     */
    public function optimizeContent(array $postData): array
    {
        Log::info("Optimizing content for post: {$postData['title']}");
        
        // Generate meta descriptions, keywords, etc.
        $postData['meta_title'] = $postData['meta_title'] ?? $this->generateMetaTitle($postData['title']);
        $postData['meta_description'] = $postData['meta_description'] ?? $this->generateMetaDescription($postData['content']);
        
        return $postData;
    }

    /**
     * Generate slug for post
     */
    public function generateSlug(string $title): string
    {
        return \Illuminate\Support\Str::slug($title);
    }

    /**
     * Cache post content
     */
    public function cachePost(Post $post): void
    {
        Cache::put("post.{$post->id}", $post->toArray(), 3600);
        Log::info("Post cached: {$post->id}");
    }

    /**
     * Generate meta title
     */
    private function generateMetaTitle(string $title): string
    {
        return strlen($title) > 60 ? substr($title, 0, 57) . '...' : $title;
    }

    /**
     * Generate meta description
     */
    private function generateMetaDescription(string $content): string
    {
        $description = strip_tags($content);
        $description = preg_replace('/\s+/', ' ', $description);
        
        return strlen($description) > 160 ? substr($description, 0, 157) . '...' : $description;
    }
}
