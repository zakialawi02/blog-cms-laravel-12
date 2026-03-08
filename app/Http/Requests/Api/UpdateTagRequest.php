<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UpdateTagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {

        $this->merge([
            'tag_name' => ucwords($this->tag_name),
            'slug' => (empty($this->slug)) ? Str::slug($this->tag_name) : Str::slug($this->slug)
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Mendapatkan ID atau parameter tag dari URL, jika ada (route model binding mungkin berupa instance)
        $tagId = $this->route('tag') ? ($this->route('tag')->id ?? $this->route('tag')) : null;

        return [
            'tag_name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('tags', 'slug')->ignore($tagId),
            ],
        ];
    }

    /**
     * Handle a failed validation attempt for API requests.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'success' => false,
            'message' => 'Validation error.',
            'errors' => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);

        throw new ValidationException($validator, $response);
    }
}
