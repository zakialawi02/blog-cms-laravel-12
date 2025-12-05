<?php

namespace App\Http\Requests\Api;

use App\Models\RequestContributor;
use Illuminate\Foundation\Http\FormRequest;

class RequestContributorRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        $requestContributor = RequestContributor::find($this->route('request_contributor'));

        return match (true) {
            $this->isMethod('POST') => $user->can('create', RequestContributor::class),
            $this->isMethod('PUT'), $this->isMethod('PATCH') => $requestContributor ? $user->can('update', $requestContributor) : false,
            $this->isMethod('DELETE') => $requestContributor ? $user->can('delete', $requestContributor) : false,
            default => false,
        };
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'description' => ['required', 'string'],
            'is_approved' => ['sometimes', 'boolean'],
        ];
    }
}
