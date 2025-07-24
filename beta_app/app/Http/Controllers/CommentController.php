<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Http\Resources\CommentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('throttle:10,1')->only(['store']);
    }

    /**
     * Display comments for a post
     */
    public function index(Post $post, Request $request)
    {
        $comments = $post->comments()
            ->with(['user.profile'])
            ->latest()
            ->paginate(20);

        if ($request->expectsJson()) {
            return CommentResource::collection($comments);
        }

        return view('comments.index', compact('post', 'comments'));
    }

    /**
     * Store a newly created comment
     */
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:3|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = $post->comments()->create([
            'content' => $validated['content'],
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        $comment->load(['user.profile']);

        if ($request->expectsJson()) {
            return new CommentResource($comment);
        }

        return back()->with('success', 'Comment added successfully!');
    }

    /**
     * Display the specified comment
     */
    public function show(Comment $comment, Request $request)
    {
        Gate::authorize('view', $comment);

        $comment->load(['user.profile', 'post', 'replies.user.profile']);

        if ($request->expectsJson()) {
            return new CommentResource($comment);
        }

        return view('comments.show', compact('comment'));
    }

    /**
     * Show the form for editing the specified comment
     */
    public function edit(Comment $comment)
    {
        Gate::authorize('update', $comment);

        return view('comments.edit', compact('comment'));
    }

    /**
     * Update the specified comment
     */
    public function update(Request $request, Comment $comment)
    {
        Gate::authorize('update', $comment);

        $validated = $request->validate([
            'content' => 'required|string|min:3|max:1000',
        ]);

        $comment->update($validated);

        if ($request->expectsJson()) {
            return new CommentResource($comment->load(['user.profile']));
        }

        return redirect()->route('posts.show', $comment->post)
            ->with('success', 'Comment updated successfully!');
    }

    /**
     * Remove the specified comment
     */
    public function destroy(Comment $comment, Request $request)
    {
        Gate::authorize('delete', $comment);

        $postId = $comment->post_id;
        $comment->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Comment deleted successfully']);
        }

        return redirect()->route('posts.show', $postId)
            ->with('success', 'Comment deleted successfully!');
    }

    /**
     * Toggle comment like/unlike
     */
    public function toggleLike(Comment $comment, Request $request)
    {
        $user = Auth::user();
        $isLiked = $user->likedComments()->where('comment_id', $comment->id)->exists();

        if ($isLiked) {
            $user->likedComments()->detach($comment->id);
            $message = 'Comment unliked';
        } else {
            $user->likedComments()->attach($comment->id);
            $message = 'Comment liked';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'is_liked' => !$isLiked,
                'likes_count' => $comment->likes()->count()
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Report a comment
     */
    public function report(Comment $comment, Request $request)
    {
        $validated = $request->validate([
            'reason' => 'required|string|in:spam,inappropriate,abuse,other',
            'description' => 'nullable|string|max:500',
        ]);

        // Create report record (assuming you have a reports table)
        // Report::create([
        //     'user_id' => Auth::id(),
        //     'reportable_type' => Comment::class,
        //     'reportable_id' => $comment->id,
        //     'reason' => $validated['reason'],
        //     'description' => $validated['description'],
        // ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Comment reported successfully']);
        }

        return back()->with('success', 'Comment reported successfully!');
    }

    /**
     * Bulk moderate comments
     */
    public function bulkModerate(Request $request)
    {
        Gate::authorize('moderate', Comment::class);

        $validated = $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id',
        ]);

        $comments = Comment::whereIn('id', $validated['comment_ids'])->get();
        $processed = 0;

        foreach ($comments as $comment) {
            switch ($validated['action']) {
                case 'approve':
                    $comment->update(['is_approved' => true]);
                    $processed++;
                    break;
                    
                case 'reject':
                    $comment->update(['is_approved' => false]);
                    $processed++;
                    break;
                    
                case 'delete':
                    if (Auth::user()->can('delete', $comment)) {
                        $comment->delete();
                        $processed++;
                    }
                    break;
            }
        }

        return response()->json([
            'message' => "Bulk moderation completed. {$processed} comments processed.",
            'processed' => $processed
        ]);
    }
}
