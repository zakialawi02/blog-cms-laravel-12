<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Str;

class ArticleService
{
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

        return $query->paginate($filters['per_page'] ?? 9)->withQueryString();
    }

    /**
     * Get popular articles sorted by total views.
     */
    public function getPopularPosts(int $limit = null)
    {
        return Article::has('articleViews')
            ->withCount(['articleViews as total_views'])
            ->with(['user', 'category'])
            ->published()
            ->orderByDesc('total_views')
            ->when($limit, fn($q) => $q->take($limit)->get(), fn($q) => $q->paginate(9)->withQueryString());
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
    public function getRandomArticles(int $limit = null, string $categorySlug = null)
    {
        return Article::with(['user', 'category'])
            ->published()
            ->when($categorySlug, fn($q) => $q->withCategorySlug($categorySlug))
            ->inRandomOrder()
            ->when($limit, fn($q) => $q->take($limit)->get());
    }

    /**
     * Get up to 5 articles: featured ones ordered by latest,
     * and if not enough, fill the rest with random non-featured articles.
     *
     * @param \Illuminate\Support\Collection $articles
     * @return \Illuminate\Support\Collection
     */
    public function getFeaturedArticles($articles, int $total = 5)
    {
        $featured = $articles
            ->filter(fn($article) => $article->is_featured)
            ->sortByDesc('published_at')
            ->take(5);
        if ($featured->count() < $total) {
            $remainingCount = $total - $featured->count();
            $nonFeatured = $articles
                ->reject(fn($article) => $article->is_featured)->shuffle()
                ->take($remainingCount);
            $featured = $featured->concat($nonFeatured);
        }
        return $featured;
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
        return $articles->map(function ($article) {
            if (empty($article->excerpt)) {
                $article->excerpt = strip_tags($article->content);
            }
            $article->excerpt = Str::limit($article->excerpt, 200);
            if (!empty($article->cover)) {
                if (filter_var($article->cover, FILTER_VALIDATE_URL)) {
                    $article->cover = $article->cover;
                } else {
                    $article->cover = asset("storage/drive/" . $article->user->username . "/img/" . $article->cover);
                }
            } else {
                $article->cover = asset("assets/img/image-placeholder.png");
            }
            if (empty($article->category_id)) {
                $article->category_id = "Uncategorized";
            }
            return $article;
        });
    }
}
