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
            'remove_thumbnail' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'action' => ['nullable', 'string', 'in:draft,submit'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tiêu đề bài viết.',
            'category_id.required' => 'Vui lòng chọn chuyên mục bài viết.',
            'category_id.exists' => 'Chuyên mục được chọn không hợp lệ.',
            'content.required_without' => 'Vui lòng nhập nội dung bài viết.',
            'body.required_without' => 'Vui lòng nhập nội dung bài viết.',
            'tags.array' => 'Dữ liệu thẻ không hợp lệ.',
            'tags.max' => 'Bạn chỉ được gắn tối đa 10 thẻ cho mỗi bài viết.',
            'tags.*.integer' => 'Mã thẻ bài viết không hợp lệ.',
            'tags.*.exists' => 'Thẻ bài viết không tồn tại trong hệ thống.',
            'thumbnail.image' => 'File tải lên phải là hình ảnh hợp lệ.',
            'thumbnail.max' => 'Dung lượng ảnh bìa không được vượt quá 2MB.',
            'action.in' => 'Thao tác thực hiện không hợp lệ.',
        ];
    }
}
