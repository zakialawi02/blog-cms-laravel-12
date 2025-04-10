<?php

namespace App\Http\Middleware;

use Closure;
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
            return $next($request);
        }
        // Ambil data WebSetting dari cache, kalau tidak ada baru query
        $webSetting = Cache::remember('web_setting', now()->addDays(30), function () {
            return WebSetting::first();
        });
        // Composer untuk semua view
        View::composer('*', function ($view) use ($webSetting) {
            // Ambil data yang sudah dikirim ke view (jika ada)
            $existingData = $view->getData()['data'] ?? [];
            // Merge dengan web_setting
            $mergedData = array_merge($existingData, ['web_setting' => $webSetting]);
            // Kirim ke view
            $view->with('data', $mergedData);
        });
        return $next($request);
    }
}
