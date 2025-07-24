<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'avatar',
        'website',
        'location',
        'phone',
        'birth_date',
        'social_media',
        'preferences',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'social_media' => 'array',
        'preferences' => 'array',
    ];

    /**
     * A profile belongs to a user (One-to-One)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full name attribute
     */
    public function getFullNameAttribute(): string
    {
        return $this->user->name ?? 'Unknown User';
    }

    /**
     * Get the age from birth_date
     */
    public function getAgeAttribute(): ?int
    {
        return $this->birth_date?->age;
    }
}
