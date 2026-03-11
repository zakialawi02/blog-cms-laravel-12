<?php

namespace App\Http\Controllers\Api;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Http\Resources\MenuItemResource;
use App\Http\Requests\MenuRequest;
use App\Http\Requests\MenuItemSyncRequest;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MenuController extends Controller
{
    /**
     * Display a listing of the menus.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $menus = Menu::all();

            return MenuResource::collection($menus)->additional([
                'success' => true,
                'message' => 'List of all menus',
            ])->response()->setStatusCode(200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created menu in storage.
     */
    public function store(MenuRequest $request): JsonResponse
    {
        try {
            $menu = Menu::create($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Menu created successfully.',
                'data' => new MenuResource($menu),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified menu with nested items.
     */
    public function show($id): JsonResponse
    {
        try {
            // Load only top-level items, children are loaded recursively by the model relation
            $menu = Menu::with(['items' => function ($query) {
                $query->whereNull('parent')->orderBy('sort', 'asc');
            }])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Menu details retrieved successfully',
                'data' => new MenuResource($menu)
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the menu by its location.
     */
    public function showByLocation($location): JsonResponse
    {
        try {
            $menu = Menu::with(['items' => function ($query) {
                $query->whereNull('parent')->orderBy('sort', 'asc');
            }])->where('location', $location)->firstOrFail();
            
            return response()->json([
                'success' => true,
                'message' => 'Menu details for location retrieved successfully',
                'data' => new MenuResource($menu)
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Menu with location ' . $location . ' not found'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified menu in storage.
     */
    public function update(MenuRequest $request, $id): JsonResponse
    {
        try {
            $menu = Menu::findOrFail($id);
            $menu->update($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Menu updated successfully.',
                'data' => new MenuResource($menu),
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found.',
                'error' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified menu from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $menu = Menu::findOrFail($id);
            // Items should cascade delete if set in DB, but we can do it manually to be safe
            $menu->items()->delete();
            $menu->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Menu deleted successfully.',
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found.',
                'error' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Sync nested structure of menu items to the menu.
     */
    public function syncItems(MenuItemSyncRequest $request, $id): JsonResponse
    {
        try {
            $menu = Menu::findOrFail($id);
            $items = $request->validated()['items'];
            
            $keepIds = [];
            $this->processItems($items, $menu->id, null, $keepIds);
            
            // Delete items that are no longer in the provided tree
            MenuItem::where('menu', $menu->id)
                ->whereNotIn('id', $keepIds)
                ->delete();

            // Load updated tree
            $menu->load(['items' => function ($query) {
                $query->whereNull('parent')->orderBy('sort', 'asc');
            }]);

            return response()->json([
                'success' => true,
                'message' => 'Menu items synced successfully.',
                'data' => new MenuResource($menu),
            ], Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found.',
                'error' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Recursively process nested items.
     */
    protected function processItems(array $items, $menuId, $parentId = null, &$keepIds = [])
    {
        foreach ($items as $index => $itemData) {
            if (empty($itemData['label'])) {
                continue;
            }

            $item = null;
            if (isset($itemData['id'])) {
                $item = MenuItem::where('menu', $menuId)->find($itemData['id']);
            }
            
            if (!$item) {
                $item = new MenuItem();
                $item->menu = $menuId;
            }
            
            $item->label = $itemData['label'];
            $item->link = $itemData['link'] ?? null;
            $item->class = $itemData['class'] ?? null;
            $item->parent = $parentId;
            $item->sort = $index;
            $item->depth = $itemData['depth'] ?? 0;
            $item->save();
            
            $keepIds[] = $item->id;
            
            if (isset($itemData['children']) && is_array($itemData['children'])) {
                $this->processItems($itemData['children'], $menuId, $item->id, $keepIds);
            }
        }
    }
}
