<?php

namespace App\Http\Requests\Api;

use App\Models\Newsletter;
use Illuminate\Foundation\Http\FormRequest;

class NewsletterRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        $newsletter = Newsletter::find($this->route('newsletter'));

        return match (true) {
            $this->isMethod('POST') => $user->can('create', Newsletter::class),
            $this->isMethod('DELETE') => $newsletter ? $user->can('delete', $newsletter) : false,
            default => false,
        };
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255', 'unique:newsletters,email,' . $this->route('newsletter')],
        ];
    }
}
