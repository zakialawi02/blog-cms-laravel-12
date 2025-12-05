<?php

namespace App\Http\Requests\Api;

use App\Models\Menu;
use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        $menu = Menu::find($this->route('menu'));

        return match (true) {
            $this->isMethod('POST') => $user->can('create', Menu::class),
            $this->isMethod('PUT'), $this->isMethod('PATCH') => $menu ? $user->can('update', $menu) : false,
            $this->isMethod('DELETE') => $menu ? $user->can('delete', $menu) : false,
            default => false,
        };
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
        ];
    }
}
