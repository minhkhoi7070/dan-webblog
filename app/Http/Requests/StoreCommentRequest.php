<?php

namespace App\Http\Requests;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && ! $this->user()->is_locked;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'post_id' => ['sometimes', 'required', 'integer', 'exists:posts,id'],
            'body' => ['required', 'string', 'min:2', 'max:1000'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
        ];
    }

    /**
     * Configure the validator instance with custom rules for parent_id.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $parentId = $this->input('parent_id');
            if ($parentId) {
                /** @var Post|null $post */
                $post = $this->route('post');
                if (! $post && $this->input('post_id')) {
                    $post = Post::find($this->input('post_id'));
                }

                $parentComment = Comment::find($parentId);

                if (! $parentComment) {
                    $validator->errors()->add('parent_id', 'The parent comment does not exist.');
                } elseif ($post && $parentComment->post_id !== $post->id) {
                    $validator->errors()->add('parent_id', 'The parent comment does not belong to this post.');
                }
            }
        });
    }
}
