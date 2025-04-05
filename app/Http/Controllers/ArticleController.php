<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ArticleController extends Controller
{
    /**
     * Maps and modifies the provided articles array by updating excerpt, cover image paths, and category ID if necessary.
     *
     * @param datatype $articles The array of articles to be mapped and modified.
     * @throws Some_Exception_Class description of exception
     * @return Some_Return_Value
     */
    protected function articlesMappingArray($articles)
    {
        $articles = $articles->map(function ($article) {
            if (empty($article->excerpt)) {
                $article->excerpt = Str::limit(strip_tags($article->content), 200);
            }
            if (!empty($article->cover)) {
                $article->cover = asset("storage/drive/" . $article->user->username . "/img/" . $article->cover);
            }
            if (empty($article->cover)) {
                $article->cover = asset("assets/img/image-placeholder.webp");
            }
            if (empty($article->category_id)) {
                $article->category_id = "Uncategorized";
            }
            return $article;
        });
        $articles->map(function ($article) {
            $article->excerpt = Str::limit($article->excerpt, 200);
        });
    }

    /**
     * Show the specified article.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $year
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $year, $slug)
    {
        if ($request->ajax() && $request->has('ajax')) {
            $article = Article::where('slug', $slug)
                ->where('published_at', '<=', Carbon::now())
                ->firstOrFail();
            $comments = Comment::where('article_id', $article->id)->whereNull('parent_id')->get();

            return view('components.comment-section', compact('comments'));
        }

        $article = Article::with('user', 'category', 'tags')
            ->where('slug', $slug)
            ->whereYear('published_at', $year)
            ->where('published_at', '<=', Carbon::now())
            ->firstOrFail();
        $article['cover'] = (!empty($article->cover) ? $article->cover = asset("storage/drive/" . $article->user->username . "/img/" . $article->cover) : $article->cover = asset("assets/img/image-placeholder.webp"));
        $article['excerpt'] = !empty($article->excerpt) ? $article->excerpt : Str::limit(strip_tags($article->content), 200);

        $popularPosts = [];

        $categories = Category::all();

        return view('pages.front.posts.singlePost', compact('article',  'categories', 'popularPosts'));
    }
}
