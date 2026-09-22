<?php

use App\Enums\LayoutSection;
use App\Models\WebSetting;
use App\Services\SectionContentService;
use Illuminate\Support\Facades\Cache;

/**
 * Regression test untuk issue Sentry BLOG-CMS-LARAVEL-3B:
 *   Undefined array key "home_feature_section"
 *   (View: resources/views/pages/front/indexHome.blade.php)
 *
 * Akar masalah:
 *   SectionContentService::getSectionData() hanya mengisi kunci section kalau
 *   baris web_settings-nya ada, sedangkan view mengakses kunci itu dengan pola
 *   `$sectionsContent['x']['config']['is_visible'] == '1' ?? false`.
 *   Operator `??` ada di posisi salah: akses array terjadi lebih dulu, jadi
 *   begitu kuncinya tidak ada -> "Undefined array key" -> fatal 500 halaman depan.
 */
beforeEach(function () {
    WebSetting::query()->delete();
    Cache::forget('web_setting');
});

it('mengisi kunci untuk semua LayoutSection walau web_settings kosong', function () {
    $sections = app(SectionContentService::class)->getSectionData();

    foreach (LayoutSection::values() as $key) {
        expect(array_key_exists($key, $sections))
            ->toBeTrue("kunci section '{$key}' hilang dari \$sectionsContent");
        expect($sections[$key]['label'])->toBeString()->not->toBeEmpty();
        expect($sections[$key]['data'])->toBeInstanceOf(\Illuminate\Support\Collection::class);
    }
});

it('menandai section sebagai tidak terlihat saat konfigurasinya tidak ada', function () {
    $sections = app(SectionContentService::class)->getSectionData();

    foreach (LayoutSection::values() as $key) {
        expect((bool) ($sections[$key]['config']['is_visible'] ?? false))
            ->toBeFalse("section '{$key}' seharusnya dianggap tidak terlihat");
    }
});

it('halaman depan tetap 200 walau data section tidak lengkap', function () {
    $this->get('/')->assertOk();
});
