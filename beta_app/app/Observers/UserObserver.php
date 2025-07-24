<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Log user creation
        \Log::info("New user created: {$user->name} ({$user->email})");
        
        // Create default profile for new user
        if (!$user->profile) {
            $user->profile()->create([
                'bio' => 'Welcome to our platform!',
                'preferences' => ['notifications' => true, 'theme' => 'light'],
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Log important changes
        if ($user->wasChanged('email')) {
            \Log::info("User {$user->id} changed email to: {$user->email}");
        }
        
        if ($user->wasChanged('is_admin')) {
            $status = $user->is_admin ? 'granted' : 'revoked';
            \Log::warning("Admin privileges {$status} for user: {$user->name}");
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        \Log::info("User deleted: {$user->name} ({$user->email})");
        
        // Anonymize user's comments instead of deleting them
        $user->comments()->update([
            'author_name' => 'Anonymous User',
            'author_email' => null,
            'user_id' => null,
        ]);
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        \Log::info("User restored: {$user->name} ({$user->email})");
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        \Log::warning("User permanently deleted: {$user->name}");
    }
}
