<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'article_id' => ['required', 'exists:articles,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'content' => ['required', 'string'],
            'is_approved' => ['sometimes', 'boolean'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ];
    }
}
