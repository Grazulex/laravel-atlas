<?php

namespace App\Rules;

use App\Models\Post;
use App\Services\ContentService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class UniquePostTitle implements ValidationRule
{
    protected ?int $userId;
    protected ?int $excludePostId;
    protected ContentService $contentService;

    /**
     * Create a new rule instance.
     */
    public function __construct(?int $userId = null, ?int $excludePostId = null)
    {
        $this->userId = $userId;
        $this->excludePostId = $excludePostId;
        $this->contentService = app(ContentService::class);
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('The :attribute must be a string.');
            return;
        }

        // Cache key for performance
        $cacheKey = 'unique_post_title_' . Str::slug($value) . '_' . ($this->userId ?? 'any');

        $existingPost = Cache::remember($cacheKey, 300, function () use ($value) {
            $query = Post::where('title', $value);

            // If checking for a specific user
            if ($this->userId) {
                $query->where('user_id', $this->userId);
            }

            // Exclude current post being updated
            if ($this->excludePostId) {
                $query->where('id', '!=', $this->excludePostId);
            }

            return $query->first();
        });

        if ($existingPost) {
            if ($this->userId && $existingPost->user_id === $this->userId) {
                $fail('You have already used this title for another post.');
            } else {
                $fail('This title has already been used by another author.');
            }
            return;
        }

        // Check for very similar titles (fuzzy matching)
        if ($this->hasSimilarTitle($value)) {
            $fail('A very similar title already exists. Please choose a more distinctive title.');
            return;
        }

        // Check if title is too generic
        if ($this->isTooGeneric($value)) {
            $fail('This title is too generic. Please be more specific.');
        }
    }

    /**
     * Check for similar titles using fuzzy matching
     */
    protected function hasSimilarTitle(string $title): bool
    {
        $normalizedTitle = $this->normalizeTitle($title);
        
        // Get recent posts to compare against
        $recentPosts = Cache::remember('recent_post_titles', 600, function () {
            return Post::where('created_at', '>', now()->subMonths(3))
                ->select('id', 'title', 'user_id')
                ->get();
        });

        foreach ($recentPosts as $post) {
            // Skip if it's the same post being updated
            if ($this->excludePostId && $post->id === $this->excludePostId) {
                continue;
            }

            $normalizedExisting = $this->normalizeTitle($post->title);
            
            // Calculate similarity
            $similarity = $this->calculateSimilarity($normalizedTitle, $normalizedExisting);
            
            // If titles are 85% similar and from different authors (or same author if specified)
            if ($similarity > 0.85) {
                if (!$this->userId || $post->user_id === $this->userId) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Normalize title for comparison
     */
    protected function normalizeTitle(string $title): string
    {
        // Convert to lowercase, remove special characters, normalize whitespace
        $normalized = strtolower($title);
        $normalized = preg_replace('/[^a-z0-9\s]/', '', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        return trim($normalized);
    }

    /**
     * Calculate similarity between two titles
     */
    protected function calculateSimilarity(string $title1, string $title2): float
    {
        // Use Levenshtein distance for similarity
        $maxLength = max(strlen($title1), strlen($title2));
        
        if ($maxLength === 0) {
            return 1.0;
        }

        $distance = levenshtein($title1, $title2);
        return 1 - ($distance / $maxLength);
    }

    /**
     * Check if title is too generic
     */
    protected function isTooGeneric(string $title): bool
    {
        $genericWords = [
            'how to', 'guide', 'tutorial', 'tips', 'tricks', 'best practices',
            'introduction', 'getting started', 'overview', 'basics', 'fundamentals',
            'complete guide', 'ultimate guide', 'beginners guide'
        ];

        $normalizedTitle = strtolower($title);
        
        foreach ($genericWords as $generic) {
            if (str_contains($normalizedTitle, $generic)) {
                // Check if the title is ONLY generic words (too generic)
                $wordCount = str_word_count($title);
                $genericWordCount = str_word_count($generic);
                
                // If more than 70% of the title is generic words
                if ($genericWordCount / $wordCount > 0.7) {
                    return true;
                }
            }
        }

        // Check for very short titles that might be too generic
        if (str_word_count($title) <= 2) {
            $commonShortTitles = ['news', 'update', 'blog', 'post', 'article', 'story'];
            foreach ($commonShortTitles as $common) {
                if (str_contains($normalizedTitle, $common)) {
                    return true;
                }
            }
        }

        return false;
    }
}
