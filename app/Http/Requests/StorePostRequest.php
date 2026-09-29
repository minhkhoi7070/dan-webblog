<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null
            && in_array($this->user()->role, ['author', 'admin'], true)
            && ! $this->user()->is_locked;
    }

    /**
     * Prepare the data for validation.
     * Normalize content/body and strip unauthorized workflow tampering fields.
     */
    protected function prepareForValidation(): void
    {
        // Support both 'content' and 'body' aliases seamlessly
        if ($this->has('content') && ! $this->has('body')) {
            $this->merge(['body' => $this->input('content')]);
        } elseif ($this->has('body') && ! $this->has('content')) {
            $this->merge(['content' => $this->input('body')]);
        }

        // Security rule: Never trust workflow fields from request input for authors
        if ($this->user() && ! $this->user()->isAdmin()) {
            $this->request->remove('status');
            $this->request->remove('publish_now');
            $this->request->remove('reviewed_by');
            $this->request->remove('reviewed_at');
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required_without:body', 'nullable', 'string'],
            'body' => ['required_without:content', 'nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ];
    }
}
