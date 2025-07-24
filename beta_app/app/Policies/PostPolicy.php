<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use App\Services\ContentService;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class PostPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct(
        private ContentService $contentService
    ) {
        //
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        Log::info("Policy check: viewAny posts for user {$user->id}");
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Post $post): Response
    {
        // Published posts are public
        if ($post->status === 'published') {
            Log::info("Policy check: view published post {$post->id}");
            return Response::allow();
        }

        // Draft posts only visible to author and admins
        if ($user && ($post->user_id === $user->id || $user->is_admin)) {
            Log::info("Policy check: view draft post {$post->id} by author/admin");
            return Response::allow();
        }

        Log::warning("Policy check: denied view access to post {$post->id}", [
            'user_id' => $user?->id,
            'post_status' => $post->status,
        ]);

        return Response::deny('You do not have permission to view this post.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        // Check if user has reached post limit
        $userPostsCount = $user->posts()->count();
        $maxPosts = $user->is_admin ? 1000 : 50;

        if ($userPostsCount >= $maxPosts) {
            Log::warning("Policy check: user {$user->id} reached post limit ({$userPostsCount}/{$maxPosts})");
            return Response::deny("You have reached the maximum number of posts ({$maxPosts}).");
        }

        Log::info("Policy check: create post allowed for user {$user->id} ({$userPostsCount}/{$maxPosts})");
        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): Response
    {
        // Author can always update their own posts
        if ($post->user_id === $user->id) {
            Log::info("Policy check: update post {$post->id} by author {$user->id}");
            return Response::allow();
        }

        // Admins can update any post
        if ($user->is_admin) {
            Log::info("Policy check: update post {$post->id} by admin {$user->id}");
            return Response::allow();
        }

        Log::warning("Policy check: denied update access to post {$post->id}", [
            'user_id' => $user->id,
            'post_author_id' => $post->user_id,
        ]);

        return Response::deny('You can only update your own posts.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): Response
    {
        // Cannot delete published posts unless admin
        if ($post->status === 'published' && !$user->is_admin) {
            Log::warning("Policy check: non-admin {$user->id} tried to delete published post {$post->id}");
            return Response::deny('Published posts cannot be deleted.');
        }

        // Author can delete their own posts
        if ($post->user_id === $user->id) {
            Log::info("Policy check: delete post {$post->id} by author {$user->id}");
            return Response::allow();
        }

        // Admins can delete any post
        if ($user->is_admin) {
            Log::info("Policy check: delete post {$post->id} by admin {$user->id}");
            return Response::allow();
        }

        Log::warning("Policy check: denied delete access to post {$post->id}", [
            'user_id' => $user->id,
            'post_author_id' => $post->user_id,
        ]);

        return Response::deny('You can only delete your own posts.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Post $post): bool
    {
        Log::info("Policy check: restore post {$post->id} by user {$user->id}");
        return $user->is_admin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        Log::info("Policy check: force delete post {$post->id} by user {$user->id}");
        return $user->is_admin;
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, Post $post): Response
    {
        // Only author or admin can publish
        if ($post->user_id === $user->id || $user->is_admin) {
            // Check content quality using service
            if ($this->contentService->generateSlug($post->title)) {
                Log::info("Policy check: publish post {$post->id} allowed for user {$user->id}");
                return Response::allow();
            }
        }

        Log::warning("Policy check: denied publish access to post {$post->id}", [
            'user_id' => $user->id,
            'post_author_id' => $post->user_id,
        ]);

        return Response::deny('You cannot publish this post.');
    }
}
