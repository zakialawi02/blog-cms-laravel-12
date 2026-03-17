<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\TagResource;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Api\StoreTagRequest;
use App\Http\Requests\Api\UpdateTagRequest;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Tag::query();

            // 1. Search / Filtering
            if ($request->has('search')) {
                $search = $request->query('search');
                $query->where('tag_name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            }

            // 2. Sorting
            $random = filter_var($request->query('random', false), FILTER_VALIDATE_BOOLEAN);

            if ($random) {
                $query->inRandomOrder();
            } else {
                $sort = $request->query('sort', 'created_at'); // default sort column
                $direction = $request->query('direction', 'asc'); // default sort direction

                $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';
                $allowedSorts = ['id', 'tag_name', 'slug', 'created_at', 'updated_at'];

                if (in_array($sort, $allowedSorts)) {
                    $query->orderBy($sort, $direction);
                }
            }

            // 3. Pagination Limit
            $limit = $request->query('limit', 10);
            $limit = (is_numeric($limit) && $limit > 0) ? (int) $limit : 10;

            // Execute Paginated Query Appending Query Strings
            $tags = $query->paginate($limit)->appends($request->query());

            return TagResource::collection($tags)->additional([
                'success' => true,
                'message' => 'Tags retrieved successfully',
            ])->response()->setStatusCode(Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tags',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request): JsonResponse
    {
        try {
            $tag = Tag::create($request->validated());

            return (new TagResource($tag))->additional([
                'success' => true,
                'message' => 'Tag created successfully',
            ])->response()->setStatusCode(Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: Failed to create tag.',
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
            $tag = Tag::where('id', $id)->orWhere('slug', $id)->firstOrFail();

            return (new TagResource($tag))->additional([
                'success' => true,
                'message' => 'Tag retrieved successfully',
            ])->response()->setStatusCode(Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found',
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
    public function update(UpdateTagRequest $request, $id): JsonResponse
    {
        try {
            $tag = Tag::where('id', $id)->orWhere('slug', $id)->firstOrFail();
            $tag->update($request->validated());

            return (new TagResource($tag))->additional([
                'success' => true,
                'message' => 'Tag updated successfully',
            ])->response()->setStatusCode(Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: Failed to update tag.',
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
            $tag = Tag::where('id', $id)->orWhere('slug', $id)->firstOrFail();
            $tag->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tag deleted successfully'
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found',
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
