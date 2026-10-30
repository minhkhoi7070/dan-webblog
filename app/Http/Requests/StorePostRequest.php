<?php

namespace App\Http\Requests;

use App\Models\Post;
use App\Services\PostContentService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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

        // Sanitize and normalize slug if provided
        if ($this->has('slug')) {
            $rawSlug = $this->input('slug');
            if (is_string($rawSlug) && trim($rawSlug) !== '') {
                $this->merge(['slug' => Str::slug($rawSlug)]);
            } else {
                $this->merge(['slug' => null]);
            }
        }

        // Security rule: Never trust workflow fields from request input for authors
        if ($this->user() && ! $this->user()->isAdmin()) {
            $this->request->remove('status');
            $this->request->remove('publish_now');
            $this->request->remove('reviewed_by');
            $this->request->remove('reviewed_at');
            if ($this->input('action') === 'publish') {
                $this->merge(['action' => 'submit']);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $post = $this->route('post');
        $postId = $post instanceof Post ? $post->id : (is_numeric($post) ? (int) $post : null);

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::notIn(Post::RESERVED_SLUGS),
                Rule::unique('posts', 'slug')->ignore($postId),
            ],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required_without:body', 'nullable', 'string'],
            'body' => ['required_without:content', 'nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_thumbnail' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'action' => ['nullable', 'string', 'in:draft,submit,publish'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->hasAny(['body', 'content'])) {
                return;
            }

            $body = $this->input('body') ?? $this->input('content');
            if (is_string($body) && ! PostContentService::hasMeaningfulContent(PostContentService::cleanHtml($body))) {
                $validator->errors()->add('body', 'Nội dung bài viết không được để trống hoặc chỉ chứa định dạng rỗng.');
            }
        });
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
            'slug.regex' => 'Đường dẫn (slug) chỉ được chứa chữ cái thường, số và dấu gạch ngang (không bắt đầu, kết thúc hoặc có 2 dấu gạch ngang liên tiếp).',
            'slug.not_in' => 'Đường dẫn này được dành riêng cho chức năng hệ thống. Vui lòng chọn đường dẫn khác.',
            'slug.unique' => 'Đường dẫn (slug) này đã tồn tại trong hệ thống. Vui lòng chọn đường dẫn khác.',
            'slug.max' => 'Đường dẫn (slug) không được vượt quá 255 ký tự.',
            'category_id.required' => 'Vui lòng chọn chuyên mục bài viết.',
            'category_id.exists' => 'Chuyên mục được chọn không hợp lệ.',
            'content.required_without' => 'Vui lòng nhập nội dung bài viết.',
            'body.required_without' => 'Vui lòng nhập nội dung bài viết.',
            'tags.array' => 'Dữ liệu thẻ không hợp lệ.',
            'tags.max' => 'Bạn chỉ được gắn tối đa 10 thẻ cho mỗi bài viết.',
            'tags.*.integer' => 'Mã thẻ bài viết không hợp lệ.',
            'tags.*.exists' => 'Thẻ bài viết không tồn tại trong hệ thống.',
            'thumbnail.image' => 'File tải lên phải là hình ảnh hợp lệ.',
            'thumbnail.mimes' => 'Ảnh bìa phải có định dạng: JPG, JPEG, PNG hoặc WEBP.',
            'thumbnail.max' => 'Dung lượng ảnh bìa không được vượt quá 2MB.',
            'action.in' => 'Thao tác thực hiện không hợp lệ.',
        ];
    }
}
