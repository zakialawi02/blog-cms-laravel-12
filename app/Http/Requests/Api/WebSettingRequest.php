<?php

namespace App\Http\Requests\Api;

use App\Models\WebSetting;
use Illuminate\Foundation\Http\FormRequest;

class WebSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        $setting = WebSetting::find($this->route('setting'));

        return match (true) {
            $this->isMethod('POST') => $user->can('create', WebSetting::class),
            $this->isMethod('PUT'), $this->isMethod('PATCH') => $setting ? $user->can('update', $setting) : false,
            $this->isMethod('DELETE') => $setting ? $user->can('delete', $setting) : false,
            default => false,
        };
    }

    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'max:255'],
            'value' => ['required'],
            'type' => ['required', 'string', 'max:50'],
        ];
    }
}
