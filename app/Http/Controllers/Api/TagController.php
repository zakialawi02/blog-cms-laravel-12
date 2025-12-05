<?php

namespace App\Http\Controllers\Api;

use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Http\Requests\Api\TagRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TagController extends Controller
{
    public function index(): JsonResponse
    {
        $tags = Tag::withCount('articles')->get();

        return response()->json([
            'success' => true,
            'data' => TagResource::collection($tags),
        ]);
    }

    public function store(TagRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['tag_name']);
        $tag = Tag::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Tag created',
            'data' => new TagResource($tag),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        try {
            $tag = Tag::withCount('articles')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new TagResource($tag),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found',
            ], 404);
        }
    }

    public function update(TagRequest $request, string $id): JsonResponse
    {
        try {
            $tag = Tag::findOrFail($id);
            $data = $request->validated();
            $data['slug'] = $data['slug'] ?? Str::slug($data['tag_name']);
            $tag->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Tag updated',
                'data' => new TagResource($tag),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found',
            ], 404);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $tag = Tag::findOrFail($id);
            $tag->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tag deleted',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found',
            ], 404);
        }
    }
}
