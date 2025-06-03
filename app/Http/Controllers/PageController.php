<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Category;
use App\Models\WebSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    protected $sectionKeys = [
        'home_feature_section',
        'home_section_1',
        'home_section_2',
        'home_section_3',
        'home_sidebar_1',
        'home_sidebar_2',
        'home_bottom_section_1'
    ];

    public function layout()
    {
        $data = [
            'title' => 'Layout Settings'
        ];

        $layouts = [];

        foreach ($this->sectionKeys as $key) {
            // WebSetting::getSetting($key) will return a PHP array if the type is ‘json’
            // or null if not found.
            $settingValue = WebSetting::getSetting($key);
            // If null, use default data from getDefaultDataForKey
            $layouts[$key] = $settingValue ?? $this->getDefaultDataForKey($key);
        }
        $itemKeyOptions = $this->getContentItemKeyOptions();

        return view('pages.dashboard.pages.settings', compact('data', 'layouts', 'itemKeyOptions'));
    }

    public function layoutUpdate(Request $request)
    {
        Cache::forget('web_setting');
        $validationRules = [];
        $availableItemKeys = array_keys($this->getContentItemKeyOptions()); // Ambil keys untuk validasi

        foreach ($this->sectionKeys as $key) {
            $validationRules["sections_config.{$key}.label"] = 'nullable|string|max:255';
            $validationRules["sections_config.{$key}.is_visible"] = 'nullable|boolean';
            // Perbarui aturan validasi untuk 'items'
            $validationRules["sections_config.{$key}.items"] = ['nullable', Rule::in($availableItemKeys)];
            // Aturan untuk 'total' tetap, akan dikontrol oleh @if di component
            $validationRules["sections_config.{$key}.total"] = 'nullable|integer|min:0';
        }

        $validatedData = $request->validate($validationRules, [
            'sections_config.*.total.integer' => 'The number of items must be a number.',
            'sections_config.*.total.min' => 'The number of items must be at least 0.',
            'sections_config.*.items.in' => 'The selected content item key is invalid.', // Pesan error untuk Rule::in
        ]);

        $submittedConfigs = $validatedData['sections_config'] ?? [];
        // dd($submittedConfigs);
        DB::beginTransaction();
        try {
            foreach ($this->sectionKeys as $key) {
                if (isset($submittedConfigs[$key])) {
                    $config = $submittedConfigs[$key];
                    $valueArray = [
                        'label' => $config['label'] ?? '',
                        'is_visible' => $config['is_visible'] ?? false,
                        'total' => (int)($config['total'] ?? ($this->getDefaultDataForKey($key)['total'] ?? 0)), // Ambil default jika tidak diset
                        'items' => $config['items'] ?? '',
                    ];
                    WebSetting::setSetting($key, $valueArray, 'json');
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'Homepage layout updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update homepage layout: ' . $e->getMessage());
            $errorMessage = 'Failed to update homepage layout.';
            if (app()->environment('local')) {
                $errorMessage .= ' Error: ' . $e->getMessage();
            }
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Get default data structure for a given section key.
     * (Method ini tetap sama)
     */
    function getDefaultDataForKey(string $key): array
    {
        $defaults = [
            'home_feature_section' => ['label' => 'Recent Posts', 'is_visible' => true, 'total' => 6, 'items' => 'recent-posts'],
            'home_section_1' => ['label' => 'Recent Posts', 'is_visible' => true, 'total' => 6, 'items' => 'recent-posts'],
            'home_section_2' => ['label' => 'Technology', 'is_visible' => true, 'total' => 6, 'items' => 'technology-category'],
            // ... (defaults lainnya)
            'home_section_3' => ['label' => '', 'is_visible' => false, 'total' => 3, 'items' => ''],
            'home_sidebar_1' => ['label' => 'Popular Posts', 'is_visible' => true, 'total' => 4, 'items' => 'popular-posts'],
            'home_sidebar_2' => ['label' => 'Tags', 'is_visible' => true, 'total' => 10, 'items' => 'tags'],
            'home_bottom_section_1' => ['label' => 'You Missed', 'is_visible' => true, 'total' => 4, 'items' => 'random-posts'],
        ];
        return $defaults[$key] ?? ['label' => 'Default Label', 'is_visible' => false, 'total' => 0, 'items' => ''];
    }

    /**
     * Define the available options for the content items key dropdown,
     * including static options and options from Category and Tag models.
     *
     * @return array
     */
    protected function getContentItemKeyOptions(): array
    {
        // 1. Basic Static Options
        $options = [
            '' => '-- Select Content Type --',
            'recent-posts' => 'Recent Posts',
            'popular-posts' => 'Popular Posts',
            'random-posts' => 'Random Posts',
            'all-tags-widget' => 'Tags Cloud Widget',
            // You can decide whether 'tags' as a general widget is still relevant
            // or whether users will always choose specific tags.
            // 'all-tags-widget' => 'Tags Cloud Widget (All Tags)',
        ];

        // 2. Take and Add Categories
        try {
            // Assumes Category has ‘slug’ and 'category'. Replace ‘slug’ with ‘id’ if necessary.
            // Ensure slug is unique. If not, ID is safer.
            $categories = Category::orderBy('category')->get();
            foreach ($categories as $category) {
                // Format key: "category:slug_kategori"
                $options["categories:{$category->slug}"] = "Category: {$category->category}";
            }
        } catch (\Exception $e) {
            Log::error("Failed to fetch categories for layout options: " . $e->getMessage());
            // Option: add error message to options if necessary
            // $options['error_categories'] = 'Error loading categories';
        }

        // 3. Take and Add Tags
        try {
            // Assume that tags have a ‘slug’ and ‘tag_name’. Replace ‘slug’ with ‘id’ if necessary.
            $tags = Tag::orderBy('tag_name')->get();
            foreach ($tags as $tag) {
                // Format key: "tag:slug_tag"
                $options["tags:{$tag->slug}"] = "Tag: {$tag->tag_name}";
            }
        } catch (\Exception $e) {
            Log::error("Failed to fetch tags for layout options: " . $e->getMessage());
            // $options['error_tags'] = 'Error loading tags';
        }
        // dd($options);
        return $options;
    }
}
