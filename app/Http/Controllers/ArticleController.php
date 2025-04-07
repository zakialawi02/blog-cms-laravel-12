<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\ArticleResource;

class ArticleController extends Controller
{
    /**
     * Fetch filtered and paginated articles.
     *
     * @param string|null $search
     * @param string|null $categorySlug
     * @param string|null $tagSlug
     * @param string|null $username
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function fetchArticles(array $filters = [])
    {
        $query = Article::with(['user', 'category', 'tags'])
            ->published()
            ->orderBy('published_at', 'desc');

        if (!empty($filters['category'])) {
            if ($filters['category'] === 'uncategorized') {
                $query->whereNull('category_id');
            } else {
                $query->withCategorySlug($filters['category']);
            }
        }

        if (!empty($filters['tag'])) {
            $query->withTagSlug($filters['tag']);
        }

        if (!empty($filters['user'])) {
            $query->withUsername($filters['user']);
        }

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        return $query->paginate(9)->withQueryString();
    }

    public function index()
    {
        $data = [
            'title' => 'Posts',
        ];

        $search = request()->query('search');
        $articles = $this->fetchArticles([
            'search' => $search,
        ]);
        $articles =  ArticleResource::collection($articles);
        $featured = $articles->filter(fn($article) => $article->is_featured)->shuffle()->take(5);
        if ($featured->count() < 5) {
            $nonFeatured = $articles->reject(fn($article) => $article->is_featured)->shuffle()->take(5 - $featured->count());
            $featured = $featured->merge($nonFeatured);
        }

        return view('pages.front.posts.posts', compact('data', 'articles', 'featured'));
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
