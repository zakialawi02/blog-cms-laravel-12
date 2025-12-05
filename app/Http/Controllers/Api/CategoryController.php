<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\Api\CategoryRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::withCount('articles')->get();

        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['category']);

        $category = Category::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Category created',
            'data' => new CategoryResource($category),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        try {
            $category = Category::withCount('articles')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new CategoryResource($category),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }
    }

    public function update(CategoryRequest $request, string $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
            $data = $request->validated();
            $data['slug'] = $data['slug'] ?? Str::slug($data['category']);
            $category->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Category updated',
                'data' => new CategoryResource($category),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }
    }
}
