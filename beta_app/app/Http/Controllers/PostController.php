<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\ContentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function __construct(
        protected ContentService $contentService
    ) {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('throttle:60,1')->only(['store', 'update']);
    }

    /**
     * Display a listing of posts
     */
    public function index(Request $request)
    {
        $query = Post::with(['user', 'category', 'tags'])
            ->published()
            ->latest('published_at');

        // Filter by category
        if ($request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by tag
        if ($request->tag) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('name', $request->tag);
            });
        }

        // Search functionality
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $posts = $query->paginate(15);

        if ($request->expectsJson()) {
            return PostResource::collection($posts);
        }

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post
     */
    public function create()
    {
        Gate::authorize('create', Post::class);

        return view('posts.create');
    }

    /**
     * Store a newly created post
     */
    public function store(StorePostRequest $request)
    {
        Gate::authorize('create', Post::class);

        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'category_id' => $request->category_id,
            'user_id' => Auth::id(),
            'status' => $request->status ?? 'draft',
            'published_at' => $request->status === 'published' ? now() : $request->published_at,
            'featured_image' => $request->featured_image,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        // Attach tags
        if ($request->tags) {
            $this->contentService->attachTags($post, $request->tags);
        }

        if ($request->expectsJson()) {
            return new PostResource($post->load(['user', 'category', 'tags']));
        }

        return redirect()->route('posts.show', $post)
            ->with('success', 'Post created successfully!');
    }

    /**
     * Display the specified post
     */
    public function show(Post $post, Request $request)
    {
        // Check if user can view this post
        if ($post->status !== 'published' && (!Auth::check() || !Auth::user()->can('view', $post))) {
            abort(404);
        }

        $post->load(['user.profile', 'category', 'tags', 'comments.user.profile']);
        
        // Track view for analytics
        $this->contentService->trackPostView($post, $request);

        if ($request->expectsJson()) {
            return new PostResource($post);
        }

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post
     */
    public function edit(Post $post)
    {
        Gate::authorize('update', $post);

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified post
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        Gate::authorize('update', $post);

        $post->update($request->validated());

        // Update tags if provided
        if ($request->has('tags')) {
            $this->contentService->syncTags($post, $request->tags);
        }

        if ($request->expectsJson()) {
            return new PostResource($post->load(['user', 'category', 'tags']));
        }

        return redirect()->route('posts.show', $post)
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified post
     */
    public function destroy(Post $post, Request $request)
    {
        Gate::authorize('delete', $post);

        $post->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Post deleted successfully']);
        }

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully!');
    }

    /**
     * Restore a soft-deleted post
     */
    public function restore($id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        Gate::authorize('restore', $post);

        $post->restore();

        return redirect()->route('posts.show', $post)
            ->with('success', 'Post restored successfully!');
    }

    /**
     * Permanently delete a post
     */
    public function forceDestroy($id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        Gate::authorize('forceDelete', $post);

        $post->forceDelete();

        return redirect()->route('posts.index')
            ->with('success', 'Post permanently deleted!');
    }

    /**
     * Toggle post like/unlike
     */
    public function toggleLike(Post $post, Request $request)
    {
        $user = Auth::user();
        $isLiked = $user->likedPosts()->where('post_id', $post->id)->exists();

        if ($isLiked) {
            $user->likedPosts()->detach($post->id);
            $message = 'Post unliked';
        } else {
            $user->likedPosts()->attach($post->id);
            $message = 'Post liked';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'is_liked' => !$isLiked,
                'likes_count' => $post->likes()->count()
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Get post analytics
     */
    public function analytics(Post $post)
    {
        Gate::authorize('viewAnalytics', $post);

        $analytics = $this->contentService->getPostAnalytics($post);

        return response()->json($analytics);
    }

    /**
     * Duplicate a post
     */
    public function duplicate(Post $post)
    {
        Gate::authorize('create', Post::class);

        $newPost = $post->replicate();
        $newPost->title = $post->title . ' (Copy)';
        $newPost->status = 'draft';
        $newPost->published_at = null;
        $newPost->user_id = Auth::id();
        $newPost->save();

        // Copy tags
        $newPost->tags()->attach($post->tags->pluck('id'));

        return redirect()->route('posts.edit', $newPost)
            ->with('success', 'Post duplicated successfully!');
    }
}
