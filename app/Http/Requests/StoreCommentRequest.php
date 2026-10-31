<?php

namespace App\Http\Requests;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $parentId = $this->input('parent_id');
        if (! is_scalar($parentId) || filter_var($parentId, FILTER_VALIDATE_INT) === false) {
            return;
        }

        $parent = Comment::find((int) $parentId);
        $postId = $this->route('post')?->id ?? $this->input('post_id');
        if (! $parent || (int) $parent->post_id !== (int) $postId) {
            return;
        }

        $visitedIds = [];
        while ($parent->parent_id !== null) {
            if (isset($visitedIds[$parent->id])) {
                return;
            }
            $visitedIds[$parent->id] = true;
            $ancestor = Comment::find($parent->parent_id);
            if (! $ancestor || $ancestor->post_id !== $parent->post_id) {
                return;
            }
            $parent = $ancestor;
        }

        $this->merge(['parent_id' => $parent->id]);
    }

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
            'client_request_id' => ['sometimes', 'required', 'uuid'],
        ];
    }

    /**
     * Configure the validator instance with custom rules for parent_id.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->hasAny(['parent_id', 'post_id'])) {
                return;
            }
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
