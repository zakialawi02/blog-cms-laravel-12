<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index(): JsonResponse
    {
        $pages = Page::select('id', 'title', 'slug', 'content', 'description', 'isFullWidth', 'created_at', 'updated_at')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'List of pages',
            'data' => $pages,
        ]);
    }

    public function show(Page $page): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $page,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'unique:pages,slug'],
            'content' => ['nullable'],
            'description' => ['nullable', 'string'],
            'isFullWidth' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();
        $data['slug'] = Str::slug($data['slug'] ?? $data['title']);

        $page = Page::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Page created successfully',
            'data' => $page,
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, Page $page): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['nullable', 'unique:pages,slug,' . $page->id],
            'content' => ['nullable'],
            'description' => ['nullable', 'string'],
            'isFullWidth' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();
        if (isset($data['title']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $page->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Page updated successfully',
            'data' => $page,
        ]);
    }

    public function destroy(Page $page): JsonResponse
    {
        $page->delete();

        return response()->json([
            'success' => true,
            'message' => 'Page deleted successfully',
        ]);
    }
}
