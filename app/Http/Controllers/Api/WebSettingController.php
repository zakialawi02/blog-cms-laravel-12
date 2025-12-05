<?php

namespace App\Http\Controllers\Api;

use App\Models\WebSetting;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\WebSettingResource;
use App\Http\Requests\Api\WebSettingRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WebSettingController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = WebSetting::all();

        return response()->json([
            'success' => true,
            'data' => WebSettingResource::collection($settings),
        ]);
    }

    public function store(WebSettingRequest $request): JsonResponse
    {
        $setting = WebSetting::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Setting created',
            'data' => new WebSettingResource($setting),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        try {
            $setting = WebSetting::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new WebSettingResource($setting),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found',
            ], 404);
        }
    }

    public function update(WebSettingRequest $request, string $id): JsonResponse
    {
        try {
            $setting = WebSetting::findOrFail($id);
            $setting->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Setting updated',
                'data' => new WebSettingResource($setting),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found',
            ], 404);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $setting = WebSetting::findOrFail($id);
            $setting->delete();

            return response()->json([
                'success' => true,
                'message' => 'Setting deleted',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found',
            ], 404);
        }
    }
}
