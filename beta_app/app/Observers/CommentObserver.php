<?php

namespace App\Observers;

use App\Models\Comment;

class CommentObserver
{
    /**
     * Handle the Comment "creating" event.
     */
    public function creating(Comment $comment): void
    {
        // Set default status for new comments
        if (empty($comment->status)) {
            $comment->status = 'pending';
        }
        
        // Store IP address for moderation
        $comment->ip_address = request()->ip();
        
        // Auto-moderate based on content (simple example)
        if ($this->containsSpam($comment->content)) {
            $comment->status = 'spam';
        }
    }

    /**
     * Handle the Comment "created" event.
     */
    public function created(Comment $comment): void
    {
        \Log::info("New comment created on post {$comment->post_id} by {$comment->author_name}");
        
        // Notify post author about new comment
        if ($comment->post && $comment->post->user) {
            // Notification logic here
        }
    }

    /**
     * Handle the Comment "updated" event.
     */
    public function updated(Comment $comment): void
    {
        if ($comment->wasChanged('status')) {
            \Log::info("Comment {$comment->id} status changed to: {$comment->status}");
            
            // If approved, notify the commenter
            if ($comment->status === 'approved' && $comment->author_email) {
                // Send approval notification
            }
        }
    }

    /**
     * Handle the Comment "deleted" event.
     */
    public function deleted(Comment $comment): void
    {
        \Log::info("Comment deleted: ID {$comment->id}");
        
        // Also delete replies
        $comment->replies()->delete();
    }

    /**
     * Simple spam detection method
     */
    private function containsSpam(string $content): bool
    {
        $spamKeywords = ['viagra', 'casino', 'lottery', 'click here', 'buy now'];
        
        foreach ($spamKeywords as $keyword) {
            if (stripos($content, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Handle the Comment "restored" event.
     */
    public function restored(Comment $comment): void
    {
        \Log::info("Comment restored: ID {$comment->id}");
    }

    /**
     * Handle the Comment "force deleted" event.
     */
    public function forceDeleted(Comment $comment): void
    {
        \Log::info("Comment permanently deleted: ID {$comment->id}");
    }
}
