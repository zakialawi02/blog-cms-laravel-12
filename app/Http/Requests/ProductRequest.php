<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @method bool hasFile(string $key)
 * @method \Illuminate\Http\UploadedFile|null file(string $key = null)
 */

class ProductRequest extends FormRequest
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
            'product_name' => ucwords($this->product_name),
            'slug' => (empty($this->slug)) ? Str::slug($this->product_name) : Str::slug($this->slug),
            'currency' => $this->currency ?? 'USD',
            'is_published' => $this->has('is_published') ? true : false,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'product_name' => 'required|string|min:3|max:255',
            'slug' => 'required|string|unique:products,slug,' . $product?->id,
            'description' => 'required|string|min:10',
            'currency' => 'required|string|in:USD,EUR,IDR,GBP,JPY',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'is_published' => 'boolean',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'product_name' => 'product name',
            'discount_price' => 'discount price',
            'is_published' => 'publication status',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'discount_price.lt' => 'The discount price must be less than the regular price.',
            'thumbnail.image' => 'The thumbnail must be an image file.',
            'thumbnail.mimes' => 'The thumbnail must be a file of type: jpg, jpeg, png, gif.',
            'thumbnail.max' => 'The thumbnail must not be larger than 2MB.',
        ];
    }
}
