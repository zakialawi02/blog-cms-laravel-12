<?php

namespace App\Http\Controllers\Api;

use App\Models\WebSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\WebSettingResource;
use App\Rules\ValidScriptContentRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WebSettingController extends Controller
{
    /**
     * Display a listing of all web settings.
     */
    public function index(): JsonResponse
    {
        try {
            $settings = WebSetting::all();
            
            return WebSettingResource::collection($settings)->additional([
                'success' => true,
                'message' => 'Web settings retrieved successfully',
            ])->response()->setStatusCode(Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve web settings',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update web settings.
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate(
            [
                'web_name' => 'nullable|string|max:50',
                'tagline' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
                'keywords' => 'nullable|string|max:255',
                'email' => 'nullable|email',
                'app_logo' => 'nullable|image|mimes:png|max:700', // Max 700KB
                'favicon' => [
                    'nullable',
                    'image',
                    'mimes:png', // Ensure it's a PNG
                    'max:548',   // Max 548KB
                    'dimensions:max_width=512,max_height=512',
                ],
                'link_fb' => 'nullable|url',
                'link_ig' => 'nullable|url',
                'link_tiktok' => 'nullable|url',
                'link_youtube' => 'nullable|url',
                'link_twitter' => 'nullable|url',
                'link_linkedin' => 'nullable|url',
                'link_github' => 'nullable|url',
                'google_analytics' => 'nullable|string|min:8|max:50',
                'google_adsense' => 'nullable|string|min:8|max:50',
                'can_join_contributor' => 'nullable|boolean',
                'web_name_variant' => 'nullable|in:vars1,vars2,vars3',
                'before_close_head' => [
                    'nullable',
                    'string',
                    new ValidScriptContentRule([
                        'script',
                        'amp',
                        'noscript',
                        'ins',
                        'style',
                        'meta',
                        'link',
                    ]),
                ],
                'before_close_body' => [
                    'nullable',
                    'string',
                    new ValidScriptContentRule([
                        'script',
                        'amp',
                        'noscript',
                        'ins',
                        'style',
                        'meta',
                        'link',
                        'div',
                        'a',
                        'img',
                        'iframe',
                        'ul',
                        'ol',
                        'li',
                        'p',
                        'span',
                        'strong',
                        'em',
                        'br',
                        'hr'
                    ]),
                ],
            ],
            [
                'favicon.dimensions' => 'The favicon should be 512x512 pixels or less.',
                'favicon.mimes' => 'The favicon must be a PNG image.',
                'app_logo.mimes' => 'The App Logo must be a PNG image.',
            ]
        );

        DB::beginTransaction();

        try {
            // Define keys for standard string settings that directly map from request
            $stringSettings = [
                'web_name',
                'tagline',
                'description',
                'keywords',
                'email',
                'link_fb',
                'link_ig',
                'link_tiktok',
                'link_youtube',
                'link_twitter',
                'link_linkedin',
                'link_github',
                'google_analytics',
                'google_adsense',
                'before_close_head',
                'before_close_body',
            ];

            foreach ($stringSettings as $key) {
                // Use array_key_exists to check if the key was provided (even if null/empty)
                if (array_key_exists($key, $request->all())) {
                    WebSetting::setSetting($key, $request->input($key), 'string');
                }
            }

            // Handle 'can_join_contributor' boolean setting
            if (array_key_exists('can_join_contributor', $request->all())) {
                $val = $request->input('can_join_contributor');
                $canJoinContributorValue = ($val === true || $val === 1 || $val === '1') ? 1 : 0;
                WebSetting::setSetting('can_join_contributor', $canJoinContributorValue, 'boolean');
            }

            // Handle 'web_name_variant' setting
            if (array_key_exists('web_name_variant', $request->all())) {
                $variantValue = '1'; // Default
                if ($request->input('web_name_variant') === 'vars2') {
                    $variantValue = '2';
                } elseif ($request->input('web_name_variant') === 'vars3') {
                    $variantValue = '3';
                }
                WebSetting::setSetting('web_name_variant', $variantValue, 'string');
            }

            // Handle 'app_logo' file upload
            if ($request->hasFile('app_logo')) {
                $file = $request->file('app_logo');
                $oldLogoFileName = WebSetting::getSetting('app_logo');

                // Delete old logo if it exists and is not a default placeholder
                if ($oldLogoFileName && !in_array(basename($oldLogoFileName), ['app_logo.png'])) {
                    $oldLogoPath = public_path('assets/app_logo/' . basename($oldLogoFileName));
                    if (Storage::exists($oldLogoPath)) {
                        Storage::delete($oldLogoPath);
                    }
                }

                $timestamp = time();
                $newLogoFileName = "app_logo_{$timestamp}." . $file->getClientOriginalExtension();
                $file->move(public_path('assets/app_logo'), $newLogoFileName);
                WebSetting::setSetting('app_logo', $newLogoFileName, 'string');
            }

            // Handle 'favicon' file upload
            if ($request->hasFile('favicon')) {
                $file = $request->file('favicon');
                $oldFaviconFileName = WebSetting::getSetting('favicon');

                // Delete old favicon if it exists and is not a default placeholder
                if ($oldFaviconFileName && !in_array(basename($oldFaviconFileName), ['favicon.png'])) {
                    $oldFaviconPath = public_path('assets/app_logo/' . basename($oldFaviconFileName));
                    if (Storage::exists($oldFaviconPath)) {
                        Storage::delete($oldFaviconPath);
                    }
                }

                $timestamp = time();
                $newFaviconFileName = "favicon_{$timestamp}." . $file->getClientOriginalExtension();
                $file->move(public_path('assets/app_logo'), $newFaviconFileName);
                WebSetting::setSetting('favicon', $newFaviconFileName, 'string');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully',
                'data' => WebSettingResource::collection(WebSetting::all())
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update web settings via API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
