<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Menu;
use App\Models\WebSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class LoadWebSettings
{
    public function handle(Request $request, Closure $next)
    {
        // Skip jika route diawali dengan `/api/` atau jika request adalah AJAX
        if ($request->is('api/*') || $request->ajax()) {
            // Skip web_setting jika API atau AJAX
            $webSetting = null;
        } else {
            // Ambil data WebSetting dari cache, kalau tidak ada baru query
            $webSetting = Cache::remember('web_setting', now()->addDays(30), function () {
                return WebSetting::getAllSettings();
            });
        }

        // Ambil semua menu beserta item dan child-nya, lalu susun berdasarkan location
        if (!$request->is('api/*') && !$request->is('dashboard/*') && !$request->ajax()) {
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
        } else {
            $navMenus = null;
        }

        // Composer untuk semua view
        View::composer('*', function ($view) use ($webSetting, $navMenus) {
            // Ambil data yang sudah dikirim ke view (jika ada)
            $existingData = $view->getData()['data'] ?? [];
            // Merge dengan web_setting dan nav_menus
            $mergedData = array_merge($existingData, [
                'web_setting' => $webSetting,
                'menu' => $navMenus
            ]);
            // Kirim ke view
            $view->with('data', $mergedData);
        });

        return $next($request);
    }
}
