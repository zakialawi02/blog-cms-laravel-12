<?php

namespace App\Http\Controllers;

use App\Models\WebSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Rules\ValidScriptContentRule;
use Illuminate\Support\Facades\Storage;

class WebSettingController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'title' => 'Web Setting',
            // data web_setting sudah di ambil di app/boot middleware
        ];

        return view('pages.dashboard.web.setting', compact('data'));
    }

    /**
     * Update the web settings with the provided request data.
     *
     * Validates and updates various web settings fields including web name, description,
     * keywords, email, social media links, and optionally uploads new app logo and favicon images.
     *
     * @param Request $request The request containing the update data.
     * @return \Illuminate\Http\RedirectResponse Redirects back with a success message upon successful update.
     */

    public function update(Request $request)
    {
        $request->validate(
            [
                'web_name' => 'nullable|string|max:50',
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
                'before_close_head' => ['nullable', 'string', new ValidScriptContentRule([
                    'script',
                    'noscript',
                    'ins',
                    'style',
                    'meta',
                    'link',
                ]),],
                'before_close_body' => ['nullable', 'string',  new ValidScriptContentRule([
                    'script',
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
                ]),],
            ],
            [
                'favicon.dimensions' => 'The favicon should be 512x512 pixels or less.',
                'favicon.mimes' => 'The favicon must be a PNG image.',
                'app_logo.mimes' => 'The App Logo must be a PNG image.',
            ]
        );

        DB::beginTransaction(); // Start a transaction for atomicity

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
                if ($request->has($key)) { // Update if key is present in request (even if value is null)
                    WebSetting::setSetting($key, $request->input($key), 'string');
                }
            }

            // Handle specific boolean/derived settings
            // For 'can_join_contributor', typically a checkbox sends 'on' when checked, or is not present if unchecked.
            $canJoinContributorValue = $request->has('can_join_contributor') && $request->input('can_join_contributor') === 'on' ? 1 : 0;
            WebSetting::setSetting('can_join_contributor', $canJoinContributorValue, 'boolean'); // Store as boolean or integer

            // Handle 'web_name_variant'
            if ($request->has('web_name_variant')) {
                $variantValue = '1'; // Default
                if ($request->input('web_name_variant') == 'vars2') {
                    $variantValue = '2';
                } elseif ($request->input('web_name_variant') == 'vars3') {
                    $variantValue = '3';
                }
                WebSetting::setSetting('web_name_variant', $variantValue, 'string'); // Or 'integer'
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
                $file->move(public_path('assets/app_logo'), $newLogoFileName); // Ensure this path is correct and writable
                WebSetting::setSetting('app_logo', $newLogoFileName, 'string');
            }

            // Handle 'favicon' file upload
            if ($request->hasFile('favicon')) {
                $file = $request->file('favicon');
                $oldFaviconFileName = WebSetting::getSetting('favicon');

                // Delete old favicon if it exists and is not a default placeholder
                // Assuming favicons are also in 'assets/app_logo/' based on your original code structure for deletion.
                // Adjust 'assets/app_logo/' if favicons have a different storage subfolder.
                if ($oldFaviconFileName && !in_array(basename($oldFaviconFileName), ['favicon.png'])) {
                    $oldFaviconPath = public_path('assets/app_logo/' . basename($oldFaviconFileName));
                    if (Storage::exists($oldFaviconPath)) {
                        Storage::delete($oldFaviconPath);
                    }
                }

                $timestamp = time();
                $newFaviconFileName = "favicon_{$timestamp}." . $file->getClientOriginalExtension();
                // Ensure this path is correct for favicons
                $file->move(public_path('assets/app_logo'), $newFaviconFileName);
                WebSetting::setSetting('favicon', $newFaviconFileName, 'string');
            }

            DB::commit(); // All settings saved successfully, commit the transaction
            return redirect()->back()->with('success', 'Settings updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack(); // Something went wrong, rollback changes
            Log::error('Failed to update web settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update settings. Please try again. ' . $e->getMessage());
        }
    }
}
