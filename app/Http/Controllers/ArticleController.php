<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use ipinfo\ipinfo\IPinfo;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\ArticleService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\SectionContentService;

class ArticleController extends Controller
{
    protected $articleService;
    protected $sectionContentService;

    public function __construct(ArticleService $articleService, SectionContentService $sectionContentService)
    {
        $this->articleService = $articleService;
        $this->sectionContentService = $sectionContentService;
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
        $sectionsContent = $this->sectionContentService->getSectionData();

        return view('pages.front.posts.posts', compact('data', 'articles',  'sectionsContent'));
    }

    /**
     * Get the IP visitor details using IPinfo API.
     *
     * @param datatype $ip The IP address of the visitor
     * @return mixed The details of the visitor based on the IP address
     */
    protected function getIpVisitor($ip)
    {
        try {
            $access_token = env('IPINFO_ACCESS_TOKEN');
            $client = new IPinfo($access_token);
            $ip_address = $ip;
            $details = $client->getDetails($ip_address);
            $dataV = $details->all;
            return $dataV;
        } catch (\Throwable $th) {
            Log::error("Error getting IP visitor details: " . $th);
            return [];
        }
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
            $agent = new Agent();
            if ($agent->robot()) return;
            $dataIpVisitor = $this->getIpVisitor($ip);
            try {
                DB::table('article_views')->insert([
                    'article_id' => $article_id,
                    'operating_system' => $agent->platform() ?? NULL,
                    'browser' => $agent->browser() ?? NULL,
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
     * Record visitor data/Save visitor information if not already cached.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $year
     * @param  string  $month
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function recordVisitor(Request $request, $year, $month, $slug, $token)
    {
        try {
            if (!is_numeric($year) || !is_numeric($month) || $month < 1 || $month > 12) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid format.'
                ], 400);
            }

            $expectedToken = Cache::get("article_visit_token_{$slug}");

            if (!$expectedToken || $token !== $expectedToken) {
                Log::warning("Invalid or expired token for article slug: {$slug}. Provided token: {$token}, Expected: {$expectedToken}");
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired security token.'
                ], 403);
            }

            Cache::forget("article_visit_token_{$slug}");

            $targetMonth = Carbon::createFromFormat('Y-m', "$year-$month");

            $article = Article::where('slug', $slug)
                ->whereMonth('published_at', $targetMonth->month)
                ->whereYear('published_at', $targetMonth->year)
                ->where('published_at', '<=', Carbon::now())
                ->first();

            if (!$article) {
                return response()->json([
                    'success' => false,
                    'message' => 'Article not found or not yet published.'
                ], 404);
            }

            $article_id = $article->id;

            $ip = $request->header('CF-Connecting-IP') ?? $request->header('X-Forwarded-For') ?? $request->ip();

            $this->saveVisitor($article_id, $ip);

            return response()->json([
                'success' => true,
                'message' => 'Visitor recorded successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("Article not found for slug: {$slug} in {$year}-{$month}. Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Article not found.'
            ], 404);
        } catch (\Throwable $e) {
            Log::error("Error recording visitor for slug: {$slug}. Error: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_ip' => $request->ip(),
                'headers' => $request->headers->all(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while recording the visit.',
            ], 500);
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

        $visitToken = Str::uuid()->toString();
        Cache::put("article_visit_token_{$slug}", $visitToken, Carbon::now()->addMinutes(5));
        // $ipAddress = $request->header('CF-Connecting-IP') ?? $request->header('X-Forwarded-For');
        // $this->saveVisitor($article->id, $ipAddress);

        $article = $this->articleService->articlesMappingArray(collect([$article]))->first();
        $sectionsContent = $this->sectionContentService->getSectionData();

        return view('pages.front.posts.singlePost', compact('article', 'visitToken', 'sectionsContent'));
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
        $sectionsContent = $this->sectionContentService->getSectionData();

        return view('pages.front.posts.popular', compact('data', 'articles',  'sectionsContent'));
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
        $sectionsContent = $this->sectionContentService->getSectionData();

        return view('pages.front.posts.posts', compact('data', 'articles', 'sectionsContent'));
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
        $sectionsContent = $this->sectionContentService->getSectionData();

        return view('pages.front.posts.posts', compact('data', 'articles',  'sectionsContent'));
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
        $sectionsContent = $this->sectionContentService->getSectionData();

        return view('pages.front.posts.posts', compact('data', 'articles',  'sectionsContent'));
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
        $sectionsContent = $this->sectionContentService->getSectionData();

        return view('pages.front.posts.archive', compact('data', 'articles', 'sectionsContent'));
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
        $sectionsContent = $this->sectionContentService->getSectionData();

        return view('pages.front.posts.archive', compact('data', 'articles', 'sectionsContent'));
    }
}
