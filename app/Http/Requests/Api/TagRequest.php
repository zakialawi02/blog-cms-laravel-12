<?php

namespace App\Http\Requests\Api;

use App\Models\Tag;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        $tag = Tag::find($this->route('tag'));

        return match (true) {
            $this->isMethod('POST') => $user->can('create', Tag::class),
            $this->isMethod('PUT'), $this->isMethod('PATCH') => $tag ? $user->can('update', $tag) : false,
            $this->isMethod('DELETE') => $tag ? $user->can('delete', $tag) : false,
            default => false,
        };
    }

    public function rules(): array
    {
        $tagId = $this->route('tag');

        return [
            'tag_name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tags', 'slug')->ignore($tagId),
            ],
        ];
    }
}
