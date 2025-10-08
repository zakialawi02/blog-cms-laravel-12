<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ArticleService
{
    /**
     * Default number of articles per page for paginated responses.
     */
    protected int $defaultPerPage = 9;

    /**
     * Cache lifetime in minutes for frequently accessed article lists.
     */
    protected int $cacheTtl = 10;

    /**
     * Fetch filtered and paginated articles.
     *
     * @param string|null $search
     * @param string|null $categorySlug
     * @param string|null $tagSlug
     * @param string|null $username
     * @param int|null $year
     * @param int|null $month
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function fetchArticles(array $filters = [])
    {
        $page = (int) ($filters['page'] ?? request()->query('page', 1));
        $perPage = (int) ($filters['per_page'] ?? $this->defaultPerPage);

        $cacheKey = $this->buildCacheKey('articles', array_merge($filters, [
            'page' => $page,
            'per_page' => $perPage,
        ]));

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheTtl), function () use ($filters, $perPage, $page) {
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

            if (!empty($filters['year'])) {
                $query->whereYear('published_at', $filters['year']);
            }

            if (!empty($filters['month'])) {
                $query->whereMonth('published_at', $filters['month']);
            }

            if (!empty($filters['search'])) {
                $query->search($filters['search']);
            }

            $total = (clone $query)->toBase()->getCountForPagination();
            $items = $query->forPage($page, $perPage)->get();

            $paginator = new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );

            return $paginator->appends(request()->query());
        });
    }

    /**
     * Get popular articles sorted by total views.
     */
    public function getPopularPosts(?int $limit = null)
    {
        $page = $limit ? 1 : (int) request()->query('page', 1);
        $cacheKey = $this->buildCacheKey('popular_posts', [
            'limit' => $limit,
            'page' => $page,
            'per_page' => $this->defaultPerPage,
        ]);

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheTtl), function () use ($limit) {
            $query = Article::has('articleViews')
                ->withCount(['articleViews as total_views'])
                ->with(['user', 'category'])
                ->published()
                ->orderByDesc('total_views');

            if ($limit) {
                return $query->take($limit)->get();
            }

            return $query->paginate($this->defaultPerPage)->withQueryString();
        });
    }

    /**
     * Get random articles. If a category slug is given, only articles
     * within that category will be returned. If a limit is given, only
     * that many articles will be returned.
     *
     * @param int|null $limit Maximum number of articles to return.
     * @param string|null $categorySlug Category slug to restrict to.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRandomArticles(?int $limit = null, ?string $categorySlug = null)
    {
        $query = Article::with(['user', 'category'])
            ->published()
            ->when($categorySlug, fn($q) => $q->withCategorySlug($categorySlug))
            ->inRandomOrder();

        return $limit ? $query->take($limit)->get() : $query->limit($this->defaultPerPage)->get();
    }

    /**
     * Get up to 5 articles: featured ones ordered by latest,
     * and if not enough, fill the rest with random non-featured articles.
     *
     * @param \Illuminate\Support\Collection $articles
     * @return \Illuminate\Support\Collection
     */
    public function getFeaturedArticles(int $total = 5)
    {
        // Get featured articles directly from the database
        $cacheKey = $this->buildCacheKey('featured_posts', ['total' => $total]);

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheTtl), function () use ($total) {
            $featured = Article::with(['user', 'category'])
                ->published()
                ->where('is_featured', true)
                ->orderByDesc('published_at')
                ->take($total)
                ->get();

            if ($featured->count() < $total) {
                $remainingCount = $total - $featured->count();

                $nonFeatured = Article::with(['user', 'category'])
                    ->published()
                    ->where('is_featured', false)
                    ->whereNotIn('id', $featured->pluck('id'))
                    ->inRandomOrder()
                    ->take($remainingCount)
                    ->get();

                $featured = $featured->concat($nonFeatured);
            }

            return $featured;
        });
    }

    /**
     * Modify an array of articles to add excerpt and cover image.
     *
     * If an article does not have an excerpt, it will be generated from the content.
     * If an article does not have a cover image, a placeholder image will be used.
     * If an article does not have a category, it will be set to "Uncategorized".
     *
     * @param \Illuminate\Support\Collection $articles
     * @return \Illuminate\Support\Collection
     */
    public function articlesMappingArray($articles)
    {
        $callback = function ($article) {
            $placeholder = asset("assets/img/image-placeholder.png");
            if (empty($article->excerpt)) {
                $article->excerpt = strip_tags((string)$article->content);
            }
            $article->excerpt = Str::limit($article->excerpt, 160);
            if (!empty($article->cover) && !filter_var($article->cover, FILTER_VALIDATE_URL)) {
                $coverPath = "storage/media/img/" . basename($article->cover);
                $article->cover = file_exists(public_path($coverPath)) ? asset($coverPath) : $placeholder;
            } elseif (empty($article->cover)) {
                $article->cover = $placeholder;
            }
            if (empty($article->cover_large) || !filter_var($article->cover_large, FILTER_VALIDATE_URL)) {
                $article->cover_large = $article->cover;
            }
            if (empty($article->category_id)) {
                $article->category_id = "Uncategorized";
            }
            return $article;
        };

        if ($articles instanceof LengthAwarePaginator || $articles instanceof Paginator) {
            $articles->getCollection()->transform($callback);
            return $articles;
        }

        if ($articles instanceof Collection) {
            return $articles->map($callback);
        }

        return $articles;
    }

    /**
     * Build a consistent cache key for article related queries.
     */
    protected function buildCacheKey(string $prefix, array $parameters = []): string
    {
        ksort($parameters);

        return sprintf('%s:%s', $prefix, md5(json_encode($parameters)));
    }
}
