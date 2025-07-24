<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Category $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'parent' => self::make($this->whenLoaded('parent')),
            'children' => self::collection($this->whenLoaded('children')),
            'posts_count' => $this->posts_count ?? $this->posts()->count(),
            'meta' => $this->when($this->meta_title || $this->meta_description, [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
            ]),
            'links' => [
                'self' => route('categories.show', $this->id),
                'posts' => route('categories.posts', $this->id),
            ],
        ];
    }
}
