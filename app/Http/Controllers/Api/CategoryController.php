<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CategoryResource;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Api\StoreCategoryRequest;
use App\Http\Requests\Api\UpdateCategoryRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Category::query();

            // 1. Search / Filtering
            if ($request->has('search')) {
                $search = $request->query('search');
                $query->where('category', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            }

            // 2. Sorting
            $sort = $request->query('sort', 'created_at'); // default sort column
            $direction = $request->query('direction', 'asc'); // default sort direction

            $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';
            $allowedSorts = ['id', 'category', 'slug', 'created_at', 'updated_at'];

            if (in_array($sort, $allowedSorts)) {
                $query->orderBy($sort, $direction);
            }

            // 3. Pagination Limit
            $limit = $request->query('limit', 10);
            $limit = (is_numeric($limit) && $limit > 0) ? (int) $limit : 10;

            // Execute Paginated Query Appending Query Strings
            $categories = $query->paginate($limit)->appends($request->query());

            return CategoryResource::collection($categories)->additional([
                'success' => true,
                'message' => 'Categories retrieved successfully',
            ])->response()->setStatusCode(Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        try {
            $category = Category::create($request->validated());

            return (new CategoryResource($category))->additional([
                'success' => true,
                'message' => 'Category created successfully',
            ])->response()->setStatusCode(Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: Failed to create category.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            // Support both ID and Slug
            $category = Category::where('id', $id)->orWhere('slug', $id)->firstOrFail();

            return (new CategoryResource($category))->additional([
                'success' => true,
                'message' => 'Category retrieved successfully',
            ])->response()->setStatusCode(Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, $id): JsonResponse
    {
        try {
            $category = Category::where('id', $id)->orWhere('slug', $id)->firstOrFail();
            $category->update($request->validated());

            return (new CategoryResource($category))->additional([
                'success' => true,
                'message' => 'Category updated successfully',
            ])->response()->setStatusCode(Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: Failed to update category.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $category = Category::where('id', $id)->orWhere('slug', $id)->firstOrFail();
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
