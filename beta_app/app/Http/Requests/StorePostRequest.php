<?php

namespace App\Http\Requests;

use App\Rules\UniquePostTitle;
use App\Rules\NoSpamContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->can('create', \App\Models\Post::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                'min:5',
                new UniquePostTitle(Auth::id()),
            ],
            'content' => [
                'required',
                'string',
                'min:100',
                new NoSpamContent(),
            ],
            'excerpt' => [
                'nullable',
                'string',
                'max:500',
            ],
            'category_id' => [
                'required',
                'exists:categories,id',
                Rule::exists('categories', 'id')->where('is_active', true),
            ],
            'tags' => [
                'nullable',
                'array',
                'max:10',
            ],
            'tags.*' => [
                'string',
                'max:50',
                'alpha_dash',
            ],
            'status' => [
                'required',
                Rule::in(['draft', 'published', 'scheduled']),
            ],
            'published_at' => [
                'nullable',
                'date',
                'after:now',
                'required_if:status,scheduled',
            ],
            'featured_image' => [
                'nullable',
                'url',
                'regex:/\.(jpg|jpeg|png|gif|webp)$/i',
            ],
            'meta_title' => [
                'nullable',
                'string',
                'max:60',
            ],
            'meta_description' => [
                'nullable',
                'string',
                'max:160',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'published_at' => 'publication date',
            'featured_image' => 'featured image URL',
            'meta_title' => 'SEO title',
            'meta_description' => 'SEO description',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'A post title is required.',
            'title.unique' => 'You already have a post with this title.',
            'content.min' => 'Your post content must be at least 100 characters long.',
            'category_id.exists' => 'The selected category must be active.',
            'tags.max' => 'You cannot add more than 10 tags.',
            'status.in' => 'Post status must be draft, published, or scheduled.',
            'published_at.after' => 'Publication date must be in the future.',
            'featured_image.regex' => 'Featured image must be a valid image URL (jpg, jpeg, png, gif, webp).',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Custom validation logic
            if ($this->status === 'published' && !$this->excerpt && strlen(strip_tags($this->content)) > 500) {
                $validator->errors()->add('excerpt', 'An excerpt is required for published posts longer than 500 characters.');
            }

            // Check user's post limit
            $userPostsCount = Auth::user()->posts()->count();
            $maxPosts = Auth::user()->is_admin ? 1000 : 50;
            
            if ($userPostsCount >= $maxPosts) {
                $validator->errors()->add('user', "You have reached the maximum number of posts ({$maxPosts}).");
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => Auth::id(),
        ]);

        // Auto-generate excerpt if not provided
        if (!$this->excerpt && $this->content) {
            $this->merge([
                'excerpt' => $this->generateExcerpt($this->content),
            ]);
        }
    }

    /**
     * Generate excerpt from content
     */
    private function generateExcerpt(string $content): string
    {
        $excerpt = strip_tags($content);
        $excerpt = preg_replace('/\s+/', ' ', $excerpt);
        
        if (strlen($excerpt) <= 200) {
            return trim($excerpt);
        }

        return trim(substr($excerpt, 0, 197)) . '...';
    }
}
