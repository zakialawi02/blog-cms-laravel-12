<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Tag;
use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use App\Services\AiService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\ArticleService;
use App\Actions\UploadCoverImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ArticleRequest;
use App\Actions\DeletePostPermanently;
use App\Services\SectionContentService;
use Illuminate\Database\QueryException;
use Yajra\DataTables\Facades\DataTables;

class PostController extends Controller
{
    protected $aiService;
    protected $articleService;
    protected $sectionContentService;

    public function __construct(AiService $aiService, ArticleService $articleService, SectionContentService $sectionContentService)
    {
        $this->aiService = $aiService;
        $this->articleService = $articleService;
        $this->sectionContentService = $sectionContentService;
    }

    private function resolvePublishingData(Request $request, array &$data, ?Article $post = null): void
    {
        $isPrivilegedUser = in_array(Auth::user()->role, ['superadmin', 'admin'], true);
        $action = $request->input('action');

        if ($action === 'draft') {
            $data['status'] = 'draft';
            // Keep the original publish date for posts that were already published.
            $data['published_at'] = ($post && $post->status === 'published' && $post->published_at)
                ? $post->published_at
                : null;
            return;
        }

        if ($isPrivilegedUser) {
            $data['status'] = 'published';

            // If the post has ever been published, preserve its original publish date.
            if ($post && $post->published_at) {
                $data['published_at'] = $post->published_at;
            } else {
                $data['published_at'] = $data['published_at'] ?? now();
            }
            return;
        }

        $data['status'] = 'pending';
        $data['published_at'] = null;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $privilegedRoles = ['superadmin', 'admin'];
            $query = Article::select('id', 'title', 'slug', 'excerpt', 'cover', 'category_id', 'published_at', 'status', 'created_at', 'updated_at', 'deleted_at', 'user_id')
                ->with(['user', 'category', 'tags'])
                ->withCount('articleViews as total_views')
                // Filter by status
                ->when(request('status') && request('status') !== 'all', function ($query) {
                    if (request('status') === 'trash') {
                        return $query->onlyTrashed();
                    }
                    return $query->where('status', request('status'));
                })
                // Filter by category
                ->when(request('category') && request('category') !== 'all', function ($query) {
                    if (request('category') === 'uncategorized') {
                        // Ambil artikel tanpa kategori
                        return $query->whereNull('category_id');
                    } else {
                        return $query->whereHas('category', function ($q) {
                            $q->where('slug', request('category'));
                        });
                    }
                })
                // Filter by author
                ->when(!in_array(Auth::user()->role, $privilegedRoles), function ($query) {
                    return $query->whereHas('user', function ($q) {
                        $q->where('username', Auth::user()->username);
                    });
                }, function ($query) {
                    if (request('author') && request('author') !== 'all') {
                        return $query->whereHas('user', function ($q) {
                            $q->where('username', request('author'));
                        });
                    }
                    return $query;
                });

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    if (request('status') === 'trash' && $data->deleted_at) {
                        return '<button type="button" class="btn bg-back-primary dark:bg-back-back-dark-primary restorePost" data-slug="' . $data->slug . '"><span class="ri-refresh-line" title="Restore"></span></button>
                        <button type="button" class="btn bg-back-error permanentlyDeletePost" data-slug="' . $data->slug . '" title="Permanently Delete"><span class="ri-delete-bin-line"></span></button>';
                    } else {
                        $buttons = '';
                        if ($data->status === 'published') {
                            $buttons .= '<a href="' . route('article.show', ['year' => $data->published_at->format('Y'), 'slug' => $data->slug]) . '" class="btn bg-back-info viewPost" target="_blank"><span class="ri-eye-line" title="View"></span></a>';
                        }
                        $buttons .= '<a href="' . route('admin.posts.preview', $data->slug) . '" class="btn bg-back-dark ml-1 previewPost" data-slug="' . $data->slug . '" title="Preview"><span class="ri-mac-line"></span></a>
                        <a href="' . route('admin.posts.edit', $data->slug) . '" class="btn bg-back-secondary editPost" data-slug="' . $data->slug . '" title="Edit"><span class="ri-edit-box-line"></span></a>
                        <button type="button" class="btn bg-back-error deletePost" data-slug="' . $data->slug . '" title="Delete"><span class="ri-delete-bin-line"></span></button>';

                        return $buttons;
                    }
                })
                ->addColumn('user', function ($data) {
                    return $data->user->username;
                })
                ->editColumn('title', function ($data) {
                    return Str::limit($data->title, 50);
                })
                ->editColumn('status', function ($data) {
                    if (request('status') === 'trash' && $data->deleted_at) {
                        return '<span class="badge bg-back-error">Deleted on: </span><br>' . $data->deleted_at->format('d M Y');
                    } else if ($data->published_at > now()) {
                        return '<span class="badge bg-back-warning text-back-dark">Scheduled: </span><br>' . $data->published_at->format('d M Y');
                    } elseif ($data->status === 'published') {
                        return '<span class="badge bg-back-success">Published on: </span><br>' . $data->published_at->format('d M Y') . '</span>';
                    } else {
                        return '<span class="badge bg-back-secondary">' . $data->status . '</span>';
                    }
                })
                ->addColumn('category', function ($data) {
                    return $data->category->category ?? 'Uncategorized';
                })
                ->addColumn('tags', function ($data) {
                    return $data->tags->pluck('tag_name')->implode(', ');
                })
                ->addColumn('author', function ($data) {
                    return $data->user->username;
                })
                ->editColumn('created_at', function ($data) {
                    return $data->created_at ? $data->created_at->format("d M Y") : '-';
                })
                ->editColumn('updated_at', function ($data) {
                    return $data->updated_at ? $data->updated_at->format("d M Y") : '-';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);;
        }

        $data = [
            'title' => 'All Posts',
        ];

        $users = User::orderBy('username', 'asc')->get();
        $categories = Category::orderBy('category', 'asc')->get();

        return view('pages.dashboard.posts.index', compact('data', 'users', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'title' => 'Create Post',
        ];

        $categories = Category::all();
        $tags = Tag::all();
        $users = Auth::user()->role === 'superadmin'
            ? User::orderBy('username', 'asc')->get()
            : [Auth::user()];

        return view('pages.dashboard.posts.create', compact('data', 'categories', 'tags', 'users'));
    }

    /**
     * Show the preview page of a post.
     *
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function preview($slug)
    {
        $data = [
            'title' => 'Preview Post',
        ];

        $article = $this->articleService->articlesMappingArray(collect(Article::where('slug', $slug)->get()))->first();
        $data['menu'] = $this->sectionContentService->getNavigationData();

        return view('pages.dashboard.posts.preview', compact('data', 'article', 'data'));
    }

    /**
     * Generate an article using the AI service.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function generateArticle(Request $request)
    {
        set_time_limit(0);
        $request->validate([
            'prompt' => 'required|string',
            'type' => 'required|in:text',
        ]);

        $prompt = $request->input('prompt');
        $exsistData = $request->input('exsistData');
        $type = $request->input('type');

        $response = $this->aiService->generateArticle($prompt, $exsistData);

        return $response;
    }

    /**
     * Generate an image using the AI service. ** ERROR **
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function generateImage(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
            'type' => 'required|in:image',
        ]);

        $prompt = $request->input('prompt');
        $type = $request->input('type');

        $response = $this->aiService->generateImage($prompt);

        return $response;
    }

    /**
     * Store a newly created article in storage.
     *
     * This method validates the incoming request and creates a new article
     * instance. It handles file uploads for the article cover image, determines
     * the publication status based on the request, and syncs associated tags.
     * The article is then stored in the database, and a redirect response with
     * a success message is returned.
     *
     * @param  \App\Http\Requests\ArticleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ArticleRequest $request)
    {
        try {
            $data = $request->validated();
            $this->resolvePublishingData($request, $data);

            // Handle upload cover
            $uploader = new UploadCoverImage();
            $data['cover'] = $uploader->execute($request->file('cover'));
            if (filled($data['cover'])) {
                $data['cover_large'] = str_replace('_small.', '_large.', $data['cover']);
            }

            // Buat artikel
            Article::create($data);

            $message = 'Post created successfully.';
            if ($data['status'] === 'pending') {
                $message .= ' Post is waiting for approval.';
            } elseif ($data['status'] === 'published') {
                $message .= ' Post is published.';
            } else {
                $message .= ' Post is saved as draft.';
            }
            return redirect()->route('admin.posts.index')->with('success', $message);
        } catch (QueryException $e) {
            Log::error("Database error during article creation: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create post. Database error.');
        } catch (Exception $e) {
            Log::error("Unexpected error during article creation: " . $e->getMessage());
            return back()->withInput()->with('error', 'An unexpected error occurred while creating the post.');
        }
    }

    /**
     * Show the form for editing the specified article.
     *
     * Checks if the authenticated user is authorized to edit the article.
     * Retrieves all categories and tags associated with the article
     * and passes them to the edit view.
     *
     * @param  \App\Models\Article  $post
     * @return \Illuminate\View\View
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */

    public function edit(Article $post)
    {
        // Cek apakah user memiliki izin untuk mengeksekusi
        if (! $post->isOwnedOrSuperadminOrAdmin(Auth::user())) {
            abort(403, 'You do not have permission to edit this post.');
        }

        $data = [
            'title' => 'Edit Post',
        ];

        $categories = Category::all();
        $tags = Tag::all();
        $users = Auth::user()->role === 'superadmin' || Auth::user()->role === 'admin'
            ? User::orderBy('username', 'asc')->get()
            : [Auth::user()];

        return view('pages.dashboard.posts.edit', compact('data', 'post', 'categories',  'tags', 'users'));
    }

    /**
     * Approve a pending post.
     *
     * This method sets the article's status to 'published' and saves the article.
     * A redirect response with a success message is returned.
     *
     * @param  \App\Models\Article  $post
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Article $post, Request $request)
    {
        $post->status = $request->input('status') ?? 'published';
        $post->published_at = now();
        $post->save();

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * This method validates the incoming request and creates a new article
     * instance. It handles file uploads for the article cover image, determines
     * the publication status based on the request, and syncs associated tags.
     * The article is then stored in the database, and a redirect response with
     * a success message is returned.
     *
     * @param  \App\Http\Requests\ArticleRequest  $request
     * @param  \App\Models\Article  $post
     * @return \Illuminate\Http\Response
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function update(ArticleRequest $request, Article $post)
    {
        // dd($request->all());
        try {
            // Cek apakah user memiliki izin untuk mengeksekusi
            if (! $post->isOwnedOrSuperadminOrAdmin(Auth::user())) {
                abort(403, 'You do not have permission to edit this post.');
            }

            // Validasi request
            $data = $request->validated();
            $this->resolvePublishingData($request, $data, $post);

            // Handle upload cover
            $uploader = new UploadCoverImage();
            $data['cover'] = $uploader->execute($request->file('cover'), $post->cover);
            if (filled($data['cover'])) {
                $data['cover_large'] = str_replace('_small.', '_large.', $data['cover']);
            }

            // Update post
            $post->update($data);

            $message = 'Post updated successfully.';
            if ($data['status'] === 'pending') {
                $message .= ' Post is waiting for approval.';
            } elseif ($data['status'] === 'published') {
                $message .= ' Post is published.';
            } else {
                $message .= ' Post is saved as draft.';
            }
            return redirect()->route('admin.posts.index')->with('success', $message);
        } catch (QueryException $e) {
            Log::error("Database error during article update: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update post. Database error.');
        } catch (Exception $e) {
            Log::error("Unexpected error during article update: " . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred while updating the post.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * Checks if the authenticated user is authorized to delete the article.
     * Only superadmins and the article's owner can delete the article.
     *
     * @param  \App\Models\Article  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Article $post): JsonResponse
    {
        try {
            // Cek apakah user memiliki izin untuk mengeksekusi
            if (! $post->isOwnedOrSuperadmin(Auth::user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action'
                ], Response::HTTP_FORBIDDEN);
            }

            // Hapus artikel dari database
            $deleted = $post->delete();

            if (!$deleted) {
                throw new Exception("Failed to delete the post.");
            }

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully'
            ], Response::HTTP_OK);
        } catch (QueryException $e) {
            Log::error("Database error during article deletion: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete post. Database error.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            Log::error("Unexpected error during article deletion: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while deleting the post.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage permanently.
     *
     * Checks if the authenticated user is authorized to delete the article.
     * Only superadmins and the article's owner can delete the article.
     * If the article has a cover, it will be deleted from the storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function permanentlyDelete($slug, DeletePostPermanently $deletePost): JsonResponse
    {
        try {
            // Cari artikel yang sudah dihapus (soft delete)
            $post = Article::onlyTrashed()->where('slug', $slug)->firstOrFail();

            // Hapus permanen artikel
            $deleted = $deletePost->execute($post);

            if (!$deleted) {
                throw new Exception("Failed to permanently delete the post.");
            }

            return response()->json([
                'success' => true,
                'message' => 'Post permanently deleted successfully'
            ], Response::HTTP_OK);
        } catch (QueryException $e) {
            Log::error("Database error during article permanent deletion: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete post. Database error.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            Log::error("Unexpected error during article permanent deletion: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while permanently deleting the post.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Restore the specified resource from storage.
     *
     * Checks if the authenticated user is authorized to restore the article.
     * Only superadmins and the article's owner can restore the article.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($slug)
    {
        try {
            // Cari artikel yang sudah dihapus (soft delete)
            $post = Article::onlyTrashed()->where('slug', $slug)->firstOrFail();

            // Restore artikel
            $restored = $post->restore();

            if (!$restored) {
                throw new Exception("Failed to restore the post.");
            }

            return response()->json([
                'success' => true,
                'message' => 'Post restored successfully'
            ], Response::HTTP_OK);
        } catch (QueryException $e) {
            Log::error("Database error during article restoration: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore post. Database error.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            Log::error("Unexpected error during article restoration: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while restoring the post.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generate a slug from a given string.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateSlug(Request $request): JsonResponse
    {
        try {
            $slug = Str::slug($request->data);

            return response()->json([
                'success' => true,
                'slug' => $slug
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
