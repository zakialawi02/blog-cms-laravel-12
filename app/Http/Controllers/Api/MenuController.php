<?php

namespace App\Http\Controllers\Api;

use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Http\Requests\Api\MenuRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MenuController extends Controller
{
    public function index(): JsonResponse
    {
        $menus = Menu::with('items.children')->get();

        return response()->json([
            'success' => true,
            'data' => MenuResource::collection($menus),
        ]);
    }

    public function store(MenuRequest $request): JsonResponse
    {
        $menu = Menu::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Menu created',
            'data' => new MenuResource($menu),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        try {
            $menu = Menu::with('items.children')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new MenuResource($menu),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found',
            ], 404);
        }
    }

    public function update(MenuRequest $request, string $id): JsonResponse
    {
        try {
            $menu = Menu::findOrFail($id);
            $menu->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Menu updated',
                'data' => new MenuResource($menu),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found',
            ], 404);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $menu = Menu::findOrFail($id);
            $menu->delete();

            return response()->json([
                'success' => true,
                'message' => 'Menu deleted',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found',
            ], 404);
        }
    }
}
