<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Tag $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'color' => $this->color,
            'is_featured' => $this->is_featured,
            'posts_count' => $this->posts_count ?? $this->posts()->count(),
            'links' => [
                'self' => route('tags.show', $this->id),
                'posts' => route('tags.posts', $this->id),
            ],
        ];
    }
}
