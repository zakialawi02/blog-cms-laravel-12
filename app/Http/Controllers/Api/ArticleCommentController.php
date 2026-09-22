<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Resources\CommentResource;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticleCommentController extends Controller
{
    /**
     * Display a listing of comments for a specific article.
     */
    public function index(Request $request, $slug): JsonResponse
    {
        try {
            $article = Article::where('slug', $slug)->firstOrFail();

            $limit = \App\Support\QueryParams::perPage($request, 10, 50);
            $sort = \App\Support\QueryParams::sort($request, ['created_at', 'updated_at'], 'created_at');
            $order = \App\Support\QueryParams::direction($request, 'desc', 'order');

            // Fetch only top-level comments (parent_id is null)
            // Nested replies will be loaded recursively via the relationship in CommentResource
            $comments = $article->comments()
                ->whereNull('parent_id')
                ->where('is_approved', true) // Only show approved comments
                ->with(['user', 'replies.user']) // Eager load user and first level of replies
                ->orderBy($sort, $order)
                ->paginate($limit);

            $resource = CommentResource::collection($comments);

            return response()->json([
                'success' => true,
                'message' => 'Comments retrieved successfully',
                'data' => $resource->items(),
                'meta' => [
                    'current_page' => $comments->currentPage(),
                    'last_page' => $comments->lastPage(),
                    'per_page' => $comments->perPage(),
                    'total' => $comments->total(),
                ],
                'links' => [
                    'first' => $comments->url(1),
                    'last' => $comments->url($comments->lastPage()),
                    'prev' => $comments->previousPageUrl(),
                    'next' => $comments->nextPageUrl(),
                ]
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching comments',
                'error' => \App\Support\ErrorReporter::refString($th, 'ArticleCommentController::index')
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created comment in storage.
     */
    public function store(CommentStoreRequest $request, $slug): JsonResponse
    {
        try {
            $article = Article::where('slug', $slug)->firstOrFail();

            $validated = $request->validated();

            // Pertahanan ganda: pastikan komentar induk memang milik artikel ini.
            $parentId = $validated['parent_id'] ?? null;

            if ($parentId !== null && ! Comment::whereKey($parentId)->where('article_id', $article->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Komentar induk tidak valid untuk artikel ini.',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $comment = $article->comments()->create([
                'user_id' => $request->user()->id,
                'content' => $validated['content'],
                'parent_id' => $validated['parent_id'] ?? null,
                'is_approved' => true, // Auto-approve for now, or set based on settings
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comment posted successfully',
                'data' => new CommentResource($comment->load('user'))
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while posting comment',
                'error' => \App\Support\ErrorReporter::refString($th, 'ArticleCommentController::store')
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
