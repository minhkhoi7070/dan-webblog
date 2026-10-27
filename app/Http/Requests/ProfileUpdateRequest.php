<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'avatar' => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,png,webp',
                'max:2048',
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'avatar.image' => 'Ảnh đại diện phải là một tệp hình ảnh hợp lệ.',
            'avatar.mimes' => 'Ảnh đại diện chỉ chấp nhận định dạng JPEG, PNG hoặc WebP.',
            'avatar.max' => 'Dung lượng ảnh đại diện không được vượt quá 2MB.',
        ];
    }
}
