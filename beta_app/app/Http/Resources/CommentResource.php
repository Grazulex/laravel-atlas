<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Comment $this */
        return [
            'id' => $this->id,
            'content' => $this->content,
            'author_name' => $this->author_name,
            'author_email' => $this->when(
                auth()->user()?->is_admin,
                $this->author_email
            ),
            'status' => $this->status,
            'user' => UserResource::make($this->whenLoaded('user')),
            'post' => PostResource::make($this->whenLoaded('post')),
            'parent' => self::make($this->whenLoaded('parent')),
            'replies' => self::collection($this->whenLoaded('replies')),
            'replies_count' => $this->replies_count ?? $this->replies()->count(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
