<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class MenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'link' => ['required', 'string'],
            'parent' => ['nullable', 'exists:menu_items,id'],
            'sort' => ['nullable', 'integer'],
            'class' => ['nullable', 'string', 'max:255'],
            'menu' => ['required', 'exists:menus,id'],
            'depth' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
