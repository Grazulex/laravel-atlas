<?php

namespace App\Http\Requests;

use App\Rules\UniquePostTitle;
use App\Rules\NoSpamContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $post = $this->route('post');
        return Auth::check() && Auth::user()->can('update', $post);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $post = $this->route('post');
        
        return [
            'title' => [
                'sometimes',
                'string',
                'max:255',
                'min:5',
                new UniquePostTitle(Auth::id(), $post?->id),
            ],
            'content' => [
                'sometimes',
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
                'sometimes',
                'exists:categories,id',
                Rule::exists('categories', 'id')->where('is_active', true),
            ],
            'tags' => [
                'sometimes',
                'array',
                'max:10',
            ],
            'tags.*' => [
                'string',
                'max:50',
                'alpha_dash',
            ],
            'status' => [
                'sometimes',
                Rule::in($this->getAllowedStatuses()),
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
     * Get allowed statuses based on user permissions
     */
    private function getAllowedStatuses(): array
    {
        $post = $this->route('post');
        $statuses = ['draft'];

        // Authors and admins can publish their posts
        if (Auth::user()->can('publish', $post)) {
            $statuses[] = 'published';
            $statuses[] = 'scheduled';
        }

        // Admins can archive posts
        if (Auth::user()->is_admin) {
            $statuses[] = 'archived';
        }

        return $statuses;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($validator) {
            $post = $this->route('post');
            
            // Prevent changing published posts to draft (unless admin)
            if ($this->status === 'draft' && $post->status === 'published' && !Auth::user()->is_admin) {
                $validator->errors()->add('status', 'Published posts cannot be changed back to draft.');
            }

            // Require excerpt for long published posts
            if ($this->status === 'published') {
                $content = $this->content ?? $post->content;
                if (!$this->excerpt && !$post->excerpt && strlen(strip_tags($content)) > 500) {
                    $validator->errors()->add('excerpt', 'An excerpt is required for published posts longer than 500 characters.');
                }
            }
        });
    }
}
