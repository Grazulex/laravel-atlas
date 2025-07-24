<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Services\ContentService;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Post $this */
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => app(ContentService::class)->generateSlug($this->title),
            'content' => $this->when(
                $this->shouldShowContent($request),
                $this->content
            ),
            'excerpt' => $this->excerpt,
            'status' => $this->status,
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            'featured_image' => $this->featured_image,
            'meta' => $this->when($this->shouldShowMeta($request), [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
            ]),
            'stats' => [
                'comments_count' => $this->comments_count ?? $this->comments()->count(),
                'tags_count' => $this->tags_count ?? $this->tags()->count(),
                'reading_time' => $this->calculateReadingTime(),
            ],
            'author' => UserResource::make($this->whenLoaded('user')),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'timestamps' => [
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            ],
            'permissions' => $this->when(Auth::check(), [
                'can_update' => Auth::user()->can('update', $this->resource),
                'can_delete' => Auth::user()->can('delete', $this->resource),
                'can_publish' => Auth::user()->can('publish', $this->resource),
            ]),
            'links' => [
                'self' => route('posts.show', $this->id),
                'author' => route('users.show', $this->user_id),
                'comments' => route('posts.comments.index', $this->id),
            ],
        ];
    }

    /**
     * Determine if full content should be shown
     */
    private function shouldShowContent(Request $request): bool
    {
        // Show content for single post view or if user can edit
        return $request->route()?->getName() === 'posts.show' || 
               (Auth::check() && Auth::user()->can('update', $this->resource));
    }

    /**
     * Determine if meta information should be shown
     */
    private function shouldShowMeta(Request $request): bool
    {
        return Auth::check() && (
            Auth::user()->is_admin || 
            Auth::user()->id === $this->user_id
        );
    }

    /**
     * Calculate estimated reading time
     */
    private function calculateReadingTime(): int
    {
        $wordsPerMinute = 200;
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / $wordsPerMinute));
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'generated_at' => now()->toISOString(),
                'user_authenticated' => Auth::check(),
            ],
        ];
    }
}
