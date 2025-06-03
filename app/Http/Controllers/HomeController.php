<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\WebSetting;
use Illuminate\Http\Request;
use App\Services\ArticleService;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index(Request $request)
    {
        $data = [
            'title' => env('APP_NAME')
        ];

        $allSettings = WebSetting::getAllSettings();
        $sectionsContent = [];
        $homepageSectionKeys = [
            'home_feature_section',
            'home_section_1',
            'home_section_2',
            'home_section_3',
            'home_sidebar_1',
            'home_sidebar_2',
            'home_bottom_section_1',
        ];

        foreach ($homepageSectionKeys as $sectionKey) {
            $config = $allSettings[$sectionKey] ?? null;

            // Process if the basic configuration for this section exists
            if ($config) {
                $itemsKey = $config['items'] ?? null;
                $total = (int)($config['total'] ?? 3); // Default number of items
                $label = $config['label'] ?? $this->getDefaultLabelForKey($sectionKey); // Get the label
                $isVisible = $config['is_visible'] ?? false; // Get the visibility status

                $dataForSection = collect(); // Initialize the data as an empty collection

                // Only run the query to retrieve data if the section is visible
                if ($isVisible) {
                    $dataForSection = $this->articleService->articlesMappingArray($this->fetchLayoutSectionData($itemsKey, $total));
                }
                // Always add section information to $sectionsContent
                // The view will use $config['is_visible'] to decide how to display it
                $sectionsContent[$sectionKey] = [
                    'label' => $label,
                    'itemsKey' => $itemsKey,
                    'data' => $dataForSection, // Will be an empty collection if is_visible is false
                    'config' => $config,       // Contains the original 'is_visible' flag and other config
                ];
            }
        }

        return view('pages.front.indexHome', compact('data', 'sectionsContent'));
    }

    /**
     * Retrieves data for a section based on itemsKey and total items.
     * This method will make extensive use of your ArticleService.
     */
    private function fetchLayoutSectionData(?string $itemsKey, int $total)
    {
        if (empty($itemsKey)) {
            return collect(); // Return an empty collection if there is no key
        }

        $parts = explode(':', $itemsKey, 2);
        $type = $parts[0];
        $identifier = $parts[1] ?? null;

        $articles = null; // Inisialisasi

        switch ($type) {
            case 'recent-posts':
                $articles = $this->articleService->fetchArticles(['per_page' => $total]);
                break;
            case 'featured-posts':
                $articles = $this->articleService->getFeaturedArticles($this->articleService->fetchArticles(), $total);
                break;
            case 'popular-posts':
                $articles = $this->articleService->getPopularPosts($total);
                break;
            case 'random-posts':
                $articles = $this->articleService->getRandomArticles($total);
                break;
            case 'categories':
                if ($identifier) {
                    $articles = $this->articleService->fetchArticles([
                        'category' => $identifier,
                        'per_page' => $total
                    ]);
                }
                break;
            case 'tags':
                if ($identifier) {
                    $articles = $this->articleService->fetchArticles([
                        'tag' => $identifier,
                        'per_page' => $total
                    ]);
                }
                break;
            case 'all-tags-widget':
                // This does not return articles, but a collection of tags.
                return Tag::inRandomOrder()->limit(10)->get(); // Adjust this query
            default:
                Log::warning("Unknown itemsKey type in fetchLayoutSectionData: {$itemsKey}");
                return collect();
        }

        // Call mapping if articles are retrieved and mapping is required
        if ($articles instanceof \Illuminate\Support\Collection && $articles->isNotEmpty()) {
            $this->articleService->articlesMappingArray($articles); // Call if this method modifies the collection directly (by reference)
            // or if it returns a new collection:
            // $articles = $this->articleService->articlesMappingArray($articles);
        }

        return $articles ?? collect(); // Return the collection of articles or an empty collection
    }
}
