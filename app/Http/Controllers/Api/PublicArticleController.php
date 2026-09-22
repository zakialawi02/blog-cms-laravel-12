<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleSummaryResource;
use App\Services\ArticleService;

class PublicArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * Display a listing of the public articles (with content).
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'sort', 'direction', 'is_featured', 'random']);
            $filters['per_page'] = \App\Support\QueryParams::perPage($request, 9, 100);

            $articles = $this->articleService->fetchArticles($filters);

            return ArticleResource::collection($articles)->additional([
                'success' => true,
                'message' => 'Articles retrieved successfully',
            ])->response()->setStatusCode(Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve articles',
                'error' => \App\Support\ErrorReporter::refString($th, 'PublicArticleController::index'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display a listing of the public articles summary (without content).
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'sort', 'direction', 'is_featured', 'random']);
            $filters['per_page'] = \App\Support\QueryParams::perPage($request, 9, 100);

            $articles = $this->articleService->fetchArticles($filters);

            return ArticleSummaryResource::collection($articles)->additional([
                'success' => true,
                'message' => 'Articles summary retrieved successfully',
            ])->response()->setStatusCode(Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve articles summary',
                'error' => \App\Support\ErrorReporter::refString($th, 'PublicArticleController::summary'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display a list of popular posts.
     */
    public function popularPost(Request $request): JsonResponse
    {
        try {
            $limit = \App\Support\QueryParams::optionalLimit($request, 100);

            $articles = $this->articleService->getPopularPosts($limit);

            return ArticleResource::collection($articles)->additional([
                'success' => true,
                'message' => 'Popular articles retrieved successfully',
            ])->response()->setStatusCode(Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve popular articles',
                'error' => \App\Support\ErrorReporter::refString($th, 'PublicArticleController::popularPost'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Retrieve articles associated with a specific category.
     */
    public function articlesByCategory(Request $request, ?string $slug = null): JsonResponse
    {
        if (empty($slug) || str_starts_with($slug, ':')) {
            return response()->json([
                'success' => false,
                'message' => 'Category slug is required',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $filters = $request->only(['search', 'sort', 'direction']);
            $filters['per_page'] = \App\Support\QueryParams::perPage($request, 9, 100);
            $filters['category'] = $slug;

            $articles = $this->articleService->fetchArticles($filters);

            return ArticleResource::collection($articles)->additional([
                'success' => true,
                'message' => 'Category articles retrieved successfully',
            ])->response()->setStatusCode(Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve category articles',
                'error' => \App\Support\ErrorReporter::refString($th, 'PublicArticleController::articlesByCategory'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Retrieve articles associated with a specific tag.
     */
    public function articlesByTag(Request $request, ?string $slug = null): JsonResponse
    {
        if (empty($slug) || str_starts_with($slug, ':')) {
            return response()->json([
                'success' => false,
                'message' => 'Tag slug is required',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $filters = $request->only(['search', 'sort', 'direction']);
            $filters['per_page'] = \App\Support\QueryParams::perPage($request, 9, 100);
            $filters['tag'] = $slug;

            $articles = $this->articleService->fetchArticles($filters);

            return ArticleResource::collection($articles)->additional([
                'success' => true,
                'message' => 'Tag articles retrieved successfully',
            ])->response()->setStatusCode(Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tag articles',
                'error' => \App\Support\ErrorReporter::refString($th, 'PublicArticleController::articlesByTag'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Retrieve articles associated with a specific user.
     */
    public function articlesByUser(Request $request, ?string $username = null): JsonResponse
    {
        if (empty($username) || str_starts_with($username, ':') || str_starts_with($username, '{')) {
            return response()->json([
                'success' => false,
                'message' => 'Author username is required',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $filters = $request->only(['search', 'sort', 'direction']);
            $filters['per_page'] = \App\Support\QueryParams::perPage($request, 9, 100);
            $filters['user'] = $username;

            $articles = $this->articleService->fetchArticles($filters);

            return ArticleResource::collection($articles)->additional([
                'success' => true,
                'message' => 'User articles retrieved successfully',
            ])->response()->setStatusCode(Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user articles',
                'error' => \App\Support\ErrorReporter::refString($th, 'PublicArticleController::articlesByUser'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Retrieve articles associated with a specific year.
     */
    public function articlesByYear(Request $request, ?string $year = null): JsonResponse
    {
        if (empty($year) || str_starts_with($year, ':') || str_starts_with($year, '{')) {
            return response()->json([
                'success' => false,
                'message' => 'Archive year is required',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!is_numeric($year) || strlen((string) $year) != 4) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid year format. Expected 4 digits.',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $filters = $request->only(['search', 'sort', 'direction']);
            $filters['per_page'] = \App\Support\QueryParams::perPage($request, 9, 100);
            $filters['year'] = $year;

            $articles = $this->articleService->fetchArticles($filters);

            return ArticleResource::collection($articles)->additional([
                'success' => true,
                'message' => 'Archive articles by year retrieved successfully',
            ])->response()->setStatusCode(Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve archive articles',
                'error' => \App\Support\ErrorReporter::refString($th, 'PublicArticleController::articlesByYear'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Retrieves articles for a specific month and year.
     */
    public function articlesByMonth(Request $request, ?string $year = null, ?string $month = null): JsonResponse
    {
        if (empty($year) || empty($month) || str_starts_with($year, ':') || str_starts_with($month, ':') || str_starts_with($year, '{') || str_starts_with($month, '{')) {
            return response()->json([
                'success' => false,
                'message' => 'Archive year and month are required',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!is_numeric($year) || strlen((string) $year) != 4 || !is_numeric($month) || $month > 12 || $month < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid year or month format.',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $filters = $request->only(['search', 'sort', 'direction']);
            $filters['per_page'] = \App\Support\QueryParams::perPage($request, 9, 100);
            $filters['month'] = $month;
            $filters['year'] = $year;

            $articles = $this->articleService->fetchArticles($filters);

            return ArticleResource::collection($articles)->additional([
                'success' => true,
                'message' => 'Archive articles by month retrieved successfully',
            ])->response()->setStatusCode(Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve archive articles',
                'error' => \App\Support\ErrorReporter::refString($th, 'PublicArticleController::articlesByMonth'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified public article.
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $article = Article::with(['user', 'category', 'tags'])
                ->published()
                ->where('slug', $slug)
                ->firstOrFail();

            return (new ArticleResource($article))->additional([
                'success' => true,
                'message' => 'Article retrieved successfully',
            ])->response()->setStatusCode(Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => \App\Support\ErrorReporter::refString($th, 'PublicArticleController::show'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
