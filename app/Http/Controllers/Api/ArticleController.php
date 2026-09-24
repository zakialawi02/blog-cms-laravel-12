<?php

namespace App\Http\Controllers\Api;

use App\Actions\UploadCoverImage;
use App\Actions\DeletePostPermanently;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function __construct(protected ArticleService $articleService) {}

    private function resolveArticle(string $identifier, bool $onlyTrashed = false): Article
    {
        $query = $onlyTrashed ? Article::onlyTrashed() : Article::query();
        return $query->where(function ($q) use ($identifier) {
            $q->where('id', $identifier)->orWhere('slug', $identifier);
        })->firstOrFail();
    }

    public function store(StoreArticleRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();

            $action = $request->input('action', 'publish');
            $role = Auth::user()->role;
            $this->articleService->resolvePublishingData($action, null, $role, $data);

            // is_featured only for privileged roles — preserve value before unset
            $isFeaturedInput = $data['is_featured'] ?? null;
            if (! in_array($role, ['superadmin', 'admin'], true)) {
                unset($data['is_featured']);
            } elseif ($isFeaturedInput !== null) {
                $data['is_featured'] = filter_var($isFeaturedInput, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }

            // Handle cover upload
            $uploader = new UploadCoverImage();
            $data['cover'] = $uploader->execute($request->file('cover'));
            if (filled($data['cover'])) {
                $data['cover_large'] = str_replace('_small.', '_large.', $data['cover']);
            }

            // Remove non-fillable keys before create
            unset($data['action'], $data['tags']);

            $article = Article::create($data);

            // Explicit tag sync for API (supports both formats)
            $tagsInput = $request->input('tags');
            if (! empty($tagsInput)) {
                // If tags came as JSON string, decode it
                if (is_string($tagsInput)) {
                    $tagsInput = json_decode($tagsInput, true);
                }
                if (is_array($tagsInput)) {
                    $article->syncTagsFromArray($tagsInput);
                }
            }

            $article->load(['user', 'category', 'tags']);

            $message = match ($article->status) {
                'published' => 'Article published successfully.',
                'pending'   => 'Article created and pending approval.',
                default     => 'Article saved as draft.',
            };

            return (new ArticleResource($article))->additional([
                'success' => true,
                'message' => $message,
            ])->response()->setStatusCode(Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: Failed to create article.',
                'error'   => \App\Support\ErrorReporter::refString($e, 'Api\ArticleController::store'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error'   => \App\Support\ErrorReporter::refString($th, 'Api\ArticleController::store'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(string $article): JsonResponse
    {
        try {
            $model = $this->resolveArticle($article);
            if (! $model->isOwnedOrSuperadminOrAdmin(Auth::user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden: You do not have permission to delete this article.',
                ], Response::HTTP_FORBIDDEN);
            }
            $model->delete();
            return response()->json([
                'success' => true,
                'message' => 'Article moved to trash successfully.',
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Article not found'], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error'   => \App\Support\ErrorReporter::refString($th, 'Api\ArticleController::destroy'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function forceDestroy(string $article, DeletePostPermanently $deleter): JsonResponse
    {
        try {
            $model = $this->resolveArticle($article, true);
            if (! $model->isOwnedOrSuperadmin(Auth::user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden: Only owner or superadmin can permanently delete.',
                ], Response::HTTP_FORBIDDEN);
            }
            $deleter->execute($model);
            return response()->json(['success' => true, 'message' => 'Article permanently deleted.'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Article not found in trash.'], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete.',
                'error'   => \App\Support\ErrorReporter::refString($th, 'Api\ArticleController::forceDestroy'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function restore(string $article): JsonResponse
    {
        try {
            $model = $this->resolveArticle($article, true);
            if (! $model->isOwnedOrSuperadminOrAdmin(Auth::user())) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You do not have permission to restore this article.'], Response::HTTP_FORBIDDEN);
            }
            $model->restore();
            $model->load(['user', 'category', 'tags']);
            return (new ArticleResource($model))->additional([
                'success' => true,
                'message' => 'Article restored successfully.',
            ])->response()->setStatusCode(Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Article not found in trash.'], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error'   => \App\Support\ErrorReporter::refString($th, 'Api\ArticleController::restore'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
