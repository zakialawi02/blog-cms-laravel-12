<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Tag;
use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Actions\UploadCoverImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ArticleRequest;
use App\Actions\DeletePostPermanently;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PostController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Article::select('id', 'title', 'content', 'slug', 'excerpt', 'cover', 'category_id', 'published_at', 'status', 'created_at', 'updated_at', 'deleted_at', 'user_id')
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
                ->when(Auth::user()->role !== 'superadmin', function ($query) {
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
                        if ($data->status !== 'draft') {
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

        $article = Article::where('slug', $slug)->first();

        return view('pages.dashboard.posts.preview', compact('data', 'article'));
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

            // Tentukan status publish/unpublish
            if ($request->has('publish')) {
                $data['status'] = 'published';
                $data['published_at'] = $data['published_at'] ?? now();
            } elseif ($request->has('unpublish')) {
                $data['status'] = 'draft';
                $data['published_at'] = null;
            }

            // Handle upload cover
            $uploader = new UploadCoverImage();
            $data['cover'] = $uploader->execute($request->file('cover'));

            // Buat artikel
            Article::create($data);

            return redirect()->route('admin.posts.index')->with('success', 'Post created successfully.');
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
        if (! $post->isOwnedOrSuperadmin(Auth::user())) {
            abort(403, 'You do not have permission to edit this post.');
        }

        $data = [
            'title' => 'Edit Post',
        ];

        $categories = Category::all();
        $tags = Tag::all();
        $users = Auth::user()->role === 'superadmin'
            ? User::orderBy('username', 'asc')->get()
            : [Auth::user()];

        return view('pages.dashboard.posts.edit', compact('data', 'post', 'categories',  'tags', 'users'));
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
        try {
            // Cek apakah user memiliki izin untuk mengeksekusi
            if (! $post->isOwnedOrSuperadmin(Auth::user())) {
                abort(403, 'You do not have permission to edit this post.');
            }

            // Validasi request
            $data = $request->validated();

            // Set status publish/unpublish
            if ($request->has('publish')) {
                $data['status'] = 'published';
                $data['published_at'] = $data['published_at'] ?? now();
            } elseif ($request->has('unpublish')) {
                $data['status'] = 'draft';
                $data['published_at'] = null;
            }

            // Handle upload cover
            $uploader = new UploadCoverImage();
            $data['cover'] = $uploader->execute($request->file('cover'), $post->cover);

            // Update post
            $post->update($data);

            return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully.');
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
