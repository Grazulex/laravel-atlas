<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidUserRole implements ValidationRule
{
    protected User $currentUser;
    protected array $roleHierarchy;

    /**
     * Create a new rule instance.
     */
    public function __construct(User $currentUser)
    {
        $this->currentUser = $currentUser;
        $this->setupRoleHierarchy();
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('The :attribute must be a string.');
            return;
        }

        // Check if the role exists
        if (!$this->isValidRole($value)) {
            $fail('The selected :attribute is invalid.');
            return;
        }

        // Check if current user can assign this role
        if (!$this->canAssignRole($value)) {
            $fail('You do not have permission to assign the :attribute role.');
            return;
        }

        // Additional business logic checks
        if (!$this->passesBusinessRules($value)) {
            $fail('The :attribute assignment violates business rules.');
        }
    }

    /**
     * Setup role hierarchy and permissions
     */
    protected function setupRoleHierarchy(): void
    {
        $this->roleHierarchy = [
            'super_admin' => [
                'level' => 100,
                'can_assign' => ['admin', 'editor', 'author', 'contributor', 'subscriber'],
                'description' => 'Full system access',
            ],
            'admin' => [
                'level' => 80,
                'can_assign' => ['editor', 'author', 'contributor', 'subscriber'],
                'description' => 'Administrative access',
            ],
            'editor' => [
                'level' => 60,
                'can_assign' => ['author', 'contributor', 'subscriber'],
                'description' => 'Content management',
            ],
            'author' => [
                'level' => 40,
                'can_assign' => ['contributor', 'subscriber'],
                'description' => 'Content creation',
            ],
            'contributor' => [
                'level' => 20,
                'can_assign' => ['subscriber'],
                'description' => 'Limited content creation',
            ],
            'subscriber' => [
                'level' => 10,
                'can_assign' => [],
                'description' => 'View and comment only',
            ],
        ];
    }

    /**
     * Check if the role is valid
     */
    protected function isValidRole(string $role): bool
    {
        return array_key_exists($role, $this->roleHierarchy);
    }

    /**
     * Check if current user can assign the given role
     */
    protected function canAssignRole(string $role): bool
    {
        $currentUserRole = $this->currentUser->role;
        
        // Super admins can assign any role (including other super admins)
        if ($currentUserRole === 'super_admin') {
            return true;
        }

        // Check if role is in the current user's assignable roles list
        $assignableRoles = $this->roleHierarchy[$currentUserRole]['can_assign'] ?? [];
        
        return in_array($role, $assignableRoles);
    }

    /**
     * Apply business rules for role assignment
     */
    protected function passesBusinessRules(string $role): bool
    {
        // Business Rule 1: Limit number of admins
        if ($role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            $maxAdmins = config('app.max_admins', 10);
            
            if ($adminCount >= $maxAdmins) {
                return false;
            }
        }

        // Business Rule 2: Super admin limitation
        if ($role === 'super_admin') {
            $superAdminCount = User::where('role', 'super_admin')->count();
            $maxSuperAdmins = config('app.max_super_admins', 3);
            
            if ($superAdminCount >= $maxSuperAdmins) {
                return false;
            }
        }

        // Business Rule 3: Department-specific role restrictions
        $assigningToDepartment = request('department');
        if ($assigningToDepartment) {
            $restrictedRoles = $this->getDepartmentRestrictedRoles($assigningToDepartment);
            if (in_array($role, $restrictedRoles)) {
                return false;
            }
        }

        // Business Rule 4: Time-based restrictions (e.g., no new admins during certain periods)
        if ($this->isInRestrictedPeriod() && in_array($role, ['admin', 'super_admin'])) {
            return false;
        }

        // Business Rule 5: User-specific restrictions
        if (!$this->userMeetsRoleRequirements($role)) {
            return false;
        }

        return true;
    }

    /**
     * Get roles that are restricted for specific departments
     */
    protected function getDepartmentRestrictedRoles(string $department): array
    {
        $departmentRestrictions = [
            'finance' => ['contributor'], // Finance dept can't have contributors
            'hr' => [], // HR has no restrictions
            'marketing' => ['admin'], // Marketing can't have admins
            'development' => [], // Dev team has no restrictions
            'support' => ['super_admin'], // Support can't be super admin
        ];

        return $departmentRestrictions[$department] ?? [];
    }

    /**
     * Check if we're in a restricted period for role assignments
     */
    protected function isInRestrictedPeriod(): bool
    {
        // Example: No admin assignments during maintenance windows
        $maintenanceWindows = config('app.maintenance_windows', []);
        $currentTime = now();

        foreach ($maintenanceWindows as $window) {
            $start = \Carbon\Carbon::parse($window['start']);
            $end = \Carbon\Carbon::parse($window['end']);
            
            if ($currentTime->between($start, $end)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user meets requirements for the role
     */
    protected function userMeetsRoleRequirements(string $role): bool
    {
        $targetUser = $this->getTargetUser();
        
        if (!$targetUser) {
            return true; // New user, no restrictions
        }

        $requirements = [
            'admin' => [
                'min_account_age_days' => 30,
                'min_posts' => 10,
                'email_verified' => true,
            ],
            'super_admin' => [
                'min_account_age_days' => 90,
                'min_posts' => 50,
                'email_verified' => true,
                'phone_verified' => true,
            ],
            'editor' => [
                'min_account_age_days' => 14,
                'min_posts' => 5,
                'email_verified' => true,
            ],
        ];

        $roleRequirements = $requirements[$role] ?? [];

        foreach ($roleRequirements as $requirement => $value) {
            switch ($requirement) {
                case 'min_account_age_days':
                    if ($targetUser->created_at->diffInDays(now()) < $value) {
                        return false;
                    }
                    break;
                    
                case 'min_posts':
                    if ($targetUser->posts()->count() < $value) {
                        return false;
                    }
                    break;
                    
                case 'email_verified':
                    if ($value && !$targetUser->hasVerifiedEmail()) {
                        return false;
                    }
                    break;
                    
                case 'phone_verified':
                    if ($value && !($targetUser->phone_verified_at ?? false)) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }

    /**
     * Get the target user being assigned the role
     */
    protected function getTargetUser(): ?User
    {
        // Try to get user from route parameter or request
        $userId = request()->route('user')?->id ?? request('user_id');
        
        if ($userId) {
            return User::find($userId);
        }

        return null;
    }

    /**
     * Get role level for comparison
     */
    public function getRoleLevel(string $role): int
    {
        return $this->roleHierarchy[$role]['level'] ?? 0;
    }

    /**
     * Get role description
     */
    public function getRoleDescription(string $role): string
    {
        return $this->roleHierarchy[$role]['description'] ?? 'Unknown role';
    }

    /**
     * Get all available roles for current user
     */
    public function getAvailableRoles(): array
    {
        $currentUserRole = $this->currentUser->role;
        
        if ($currentUserRole === 'super_admin') {
            return array_keys($this->roleHierarchy);
        }

        return $this->roleHierarchy[$currentUserRole]['can_assign'] ?? [];
    }
}
