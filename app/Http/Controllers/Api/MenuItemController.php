<?php

namespace App\Http\Controllers\Api;

use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\MenuItemResource;
use App\Http\Requests\Api\MenuItemRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MenuItemController extends Controller
{
    public function store(MenuItemRequest $request): JsonResponse
    {
        $menuItem = MenuItem::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Menu item created',
            'data' => new MenuItemResource($menuItem),
        ], 201);
    }

    public function update(MenuItemRequest $request, string $id): JsonResponse
    {
        try {
            $menuItem = MenuItem::findOrFail($id);
            $menuItem->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Menu item updated',
                'data' => new MenuItemResource($menuItem),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Menu item not found',
            ], 404);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $menuItem = MenuItem::findOrFail($id);
            $menuItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Menu item deleted',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Menu item not found',
            ], 404);
        }
    }
}
