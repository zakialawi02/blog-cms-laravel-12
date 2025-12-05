<?php

namespace App\Http\Requests\Api;

use App\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        $category = Category::find($this->route('category'));

        return match (true) {
            $this->isMethod('POST') => $user->can('create', Category::class),
            $this->isMethod('PUT'), $this->isMethod('PATCH') => $category ? $user->can('update', $category) : false,
            $this->isMethod('DELETE') => $category ? $user->can('delete', $category) : false,
            default => false,
        };
    }

    public function rules(): array
    {
        $categoryId = $this->route('category');

        return [
            'category' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ],
        ];
    }
}
