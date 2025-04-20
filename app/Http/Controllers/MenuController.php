<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class MenuController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Menus'
        ];
        $menus = Menu::with('items')->get();

        return view('pages.dashboard.menus.index', compact('data', 'menus'));
    }

    public function getMenus()
    {
        try {
            $menus = Menu::withCount('items')->get();
            return response()->json($menus);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function getMenuItems($id)
    {
        try {
            $menu = Menu::findOrFail($id);
            $items = MenuItem::where('menu', $menu->id)->orderBy('sort', 'asc')->get();

            return response()->json([
                'success' => true,
                'message' => 'Menu items fetched successfully',
                'menu' => $menu,
                'items' => $items
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Menu not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred'], 500);
        }
    }

    public function createMenu(Request $request)
    {
        Cache::forget('nav_menus');
        try {
            $request->validate([
                'name' => 'required|string|unique:menus,name|max:50',
                'location' => 'required|string|max:50|unique:menus,location',
            ]);

            $menu = Menu::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Menu created',
                'menu' => $menu
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred'], 500);
        }
    }

    public function destroy($id)
    {
        Cache::forget('nav_menus');
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu not found.'], 404);
        }

        $menu->delete();

        return response()->json(['message' => 'Menu deleted successfully.']);
    }

    public function storeMenuItem(Request $request)
    {
        Cache::forget('nav_menus');
        try {
            $request->validate([
                'menu' => 'required|exists:menus,id',
                'label' => 'required',
                'link' => 'required',
            ]);

            $menu = Menu::findOrFail($request->menu);

            $item = MenuItem::create([
                'menu' => $request->menu,
                'label' => $request->label,
                'link' => $request->link,
                'parent' => null,
                'sort' => MenuItem::where('menu', $request->menu)->max('sort') + 1,
                'class' => '',
                'depth' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item created',
                'location' => $menu->location,
                'item' => $item
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred'], 500);
        }
    }

    public function updateMenuStructure(Request $request)
    {
        Cache::forget('nav_menus');

        $structure = $request->structure;
        foreach ($structure as &$item) {
            if ((int)$item['parent'] === 0) {
                $item['parent'] = null;
            }
        }

        // Pastikan ada data struktur
        if (empty($structure)) {
            return response()->json(['success' => false, 'message' => 'No structure data received.'], 400);
        }

        // Mendapatkan menu berdasarkan item pertama
        $menu = Menu::whereHas('items', fn($q) => $q->where('id', $structure[0]['id']))->first();

        if (!$menu) {
            return response()->json(['success' => false, 'message' => 'Menu not found.'], 404);
        }

        // Tentukan apakah menu bertipe 'header'
        $isHeader = $menu->location === 'header';

        // Update menu items
        try {
            DB::transaction(function () use ($structure, $isHeader) {
                foreach ($structure as $item) {
                    MenuItem::where('id', $item['id'])->update([
                        'sort'   => $item['sort'],
                        'parent' => $isHeader ? $item['parent'] : null,
                        'depth'  => $isHeader ? $item['depth'] : 0,
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Menu order updated successfully' . (!$isHeader ? ' (nested structure ignored for non-header location)' : ''),
                'location' => $menu->location
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while updating menu order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteItem($itemId)
    {
        Cache::forget('nav_menus');
        $item = MenuItem::find($itemId);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found.'
            ], 404);
        }

        try {
            // Menghapus item menu
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error occurred while deleting menu item.'], 500);
        }
    }
}
