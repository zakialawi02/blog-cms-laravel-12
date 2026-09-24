<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'slug' => empty($this->slug) ? Str::slug((string) $this->title) : Str::slug((string) $this->slug),
        ]);
    }

    public function rules(): array
    {
        return [
            'title'         => 'required|string|min:5|max:255',
            'slug'          => 'required|string|unique:articles,slug',
            'content'       => 'required|string|min:10',
            'excerpt'       => 'nullable|string|max:200',
            'category_id'   => 'nullable|exists:categories,id',
            'tags'          => 'nullable',
            'cover'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'meta_title'    => 'nullable|string|min:10|max:80',
            'meta_desc'     => 'nullable|string|min:10|max:180',
            'meta_keywords' => 'nullable|string|max:255',
            'is_featured'   => 'nullable|boolean',
            'type'          => 'nullable|in:post,page',
            'action'        => 'nullable|in:publish,draft',
            'published_at'  => 'nullable|date',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'success' => false,
            'message' => 'Validation error.',
            'errors'  => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);

        throw new ValidationException($validator, $response);
    }
}
