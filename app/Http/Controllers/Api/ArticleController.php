<?php

namespace App\Http\Controllers\Api;

use App\Models\Tag;
use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\Api\ArticleRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ArticleController extends Controller
{
    public function index(): JsonResponse
    {
        $articles = Article::with(['category', 'user', 'tags'])
            ->withCount('comments', 'articleViews')
            ->latest('published_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ArticleResource::collection($articles),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        try {
            $article = Article::with(['category', 'user', 'tags', 'comments.replies', 'comments.user', 'articleViews'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new ArticleResource($article),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
            ], 404);
        }
    }

    public function store(ArticleRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $data['user_id'] ?? $request->user()?->id;
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        $article = Article::create($data);
        $this->syncTags($article, $data['tags'] ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Article created',
            'data' => new ArticleResource($article->load(['category', 'user', 'tags'])),
        ], 201);
    }

    public function update(ArticleRequest $request, string $id): JsonResponse
    {
        try {
            $article = Article::findOrFail($id);
            $data = $request->validated();
            $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

            $article->update($data);
            $this->syncTags($article, $data['tags'] ?? []);

            return response()->json([
                'success' => true,
                'message' => 'Article updated',
                'data' => new ArticleResource($article->load(['category', 'user', 'tags'])),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
            ], 404);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $article = Article::findOrFail($id);
            $article->delete();

            return response()->json([
                'success' => true,
                'message' => 'Article deleted',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
            ], 404);
        }
    }

    protected function syncTags(Article $article, array $tags): void
    {
        if (empty($tags)) {
            return;
        }

        $tagIds = collect($tags)->map(fn(string $tag) => Tag::firstOrCreate(
            ['slug' => Str::slug($tag)],
            ['tag_name' => ucwords($tag)]
        )->id);

        $article->tags()->sync($tagIds);
    }
}
