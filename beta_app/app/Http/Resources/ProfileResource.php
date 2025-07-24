<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Profile $this */
        return [
            'bio' => $this->bio,
            'avatar' => $this->avatar,
            'website' => $this->website,
            'location' => $this->location,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'social_media' => $this->social_media,
            'preferences' => $this->preferences,
        ];
    }
}
