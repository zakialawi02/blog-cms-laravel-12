<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $articles = Article::with(['category:id,slug,name', 'tags:id,slug,tag_name', 'user:id,username,name'])
            ->when($request->query('status'), fn($q, $status) => $q->where('status', $status))
            ->when($request->query('category'), fn($q, $slug) => $q->withCategorySlug($slug))
            ->when($request->query('tag'), fn($q, $slug) => $q->withTagSlug($slug))
            ->when($request->query('search'), fn($q, $keyword) => $q->search($keyword))
            ->orderByDesc('published_at')
            ->paginate($request->integer('per_page', 10));

        return response()->json([
            'success' => true,
            'message' => 'List of articles',
            'data' => $articles,
        ]);
    }

    public function show(Article $article): JsonResponse
    {
        $article->load(['category:id,slug,name', 'tags:id,slug,tag_name', 'user:id,username,name']);

        return response()->json([
            'success' => true,
            'data' => $article,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'min:5'],
            'slug' => ['nullable', 'unique:articles,slug'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['nullable', 'in:draft,published,pending'],
            'published_at' => ['nullable', 'date'],
            'content' => ['nullable'],
            'excerpt' => ['nullable', 'max:200'],
            'cover' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'min:10', 'max:80'],
            'meta_desc' => ['nullable', 'min:10', 'max:180'],
            'meta_keywords' => ['nullable', 'max:255'],
            'tags' => ['array'],
            'tags.*' => ['string'],
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
        $data['user_id'] = $request->user()->id;
        $data['status'] = $data['status'] ?? 'draft';

        $article = Article::create(Arr::except($data, ['tags']));

        if (! empty($data['tags'])) {
            $tagIds = collect($data['tags'])->map(function (string $value) {
                return Tag::firstOrCreate([
                    'slug' => Str::slug($value),
                ], [
                    'tag_name' => ucwords($value),
                ])->id;
            });

            $article->tags()->sync($tagIds);
        }

        return response()->json([
            'success' => true,
            'message' => 'Article created successfully',
            'data' => $article->load(['category:id,slug,name', 'tags:id,slug,tag_name']),
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, Article $article): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => ['sometimes', 'required', 'min:5'],
            'slug' => ['nullable', 'unique:articles,slug,' . $article->id],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['nullable', 'in:draft,published,pending'],
            'published_at' => ['nullable', 'date'],
            'content' => ['nullable'],
            'excerpt' => ['nullable', 'max:200'],
            'cover' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'min:10', 'max:80'],
            'meta_desc' => ['nullable', 'min:10', 'max:180'],
            'meta_keywords' => ['nullable', 'max:255'],
            'tags' => ['array'],
            'tags.*' => ['string'],
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

        $article->update(Arr::except($data, ['tags']));

        if (array_key_exists('tags', $data)) {
            $tagIds = collect($data['tags'] ?? [])->map(function (string $value) {
                return Tag::firstOrCreate([
                    'slug' => Str::slug($value),
                ], [
                    'tag_name' => ucwords($value),
                ])->id;
            });

            $article->tags()->sync($tagIds);
        }

        return response()->json([
            'success' => true,
            'message' => 'Article updated successfully',
            'data' => $article->load(['category:id,slug,name', 'tags:id,slug,tag_name']),
        ]);
    }

    public function destroy(Article $article): JsonResponse
    {
        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'Article deleted successfully',
        ]);
    }
}
