<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\User $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->when(
                $this->shouldShowEmail($request),
                $this->email
            ),
            'bio' => $this->bio,
            'avatar' => $this->avatar,
            'website' => $this->website,
            'location' => $this->location,
            'is_admin' => $this->when(
                Auth::check() && Auth::user()->is_admin,
                $this->is_admin
            ),
            'profile' => ProfileResource::make($this->whenLoaded('profile')),
            'stats' => [
                'posts_count' => $this->posts_count ?? $this->posts()->count(),
                'comments_count' => $this->comments_count ?? $this->comments()->count(),
                'member_since' => $this->created_at->diffForHumans(),
                'last_activity' => $this->when(
                    isset($this->last_activity),
                    $this->last_activity?->diffForHumans()
                ),
            ],
            'social' => $this->when($this->profile?->social_media, [
                'social_media' => $this->profile?->social_media,
            ]),
            'timestamps' => $this->when($this->shouldShowTimestamps($request), [
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
                'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            ]),
            'permissions' => $this->when(Auth::check(), [
                'can_view_profile' => Auth::user()->can('view', $this->resource),
                'can_edit_profile' => Auth::user()->can('update', $this->resource),
                'can_delete' => Auth::user()->can('delete', $this->resource),
            ]),
            'links' => [
                'self' => route('users.show', $this->id),
                'posts' => route('users.posts', $this->id),
                'comments' => route('users.comments', $this->id),
            ],
        ];
    }

    /**
     * Determine if email should be shown
     */
    private function shouldShowEmail(Request $request): bool
    {
        return Auth::check() && (
            Auth::id() === $this->id || 
            Auth::user()->is_admin
        );
    }

    /**
     * Determine if timestamps should be shown
     */
    private function shouldShowTimestamps(Request $request): bool
    {
        return Auth::check() && (
            Auth::id() === $this->id || 
            Auth::user()->is_admin
        );
    }
}
