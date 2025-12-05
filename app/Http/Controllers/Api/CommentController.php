<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Requests\Api\CommentRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommentController extends Controller
{
    public function index(): JsonResponse
    {
        $comments = Comment::with(['user', 'replies'])->latest()->get();

        return response()->json([
            'success' => true,
            'data' => CommentResource::collection($comments),
        ]);
    }

    public function store(CommentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $data['user_id'] ?? $request->user()?->id;

        $comment = Comment::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Comment created',
            'data' => new CommentResource($comment->load(['user', 'replies'])),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        try {
            $comment = Comment::with(['user', 'replies'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new CommentResource($comment),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found',
            ], 404);
        }
    }

    public function update(CommentRequest $request, string $id): JsonResponse
    {
        try {
            $comment = Comment::findOrFail($id);
            $comment->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Comment updated',
                'data' => new CommentResource($comment->load(['user', 'replies'])),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found',
            ], 404);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $comment = Comment::findOrFail($id);
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found',
            ], 404);
        }
    }
}
