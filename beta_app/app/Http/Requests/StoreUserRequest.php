<?php

namespace App\Http\Requests;

use App\Rules\ValidUserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->can('create', \App\Models\User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                'regex:/^[a-zA-ZÀ-ÿ\s\'-]+$/u', // Allow international characters
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users'),
            ],
            'password' => [
                'required',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
                'confirmed',
            ],
            'role' => [
                'required',
                'string',
                new ValidUserRole(Auth::user()),
            ],
            'profile.bio' => [
                'nullable',
                'string',
                'max:500',
            ],
            'profile.avatar' => [
                'nullable',
                'url',
                'regex:/\.(jpg|jpeg|png|gif|webp)$/i',
            ],
            'profile.website' => [
                'nullable',
                'url',
                'active_url',
            ],
            'profile.social_links' => [
                'nullable',
                'array',
                'max:5',
            ],
            'profile.social_links.*.platform' => [
                'required_with:profile.social_links',
                'string',
                Rule::in(['twitter', 'linkedin', 'github', 'facebook', 'instagram']),
            ],
            'profile.social_links.*.url' => [
                'required_with:profile.social_links',
                'url',
                'active_url',
            ],
            'notification_preferences' => [
                'nullable',
                'array',
            ],
            'notification_preferences.email_notifications' => [
                'boolean',
            ],
            'notification_preferences.sms_notifications' => [
                'boolean',
            ],
            'notification_preferences.push_notifications' => [
                'boolean',
            ],
            'department' => [
                'nullable',
                'string',
                'max:100',
            ],
            'start_date' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'profile.bio' => 'profile bio',
            'profile.avatar' => 'profile avatar',
            'profile.website' => 'profile website',
            'profile.social_links.*.platform' => 'social media platform',
            'profile.social_links.*.url' => 'social media URL',
            'notification_preferences.email_notifications' => 'email notifications preference',
            'notification_preferences.sms_notifications' => 'SMS notifications preference',
            'notification_preferences.push_notifications' => 'push notifications preference',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Check if user can assign the requested role
            if ($this->role === 'admin' && !Auth::user()->is_admin) {
                $validator->errors()->add('role', 'You do not have permission to create admin users.');
            }

            // Validate social links format
            if ($this->has('profile.social_links')) {
                foreach ($this->input('profile.social_links', []) as $index => $link) {
                    if (isset($link['platform'], $link['url'])) {
                        $platform = $link['platform'];
                        $url = $link['url'];
                        
                        $expectedDomain = match ($platform) {
                            'twitter' => 'twitter.com',
                            'linkedin' => 'linkedin.com',
                            'github' => 'github.com',
                            'facebook' => 'facebook.com',
                            'instagram' => 'instagram.com',
                            default => null,
                        };

                        if ($expectedDomain && !str_contains($url, $expectedDomain)) {
                            $validator->errors()->add(
                                "profile.social_links.{$index}.url",
                                "The {$platform} URL must be from {$expectedDomain}."
                            );
                        }
                    }
                }
            }

            // Check department-role compatibility
            if ($this->role === 'admin' && $this->department) {
                $validator->errors()->add('department', 'Administrators should not be assigned to specific departments.');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.regex' => 'The name may only contain letters, spaces, apostrophes and hyphens.',
            'email.email' => 'Please provide a valid email address.',
            'password.uncompromised' => 'The password has appeared in a data breach and should not be used.',
            'profile.avatar.regex' => 'The avatar must be an image file (jpg, jpeg, png, gif, or webp).',
            'profile.social_links.*.platform.in' => 'The social platform must be one of: Twitter, LinkedIn, GitHub, Facebook, or Instagram.',
        ];
    }
}
