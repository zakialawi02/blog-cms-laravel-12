<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WebSetting;
use Illuminate\Http\JsonResponse;

class WebSettingController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $settings = WebSetting::select(
            'id',
            'site_name',
            'site_logo',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'contact_email',
            'contact_phone',
            'social_media'
        )->first();

        return response()->json([
            'success' => true,
            'message' => 'Web settings',
            'data' => $settings,
        ]);
    }
}
