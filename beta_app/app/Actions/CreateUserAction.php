<?php

namespace App\Actions;

use App\Models\User;
use App\Models\Profile;
use App\Events\UserCreated;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class CreateUserAction
{
    /**
     * Create a new CreateUserAction instance.
     */
    public function __construct(
        private NotificationService $notificationService,
        private Log $logger
    ) {
        //
    }
    /**
     * Create a new user with profile
     *
     * @param array $userData
     * @param array $profileData
     * @return User
     * @throws InvalidArgumentException
     */
    public function __invoke(array $userData, array $profileData = []): User
    {
        $this->validateUserData($userData);

        return DB::transaction(function () use ($userData, $profileData) {
            // Create the user
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'is_admin' => $userData['is_admin'] ?? false,
                'bio' => $userData['bio'] ?? null,
                'avatar' => $userData['avatar'] ?? null,
                'website' => $userData['website'] ?? null,
                'location' => $userData['location'] ?? null,
            ]);

            // Create profile if profile data provided
            if (!empty($profileData)) {
                $user->profile()->create([
                    'bio' => $profileData['bio'] ?? null,
                    'avatar' => $profileData['avatar'] ?? null,
                    'website' => $profileData['website'] ?? null,
                    'location' => $profileData['location'] ?? null,
                    'phone' => $profileData['phone'] ?? null,
                    'birth_date' => $profileData['birth_date'] ?? null,
                    'social_media' => $profileData['social_media'] ?? [],
                    'preferences' => $profileData['preferences'] ?? [],
                ]);
            }

            $userWithProfile = $user->fresh(['profile']);

            // Dispatch event
            event(new UserCreated($userWithProfile, $userData['is_admin'] ?? false, $profileData));

            // Send notifications
            $this->notificationService->sendWelcomeNotification($userWithProfile);
            
            if ($userData['is_admin'] ?? false) {
                $this->notificationService->sendAdminNotification($userWithProfile);
            }

            return $userWithProfile;
        });
    }

    /**
     * Execute the action (alternative method name)
     */
    public function execute(array $userData, array $profileData = []): User
    {
        return $this($userData, $profileData);
    }

    /**
     * Validate user data
     *
     * @param array $userData
     * @throws InvalidArgumentException
     */
    private function validateUserData(array $userData): void
    {
        if (empty($userData['name'])) {
            throw new InvalidArgumentException('User name is required');
        }

        if (empty($userData['email'])) {
            throw new InvalidArgumentException('User email is required');
        }

        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }

        if (empty($userData['password']) || strlen($userData['password']) < 8) {
            throw new InvalidArgumentException('Password must be at least 8 characters');
        }

        // Check if email already exists
        if (User::where('email', $userData['email'])->exists()) {
            throw new InvalidArgumentException('Email already exists');
        }
    }

    /**
     * Create admin user
     */
    public function createAdmin(array $userData, array $profileData = []): User
    {
        $userData['is_admin'] = true;
        return $this($userData, $profileData);
    }
}
