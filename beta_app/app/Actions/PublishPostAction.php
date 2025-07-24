<?php

namespace App\Actions;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Events\PostPublished;
use App\Events\PostScheduled;
use App\Services\NotificationService;
use App\Services\ContentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Carbon\Carbon;

class PublishPostAction
{
    /**
     * Create a new PublishPostAction instance.
     */
    public function __construct(
        private NotificationService $notificationService,
        private ContentService $contentService
    ) {
        //
    }
    /**
     * Publish a post with tags and category
     *
     * @param array $postData
     * @param array $tagNames
     * @param User|int|null $user
     * @return Post
     * @throws InvalidArgumentException
     */
    public function __invoke(array $postData, array $tagNames = [], User|int $user = null): Post
    {
        $this->validatePostData($postData);

        // Optimize content using injected service
        $postData = $this->contentService->optimizeContent($postData);

        return DB::transaction(function () use ($postData, $tagNames, $user) {
            // Determine user
            if ($user instanceof User) {
                $userId = $user->id;
            } elseif (is_int($user)) {
                $userId = $user;
            } else {
                $userId = auth()->id();
            }

            if (!$userId) {
                throw new InvalidArgumentException('User is required to publish post');
            }

            // Create the post
            $post = Post::create([
                'title' => $postData['title'],
                'content' => $postData['content'],
                'excerpt' => $postData['excerpt'] ?? $this->generateExcerpt($postData['content']),
                'user_id' => $userId,
                'category_id' => $postData['category_id'] ?? null,
                'status' => $postData['status'] ?? 'published',
                'published_at' => $postData['published_at'] ?? now(),
                'featured_image' => $postData['featured_image'] ?? null,
                'meta_title' => $postData['meta_title'] ?? $postData['title'],
                'meta_description' => $postData['meta_description'] ?? $this->generateExcerpt($postData['content']),
            ]);

            // Handle tags
            if (!empty($tagNames)) {
                $tagIds = $this->getOrCreateTags($tagNames);
                $post->tags()->sync($tagIds);
            }

            $postWithRelations = $post->fresh(['user', 'category', 'tags']);

            // Cache the post
            $this->contentService->cachePost($postWithRelations);

            // Dispatch events based on status
            if ($postData['status'] === 'published') {
                event(new PostPublished($postWithRelations, $tagNames));
                $this->notificationService->sendPostPublishedNotification($postWithRelations);
            }

            return $postWithRelations;
        });
    }

    /**
     * Execute the action (alternative method name)
     */
    public function execute(array $postData, array $tagNames = [], User|int $user = null): Post
    {
        return $this($postData, $tagNames, $user);
    }

    /**
     * Publish as draft
     */
    public function publishAsDraft(array $postData, array $tagNames = [], User|int $user = null): Post
    {
        $postData['status'] = 'draft';
        $postData['published_at'] = null;
        return $this($postData, $tagNames, $user);
    }

    /**
     * Schedule post for later
     */
    public function schedulePost(array $postData, Carbon $publishDate, array $tagNames = [], User|int $user = null): Post
    {
        if ($publishDate->isPast()) {
            throw new InvalidArgumentException('Cannot schedule post in the past');
        }

        $postData['status'] = 'scheduled';
        $postData['published_at'] = $publishDate;
        
        $post = $this($postData, $tagNames, $user);
        
        // Dispatch scheduled event
        \Illuminate\Support\Facades\Event::dispatch(new PostScheduled($post, $publishDate));
        
        return $post;
    }

    /**
     * Validate post data
     */
    private function validatePostData(array $postData): void
    {
        if (empty($postData['title'])) {
            throw new InvalidArgumentException('Post title is required');
        }

        if (empty($postData['content'])) {
            throw new InvalidArgumentException('Post content is required');
        }

        if (strlen($postData['title']) > 255) {
            throw new InvalidArgumentException('Post title is too long (max 255 characters)');
        }

        if (isset($postData['status']) && !in_array($postData['status'], ['draft', 'published', 'scheduled', 'archived'])) {
            throw new InvalidArgumentException('Invalid post status');
        }
    }

    /**
     * Generate excerpt from content
     */
    private function generateExcerpt(string $content, int $length = 160): string
    {
        $excerpt = strip_tags($content);
        $excerpt = preg_replace('/\s+/', ' ', $excerpt);
        
        if (strlen($excerpt) <= $length) {
            return trim($excerpt);
        }

        return trim(substr($excerpt, 0, $length)) . '...';
    }

    /**
     * Get or create tags
     */
    private function getOrCreateTags(array $tagNames): array
    {
        $tagIds = [];

        foreach ($tagNames as $tagName) {
            $tag = Tag::firstOrCreate([
                'name' => trim($tagName)
            ], [
                'slug' => Str::slug(trim($tagName)),
                'description' => null,
                'color' => $this->generateRandomColor(),
                'is_featured' => false,
            ]);

            $tagIds[] = $tag->id;
        }

        return $tagIds;
    }

    /**
     * Generate random color for tags
     */
    private function generateRandomColor(): string
    {
        $colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
            '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9'
        ];

        return $colors[array_rand($colors)];
    }

    /**
     * Bulk publish posts
     */
    public function bulkPublish(array $postsData, User|int $user = null): array
    {
        $publishedPosts = [];

        DB::transaction(function () use ($postsData, $user, &$publishedPosts) {
            foreach ($postsData as $postData) {
                $tags = $postData['tags'] ?? [];
                unset($postData['tags']);
                
                $publishedPosts[] = $this($postData, $tags, $user);
            }
        });

        // Dispatch bulk publish event using dispatch helper
        dispatch(function () use ($publishedPosts) {
            \Illuminate\Support\Facades\Log::info('Bulk published ' . count($publishedPosts) . ' posts');
        });

        return $publishedPosts;
    }
}
