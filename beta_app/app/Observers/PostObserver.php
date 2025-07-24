<?php

namespace App\Observers;

use App\Models\Post;

class PostObserver
{
    /**
     * Handle the Post "creating" event.
     */
    public function creating(Post $post): void
    {
        // Auto-generate excerpt if not provided
        if (empty($post->excerpt) && !empty($post->content)) {
            $post->excerpt = substr(strip_tags($post->content), 0, 200) . '...';
        }
        
        // Auto-generate slug from title if not provided
        if (empty($post->slug) && !empty($post->title)) {
            $post->slug = \Str::slug($post->title);
        }
    }

    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        \Log::info("New post created: '{$post->title}' by user {$post->user_id}");
        
        // Notify subscribers about new post
        // event(new PostCreated($post));
    }

    /**
     * Handle the Post "updating" event.
     */
    public function updating(Post $post): void
    {
        // Update published_at when status changes to published
        if ($post->isDirty('status') && $post->status === 'published' && empty($post->published_at)) {
            $post->published_at = now();
        }
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        if ($post->wasChanged('status')) {
            \Log::info("Post '{$post->title}' status changed to: {$post->status}");
        }
        
        // Clear cache when post is updated
        \Cache::forget("post.{$post->id}");
        \Cache::tags(['posts'])->flush();
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        \Log::info("Post deleted: '{$post->title}'");
        
        // Also delete associated comments
        $post->comments()->delete();
        
        // Clear related caches
        \Cache::forget("post.{$post->id}");
        \Cache::tags(['posts'])->flush();
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post): void
    {
        \Log::info("Post restored: '{$post->title}'");
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Post $post): void
    {
        \Log::warning("Post permanently deleted: '{$post->title}'");
    }
}
