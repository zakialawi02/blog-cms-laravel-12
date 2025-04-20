<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Category;
use ipinfo\ipinfo\IPinfo;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\ArticleService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\ArticleResource;

class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * Display a listing of the posts.
     *
     * This method fetches and displays a list of posts, optionally filtered by a search query.
     * It also retrieves featured articles from the list of fetched articles.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */

    public function index(Request $request)
    {
        $data = ['title' => $request->query('search') ? 'Search results for "' . request()->get('search') . '"' : 'Blog'];
        $search = $request->query('search');
        $articles = $this->articleService->fetchArticles(['search' => $search]);
        $this->articleService->articlesMappingArray($articles);
        $featured = $this->articleService->getFeaturedArticles($articles);
        $randomPosts = $this->articleService->getRandomArticles(4);

        return view('pages.front.posts.posts', compact('data', 'articles', 'featured', 'randomPosts'));
    }

    /**
     * Get the IP visitor details using IPinfo API.
     *
     * @param datatype $ip The IP address of the visitor
     * @return mixed The details of the visitor based on the IP address
     */
    protected function getIpVisitor($ip)
    {
        $access_token = env('IPINFO_ACCESS_TOKEN');
        $client = new IPinfo($access_token);
        $ip_address = $ip;
        $details = $client->getDetails($ip_address);
        $dataV = $details->all;
        return $dataV;
    }

    /**
     * Store visitor data/Save visitor information if not already cached.
     *
     * @param datatype $article_id The ID of the article visited
     * @param datatype $ip The IP address of the visitor
     * @throws \Throwable Description of the exception
     * @return void
     */
    protected function saveVisitor($article_id, $ip)
    {
        $cacheKey = 'article-view:' . $article_id . ':' . $ip;
        $cacheDuration = 60 * 5; // Cache for xx minutes
        if (!Cache::has($cacheKey)) {
            $dataIpVisitor = $this->getIpVisitor($ip);
            try {
                DB::table('article_views')->insert([
                    'article_id' => $article_id,
                    'ip_address' => $ip,
                    'code' => array_key_exists('country', $dataIpVisitor) ? $dataIpVisitor['country'] : NULL,
                    'location' => array_key_exists('country_name', $dataIpVisitor) ? $dataIpVisitor['country_name'] : NULL,
                    'viewed_at' => now(),
                ]);

                Cache::put($cacheKey, true, $cacheDuration);
            } catch (\Throwable $th) {
                //throw $th;
                Log::error("Error saving visitor data: " . $th);
            }
        }
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

        $ipAddress = $request->header('CF-Connecting-IP') ?? $request->header('X-Forwarded-For');
        $this->saveVisitor($article->id, $ipAddress);

        $article = $this->articleService->articlesMappingArray(collect([$article]))->first();
        $popularPosts = $this->articleService->getPopularPosts(4);
        $categories = Category::inRandomOrder()->limit(5)->get();
        $tags = Tag::inRandomOrder()->limit(10)->get();

        return view('pages.front.posts.singlePost', compact('article',  'categories', 'popularPosts', 'tags'));
    }

    /**
     * Display a list of popular posts.
     *
     * This method retrieves the most popular articles based on view counts
     * and renders them in the 'popular' view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function popularPost()
    {
        $data = [
            'title' => 'Popular Post',
            'meta_description' => 'Popular Post of zakialawi.my.id website',
            'og_title' => 'Popular Post',
            'og_description' => 'Popular Post of zakialawi.my.id website',
        ];
        $articles = $this->articleService->getPopularPosts();
        $this->articleService->articlesMappingArray($articles);
        $randomPosts = $this->articleService->getRandomArticles(4);

        return view('pages.front.posts.popular', compact('data', 'articles', 'randomPosts'));
    }

    /**
     * Retrieve articles associated with a specific tag.
     *
     * This method fetches articles based on the provided tag slug
     * and returns a view displaying the articles.
     *
     * @param string $slug The slug of the tag to filter articles by.
     * @return \Illuminate\Contracts\View\View The view displaying the articles associated with the tag.
     */
    public function articlesByTag(string $slug)
    {
        $data = ['title' => 'Posts in tag of ' . request()->segment(3) . ' | zakialawi'];
        $articles = $this->articleService->fetchArticles(['tag' => $slug]);
        $this->articleService->articlesMappingArray($articles);
        $randomPosts = $this->articleService->getRandomArticles(4);

        return view('pages.front.posts.posts', compact('data', 'articles', 'randomPosts'));
    }

    /**
     * Retrieve articles associated with a specific category.
     *
     * This method fetches articles based on the provided category slug
     * and returns a view displaying the articles.
     *
     * @param string $slug The slug of the category to filter articles by.
     * @return \Illuminate\Contracts\View\View The view displaying the articles associated with the category.
     */
    public function articlesByCategory(string $slug)
    {
        $data = ['title' => "Posts in category of " . request()->segment(3) . " | zakialawi"];
        $articles = $this->articleService->fetchArticles(['category' => $slug]);
        $this->articleService->articlesMappingArray($articles);
        $featured = $this->articleService->getFeaturedArticles($articles);
        $randomPosts = $this->articleService->getRandomArticles(4);

        return view('pages.front.posts.posts', compact('data', 'articles', 'featured', 'randomPosts'));
    }

    /**
     * Retrieve articles associated with a specific user.
     *
     * This method fetches articles based on the provided username
     * and returns a view displaying the articles.
     *
     * @param string $username The username of the user to filter articles by.
     * @return \Illuminate\Contracts\View\View The view displaying the articles associated with the user.
     */
    public function articlesByUser(string $username)
    {
        $data = ['title' => 'Posts by ' . $username];
        $articles = $this->articleService->fetchArticles(['user' => $username]);
        $this->articleService->articlesMappingArray($articles);
        $featured = $this->articleService->getFeaturedArticles($articles);
        $randomPosts = $this->articleService->getRandomArticles(4);

        return view('pages.front.posts.posts', compact('data', 'articles', 'featured', 'randomPosts'));
    }

    /**
     * Retrieve articles associated with a specific year.
     *
     * This method fetches articles based on the provided year
     * and returns a view displaying the articles.
     *
     * @param int $year The year to filter articles by.
     * @return \Illuminate\Contracts\View\View The view displaying the articles associated with the year.
     */
    public function articlesByYear(int $year)
    {
        (!is_numeric($year)) ? abort(404) : $year;
        (strlen($year) != 4) ? abort(404) : $year;

        $data = ['title' => 'Posts in ' . $year];
        $articles = $this->articleService->fetchArticles(['year' => $year]);
        $this->articleService->articlesMappingArray($articles);
        $randomPosts = $this->articleService->getRandomArticles(4);

        return view('pages.front.posts.archive', compact('data', 'articles', 'randomPosts'));
    }

    /**
     * Retrieves articles for a specific month and year.
     *
     * This method fetches articles based on the provided year and month,
     * and returns a view displaying the articles.
     *
     * @param int $year The year to filter articles by.
     * @param int $month The month to filter articles by.
     * @return \Illuminate\Contracts\View\View The view displaying the articles associated with the specified month and year.
     */
    public function articlesByMonth(int $year, int $month)
    {
        (!is_numeric($year)) ? abort(404) : $year;
        (strlen($year) != 4) ? abort(404) : $year;
        (!is_numeric($month)) ? abort(404) : $month;
        ($month > 12 || $month < 1) ? abort(404) : $month;

        $data = ['title' => 'Posts in ' . date('F', strtotime($year . '-' . $month . '-01')) . ' ' . $year];
        $articles = $this->articleService->fetchArticles(['month' => $month, 'year' => $year]);
        $this->articleService->articlesMappingArray($articles);
        $randomPosts = $this->articleService->getRandomArticles(4);

        return view('pages.front.posts.archive', compact('data', 'articles', 'randomPosts'));
    }
}
