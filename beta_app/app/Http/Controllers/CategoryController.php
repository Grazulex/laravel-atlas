<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        $query = Category::withCount(['posts' => fn($q) => $q->published()])
            ->when($request->search, fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));

        $categories = $query->paginate(15);

        if ($request->expectsJson()) {
            return CategoryResource::collection($categories);
        }

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        Gate::authorize('create', Category::class);

        return view('categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Category::class);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category = Category::create($validated);

        if ($request->expectsJson()) {
            return new CategoryResource($category);
        }

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Display the specified category
     */
    public function show(Category $category, Request $request)
    {
        $category->loadCount(['posts' => fn($q) => $q->published()]);
        
        if ($request->expectsJson()) {
            return new CategoryResource($category);
        }

        $posts = $category->posts()->published()->with(['user', 'tags'])->paginate(10);

        return view('categories.show', compact('category', 'posts'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category)
    {
        Gate::authorize('update', $category);

        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        Gate::authorize('update', $category);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        if ($category->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        if ($request->expectsJson()) {
            return new CategoryResource($category);
        }

        return redirect()->route('categories.show', $category)
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category, Request $request)
    {
        Gate::authorize('delete', $category);

        // Check if category has posts
        if ($category->posts()->exists()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Cannot delete category with existing posts'], 422);
            }
            return back()->with('error', 'Cannot delete category with existing posts!');
        }

        $category->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Category deleted successfully']);
        }

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}
