<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    public function index(): JsonResponse
    {
        $menus = Menu::with('items.children')->get(['id', 'name', 'location']);

        return response()->json([
            'success' => true,
            'message' => 'List of menus',
            'data' => $menus,
        ]);
    }
}
