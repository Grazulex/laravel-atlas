<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        Log::info("Policy check: viewAny users for user {$user->id}");
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): Response
    {
        // Users can view their own profile
        if ($user->id === $model->id) {
            Log::info("Policy check: user {$user->id} viewing own profile");
            return Response::allow();
        }

        // Admins can view any profile
        if ($user->is_admin) {
            Log::info("Policy check: admin {$user->id} viewing user {$model->id}");
            return Response::allow();
        }

        Log::warning("Policy check: denied view access to user {$model->id}", [
            'requesting_user_id' => $user->id,
        ]);

        return Response::deny('You can only view your own profile.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        Log::info("Policy check: create user by user {$user->id}");
        return $user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): Response
    {
        // Users can update their own profile
        if ($user->id === $model->id) {
            Log::info("Policy check: user {$user->id} updating own profile");
            return Response::allow();
        }

        // Admins can update any profile
        if ($user->is_admin) {
            Log::info("Policy check: admin {$user->id} updating user {$model->id}");
            return Response::allow();
        }

        Log::warning("Policy check: denied update access to user {$model->id}", [
            'requesting_user_id' => $user->id,
        ]);

        return Response::deny('You can only update your own profile.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): Response
    {
        // Cannot delete own account
        if ($user->id === $model->id) {
            Log::warning("Policy check: user {$user->id} tried to delete own account");
            return Response::deny('You cannot delete your own account.');
        }

        // Only admins can delete users
        if ($user->is_admin && !$model->is_admin) {
            Log::info("Policy check: admin {$user->id} deleting user {$model->id}");
            return Response::allow();
        }

        Log::warning("Policy check: denied delete access to user {$model->id}", [
            'requesting_user_id' => $user->id,
            'is_admin' => $user->is_admin,
            'target_is_admin' => $model->is_admin,
        ]);

        return Response::deny('You cannot delete this user.');
    }

    /**
     * Determine whether the user can make another user admin.
     */
    public function makeAdmin(User $user, User $model): Response
    {
        // Only admins can promote users
        if (!$user->is_admin) {
            Log::warning("Policy check: non-admin {$user->id} tried to promote user {$model->id}");
            return Response::deny('Only administrators can promote users.');
        }

        // Cannot promote yourself (already admin)
        if ($user->id === $model->id) {
            Log::warning("Policy check: admin {$user->id} tried to promote themselves");
            return Response::deny('You are already an administrator.');
        }

        Log::info("Policy check: admin {$user->id} promoting user {$model->id}");
        return Response::allow();
    }
}
