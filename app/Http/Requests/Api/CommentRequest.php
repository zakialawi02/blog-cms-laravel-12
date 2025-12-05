<?php

namespace App\Http\Requests\Api;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        $comment = Comment::find($this->route('comment'));

        return match (true) {
            $this->isMethod('POST') => $user->can('create', Comment::class),
            $this->isMethod('PUT'), $this->isMethod('PATCH') => $comment ? $user->can('update', $comment) : false,
            $this->isMethod('DELETE') => $comment ? $user->can('delete', $comment) : false,
            default => false,
        };
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
