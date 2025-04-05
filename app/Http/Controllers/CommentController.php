<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display all listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'superadmin') {
            $comments = Comment::with('article', 'user')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Ambil hanya komentar pada artikel yang dimiliki user
            $comments = Comment::with('article', 'user')
                ->whereHas('article', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $data = [
            'title' => 'All Comments',
        ];

        return view('pages.dashboard.comments.index', compact('data', 'comments'));
    }

    /**
     * Show the user's own comments.
     *
     * @return \Illuminate\View\View
     */
    public function mycomments()
    {
        $comments = Comment::with('article', 'user')
            ->where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'title' => 'My Comments',
        ];

        return view('pages.dashboard.comments.myComments', compact('data', 'comments'));
    }

    public function store(Article $post)
    {
        $validator = Validator::make(request()->all(), [
            'comment' => 'required',
        ]);
        if (!$validator->passes()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create comment',
                'errors' => $validator->errors()
            ], 422);
        }

        $article_id = $post->id;
        $author_id = $post->user_id;
        $parent_id = request('parent_id');
        $parent_id = $parent_id === null ? null : explode('zkc_0212', $parent_id)[1];
        $user_id = Auth::user()->id;

        $comment = Comment::create([
            'article_id' => $article_id,
            'parent_id' => $parent_id,
            'user_id' => $user_id,
            'content' => request('comment'),
        ]);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create comment'
            ]);
        }

        return response()->json([
            'success' => true,
            'comment' => $comment
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * Delete a comment via AJAX if the request is an AJAX request.
     * Otherwise, delete the comment and redirect back to the my comments page.
     *
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Comment $comment)
    {
        if (request()->ajax()) {
            Comment::where('id', $comment->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);
        }

        Comment::where('id', $comment->id)->delete();

        return redirect()->route('admin.mycomments.index')->with('success', 'Comment deleted successfully');
    }
}
