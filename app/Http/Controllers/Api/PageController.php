<?php

namespace App\Http\Controllers\Api;

use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Http\Requests\Api\PageRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PageController extends Controller
{
    public function index(): JsonResponse
    {
        $pages = Page::all();

        return response()->json([
            'success' => true,
            'data' => PageResource::collection($pages),
        ]);
    }

    public function store(PageRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $page = Page::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Page created',
            'data' => new PageResource($page),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        try {
            $page = Page::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new PageResource($page),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        }
    }

    public function update(PageRequest $request, string $id): JsonResponse
    {
        try {
            $page = Page::findOrFail($id);
            $data = $request->validated();
            $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
            $page->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Page updated',
                'data' => new PageResource($page),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $page = Page::findOrFail($id);
            $page->delete();

            return response()->json([
                'success' => true,
                'message' => 'Page deleted',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        }
    }
}
