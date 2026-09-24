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
            ->published();

        $random = filter_var($filters['random'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($random) {
            $query->inRandomOrder();
        } else {
            $sort = in_array($filters['sort'] ?? null, ['published_at', 'created_at', 'updated_at', 'title'], true) ? $filters['sort'] : 'published_at';
            $direction = strtolower((string) ($filters['direction'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sort, $direction);
        }

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

        if (isset($filters['is_featured']) && $filters['is_featured'] !== '') {
            $query->where('is_featured', filter_var($filters['is_featured'], FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = $filters['per_page'] ?? 9;
        $perPage = is_numeric($perPage) ? max(1, min((int) $perPage, 100)) : 9;

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get popular articles sorted by total views.
     */
    public function getPopularPosts(?int $limit = null)
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
    public function getRandomArticles(?int $limit = null, ?string $categorySlug = null)
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
    public function getFeaturedArticles(int $total = 5)
    {
        // Get featured articles directly from the database
        $featured = Article::with(['user', 'category'])
            ->published()
            ->where('is_featured', true)
            ->orderByDesc('published_at')
            ->take($total)
            ->get();

        // If not enough, get more random articles
        if ($featured->count() < $total) {
            $remainingCount = $total - $featured->count();

            // Exclude already fetched featured articles
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
    }

    /**
     * Resolve publishing status/data based on requested action and user role.
     * Extracted from PostController::resolvePublishingData for reuse by API.
     */
    public function resolvePublishingData(string $action, ?Article $post, ?string $role, array &$data): void
    {
        $isPrivileged = in_array($role, ['superadmin', 'admin'], true);

        if ($action === 'draft') {
            $data['status'] = 'draft';
            $data['published_at'] = ($post && $post->status === 'published' && $post->published_at)
                ? $post->published_at
                : null;
            return;
        }

        if ($isPrivileged) {
            $data['status'] = 'published';
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
            $placeholder = asset("assets/img/image-placeholder.png");
            if (empty($article->excerpt)) {
                $article->excerpt = strip_tags((string) $article->content);
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
        });
    }
}
