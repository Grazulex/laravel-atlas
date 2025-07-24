<?php

namespace App\Http\Controllers;

use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TagController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of tags
     */
    public function index(Request $request)
    {
        $query = Tag::withCount(['posts' => fn($q) => $q->published()])
            ->when($request->search, fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));

        $tags = $query->orderByDesc('posts_count')->paginate(20);

        if ($request->expectsJson()) {
            return TagResource::collection($tags);
        }

        return view('tags.index', compact('tags'));
    }

    /**
     * Store a newly created tag
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Tag::class);

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tags|alpha_dash',
            'description' => 'nullable|string|max:255',
        ]);

        $tag = Tag::create($validated);

        if ($request->expectsJson()) {
            return new TagResource($tag);
        }

        return redirect()->route('tags.index')
            ->with('success', 'Tag created successfully!');
    }

    /**
     * Display the specified tag
     */
    public function show(Tag $tag, Request $request)
    {
        $tag->loadCount(['posts' => fn($q) => $q->published()]);

        if ($request->expectsJson()) {
            return new TagResource($tag);
        }

        $posts = $tag->posts()->published()->with(['user', 'category'])->paginate(10);

        return view('tags.show', compact('tag', 'posts'));
    }

    /**
     * Update the specified tag
     */
    public function update(Request $request, Tag $tag)
    {
        Gate::authorize('update', $tag);

        $validated = $request->validate([
            'name' => 'required|string|max:50|alpha_dash|unique:tags,name,' . $tag->id,
            'description' => 'nullable|string|max:255',
        ]);

        $tag->update($validated);

        if ($request->expectsJson()) {
            return new TagResource($tag);
        }

        return redirect()->route('tags.show', $tag)
            ->with('success', 'Tag updated successfully!');
    }

    /**
     * Remove the specified tag
     */
    public function destroy(Tag $tag, Request $request)
    {
        Gate::authorize('delete', $tag);

        $tag->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Tag deleted successfully']);
        }

        return redirect()->route('tags.index')
            ->with('success', 'Tag deleted successfully!');
    }

    /**
     * Search tags (for autocomplete)
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $tags = Tag::where('name', 'like', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($tags);
    }
}
