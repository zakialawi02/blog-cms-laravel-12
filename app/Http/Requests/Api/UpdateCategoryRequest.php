<?php

namespace App\Http\Requests\Api;

use App\Models\Category;
use Illuminate\Http\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryParam = $this->route('category'); // get parameter from URL

        // Find the category to get its ID, as the param could be an ID or Slug
        $category = Category::where('id', $categoryParam)->orWhere('slug', $categoryParam)->firstOrFail();
        $id = $category->id;

        return [
            'category' => 'required|min:3|unique:categories,category,' . $id,
            'slug' => 'nullable|unique:categories,slug,' . $id,
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
