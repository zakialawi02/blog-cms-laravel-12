<?php

namespace App\Http\Controllers;

use App\Models\WebSetting;
use Illuminate\Http\Request;
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
                'app_logo' => 'nullable|image|mimes:png|max:700',
                'favicon' => [
                    'nullable',
                    'image',
                    'mimes:png',
                    'max:548',
                    'dimensions:max_width=512,max_height=512',
                ],
                'link_fb' => 'nullable|url',
                'link_ig' => 'nullable|url',
                'link_tiktok' => 'nullable|url',
                'link_youtube' => 'nullable|url',
                'link_twitter' => 'nullable|url',
                'link_linkedin' => 'nullable|url',
                'link_github' => 'nullable|url',
            ],
            [
                'favicon.dimensions' => 'The favicon should be 512x512 pixels or less.',
            ]
        );

        $data = WebSetting::first();
        $settings = WebSetting::find($data->id);
        $request->merge(['id' => $data->id]);

        $settings->fill($request->except(['app_logo', 'favicon']));

        if ($request->hasFile('app_logo')) {
            if ($settings->app_logo && !in_array(basename($settings->app_logo), ['app_logo.png'])) {
                Storage::delete(public_path('assets/app_logo/' . basename($settings->app_logo)));
            }
            $timestamp = time();
            $fileName = "app_logo_{$timestamp}." . $request->file('app_logo')->getClientOriginalExtension();
            $request->file('app_logo')->move(public_path('assets/app_logo'), $fileName);
            $settings->app_logo =  $fileName;
        }

        if ($request->hasFile('favicon')) {
            if ($settings->favicon && !in_array(basename($settings->favicon), ['favicon.png'])) {
                Storage::delete(public_path('assets/app_logo/' . basename($settings->favicon)));
            }
            $timestamp = time();
            $fileName = "favicon_{$timestamp}." . $request->file('favicon')->getClientOriginalExtension();
            $request->file('favicon')->move(public_path('assets/app_logo'), $fileName);
            $settings->favicon = $fileName;
        }

        $result = $settings->save();
        if (!$result) {
            return redirect()->back()->with('error', 'Failed to update settings.');
        }
        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
