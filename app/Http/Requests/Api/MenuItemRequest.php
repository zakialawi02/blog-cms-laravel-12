<?php

namespace App\Http\Requests\Api;

use App\Models\MenuItem;
use Illuminate\Foundation\Http\FormRequest;

class MenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        $menuItem = MenuItem::find($this->route('menu_item'));

        return match (true) {
            $this->isMethod('POST') => $user->can('create', MenuItem::class),
            $this->isMethod('PUT'), $this->isMethod('PATCH') => $menuItem ? $user->can('update', $menuItem) : false,
            $this->isMethod('DELETE') => $menuItem ? $user->can('delete', $menuItem) : false,
            default => false,
        };
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
