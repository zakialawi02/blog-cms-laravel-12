<?php

namespace App\Http\Requests\Api;

use App\Models\Page;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        $page = Page::find($this->route('page'));

        return match (true) {
            $this->isMethod('POST') => $user->can('create', Page::class),
            $this->isMethod('PUT'), $this->isMethod('PATCH') => $page ? $user->can('update', $page) : false,
            $this->isMethod('DELETE') => $page ? $user->can('delete', $page) : false,
            default => false,
        };
    }

    public function rules(): array
    {
        $pageId = $this->route('page');

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('pages', 'slug')->ignore($pageId),
            ],
            'isFullWidth' => ['sometimes', 'boolean'],
        ];
    }
}
