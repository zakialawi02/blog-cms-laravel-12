<?php

namespace App\Services;

use App\Models\Tag;
use App\Models\Menu;
use App\Models\Category;
use App\Models\WebSetting;
use App\Enums\LayoutSection;
use App\Services\ArticleService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SectionContentService
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * Retrieves data for all sections on the page layout content, including featured
     * sections, recent posts, popular posts, categories, tags, and ads.
     * This method will make extensive use of your ArticleService.
     *
     * @return array An associative array with section keys as keys, and section data
     *               as values. The section data is an array with the following keys:
     *               - label: string
     *               - itemsKey: string
     *               - data: \Illuminate\Support\Collection of \App\Models\Article
     *               - config: array of config values for the section
     */
    public function getSectionData(): array
    {
        $cacheKey = 'section_content:' . app()->getLocale();

        return Cache::remember($cacheKey, now()->addMinutes(10), function () {
            $allSettings = WebSetting::getAllSettings();
            $sectionsContent = [];

            foreach (LayoutSection::values() as $sectionKey) {
                $config = $allSettings[$sectionKey] ?? null;

                if ($config) {
                    $itemsKey = $config['items'] ?? null;
                    $total = (int)($config['total'] ?? 3);
                    $label = $config['label'] ?? $this->getDefaultLabelForKey($sectionKey);
                    $isVisible = $config['is_visible'] ?? false;

                    $dataForSection = collect();

                    if ($isVisible) {
                        $dataForSection = $this->articleService->articlesMappingArray(
                            $this->getLayoutSectionData($itemsKey, $total)
                        );
                    }

                    $sectionsContent[$sectionKey] = [
                        'label' => $label,
                        'itemsKey' => $itemsKey,
                        'data' => $dataForSection,
                        'config' => $config,
                    ];
                }
            }

            return $sectionsContent;
        });
    }

    /**
     * Retrieves data for a section based on itemsKey and total items.
     * This method will make extensive use of your ArticleService.
     */
    private function getLayoutSectionData(?string $itemsKey, int $total)
    {
        $parts = explode(':', $itemsKey, 2);
        $type = $parts[0];
        $identifier = $parts[1] ?? null;

        $cacheKey = $this->buildSectionCacheKey($itemsKey, $total);

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($type, $identifier, $total) {
            switch ($type) {
                case 'recent-posts':
                    return $this->articleService->fetchArticles([
                        'per_page' => $total,
                        'page' => 1,
                    ])->getCollection();
                case 'featured-posts':
                    return $this->articleService->getFeaturedArticles($total);
                case 'popular-posts':
                    $popular = $this->articleService->getPopularPosts($total);
                    return $popular instanceof \Illuminate\Pagination\LengthAwarePaginator
                        ? $popular->getCollection()
                        : $popular;
                case 'random-posts':
                    return $this->articleService->getRandomArticles($total);
                case 'categories':
                    if ($identifier) {
                        return $this->articleService->fetchArticles([
                            'category' => $identifier,
                            'per_page' => $total,
                            'page' => 1,
                        ])->getCollection();
                    }
                    return collect();
                case 'tags':
                    if ($identifier) {
                        return $this->articleService->fetchArticles([
                            'tag' => $identifier,
                            'per_page' => $total,
                            'page' => 1,
                        ])->getCollection();
                    }
                    return collect();
                case 'all-tags-widget':
                    return Tag::inRandomOrder()->limit($total ?? 10)->get();
                case 'all-categories-widget':
                    return Category::inRandomOrder()->limit($total ?? 5)->get();
                case 'js-script':
                    return collect();
                default:
                    Log::warning("Unknown itemsKey type in getLayoutSectionData: {$itemsKey}");
                    return collect();
            }
        });
    }

    /**
     * Retrieves navigation menu data from the cache or database.
     *
     * This method fetches navigation menus with their items and any child items,
     * ordered by their sort order. The results are cached for 30 days to improve
     * performance. The navigation menus are keyed by their location.
     *
     * @return array An associative array of navigation menus keyed by location.
     */
    public function getNavigationData(): array
    {
        $navMenus = Cache::remember('nav_menus', now()->addDays(30), function () {
            return Menu::with([
                'items' => function ($q) {
                    $q->where('parent', null)->orderBy('sort')
                        ->with(['children' => function ($c) {
                            $c->orderBy('sort');
                        }]);
                }
            ])->get()->keyBy('location')->toArray(); // keyBy location
        });

        return $navMenus;
    }

    protected function buildSectionCacheKey(?string $itemsKey, int $total): string
    {
        return sprintf('section:%s:%s', $itemsKey ?? 'none', $total);
    }
}
