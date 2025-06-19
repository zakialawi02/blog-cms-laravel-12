<?php

namespace Database\Seeders;

use App\Models\WebSetting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WebSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'web_name', 'value' => 'My Blog', 'type' => 'string'],
            ['key' => 'web_name_variant', 'value' => '3', 'type' => 'string'],
            ['key' => 'tagline', 'value' => 'My Blog Tagline Here', 'type' => 'string'],
            ['key' => 'description', 'value' => 'My Blog Description Here for SEO', 'type' => 'string'],
            ['key' => 'keywords', 'value' => 'My Blog, keywords, Laravel, blog, zakialawi, zakialawi.my.id, zakialawi.com', 'type' => 'string'],
            ['key' => 'app_logo', 'value' => 'app_logo.png', 'type' => 'string'],
            ['key' => 'favicon', 'value' => 'favicon.png', 'type' => 'string'],
            ['key' => 'email', 'value' => 'hallo@zakialawi.my.id', 'type' => 'string'],
            ['key' => 'link_fb', 'value' => null, 'type' => 'string'],
            ['key' => 'link_tiktok', 'value' => null, 'type' => 'string'],
            ['key' => 'link_ig', 'value' => null, 'type' => 'string'],
            ['key' => 'link_twitter', 'value' => null, 'type' => 'string'],
            ['key' => 'link_youtube', 'value' => null, 'type' => 'string'],
            ['key' => 'link_linkedin', 'value' => null, 'type' => 'string'],
            ['key' => 'link_github', 'value' => null, 'type' => 'string'],
            ['key' => 'can_join_contributor', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'google_adsense', 'value' => null, 'type' => 'string'],
            ['key' => 'google_analytics', 'value' => null, 'type' => 'string'],
            ['key' => 'home_feature_section', 'value' => '{"label":"Featured Posts","is_visible":true,"total":6,"items":"random-posts"}', 'type' => 'json'],
            ['key' => 'ads_featured', 'value' => '{"label":"","is_visible":false,"total":null,"items":"js-script"}', 'type' => 'json'],
            ['key' => 'home_section_1', 'value' => '{"label":"Recent Posts","is_visible":true,"total":6,"items":"recent-posts"}', 'type' => 'json'],
            ['key' => 'home_section_2', 'value' => '{"label":"Default label","is_visible":false,"total":"6","items":""}', 'type' => 'json'],
            ['key' => 'home_section_3', 'value' => '{"label":"Default label","is_visible":false,"total":"3","items":""}', 'type' => 'json'],
            ['key' => 'home_section_4', 'value' => '{"label":"Default label","is_visible":false,"total":"3","items":""}', 'type' => 'json'],
            ['key' => 'home_section_5', 'value' => '{"label":"Default label","is_visible":false,"total":"3","items":""}', 'type' => 'json'],
            ['key' => 'home_sidebar_1', 'value' => '{"label":"Popular Posts","is_visible":true,"total":4,"items":"popular-posts"}', 'type' => 'json'],
            ['key' => 'home_sidebar_2', 'value' => '{"label":"Tags","is_visible":true,"total":10,"items":"all-tags-widget"}', 'type' => 'json'],
            ['key' => 'home_sidebar_3', 'value' => '{"label":"Default label","is_visible":false,"total":"5","items":""}', 'type' => 'json'],
            ['key' => 'home_sidebar_4', 'value' => '{"label":"Default label","is_visible":false,"total":"5","items":""}', 'type' => 'json'],
            ['key' => 'ads_sidebar_1', 'value' => '{"label":"","is_visible":false,"total":null,"items":"js-script"}', 'type' => 'json'],
            ['key' => 'ads_sidebar_2', 'value' => '{"label":"","is_visible":false,"total":null,"items":"js-script"}', 'type' => 'json'],
            ['key' => 'ads_bottom_1', 'value' => '{"label":"","is_visible":false,"total":null,"items":"js-script"}', 'type' => 'json'],
            ['key' => 'home_bottom_section_1', 'value' => '{"label":"You Missed","is_visible":true,"total":4,"items":"random-posts"}', 'type' => 'json'],
            ['key' => 'ads_bottom_2', 'value' => '{"label":"","is_visible":false,"total":null,"items":"js-script"}', 'type' => 'json'],
            ['key' => 'before_close_head', 'value' => null, 'type' => 'string'],
            ['key' => 'before_close_body', 'value' => null, 'type' => 'string'],
        ];

        // Loop dan buat entri setting
        // Menggunakan Model (Pastikan Model Setting ada dan terhubung ke tabel 'settings')
        foreach ($settings as $setting) {
            WebSetting::create([
                'key' => $setting['key'],
                'value' => $setting['value'],
                'type' => $setting['type'] ?? 'string', // Gunakan default 'string' jika 'type' tidak ada
                'updated_at' => now(),
                'created_at' => now(),
            ]);
        }
    }
}
