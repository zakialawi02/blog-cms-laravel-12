<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuItemSyncRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Adjust according to your roles/permissions
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array'],
            'items.*.id' => ['nullable', 'integer', 'exists:menu_items,id'],
            'items.*.label' => ['required', 'string', 'max:255'],
            'items.*.link' => ['nullable', 'string', 'max:255'],
            'items.*.parent' => ['nullable', 'integer'],
            'items.*.sort' => ['nullable', 'integer'],
            'items.*.class' => ['nullable', 'string', 'max:255'],
            'items.*.depth' => ['nullable', 'integer'],
            // Optional: children rule to allow syncing full nested JSON tree structure
            'items.*.children' => ['nullable', 'array'],
        ];
    }
}
