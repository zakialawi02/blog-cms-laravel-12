<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display products for frontend shop page
     */
    public function shop()
    {
        $data = [
            'title' => 'Shop',
            'web_setting' => \App\Models\WebSetting::first()
        ];

        // Get published products with pagination
        $products = Product::with(['user', 'productImages'])
            ->where('is_published', true)
            ->where('stock', '>', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('pages.front.shop.index', compact('data', 'products'));
    }

    /**
     * Display the specified product for frontend
     */
    public function show(Product $product)
    {
        // Check if product is published
        if (!$product->is_published) {
            abort(404);
        }

        $data = [
            'title' => $product->product_name,
        ];

        // Load relationships
        $product->load(['user', 'productImages', 'productFiles']);

        // Get related products (same category or similar)
        $relatedProducts = Product::with(['user', 'productImages'])
            ->where('is_published', true)
            ->where('stock', '>', 0)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('pages.front.shop.show', compact('data', 'product', 'relatedProducts'));
    }

    public function index()
    {
        $data = [
            'title' => 'Manage Products'
        ];

        $products = Product::with('user')->paginate(10);

        // Calculate statistics
        $totalProducts = Product::count();
        $inStockProducts = Product::where('stock', '>', 0)->count();
        $lowStockProducts = Product::where('stock', '>', 0)->where('stock', '<=', 5)->count();
        $outOfStockProducts = Product::where('stock', 0)->count();

        $stats = [
            'total' => $totalProducts,
            'in_stock' => $inStockProducts,
            'low_stock' => $lowStockProducts,
            'out_of_stock' => $outOfStockProducts
        ];

        return view('pages.dashboard.products.index', compact('data', 'products', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'title' => 'Create New Product'
        ];

        $currencies = [
            'USD' => 'US Dollar ($)',
            'EUR' => 'Euro (€)',
            'IDR' => 'Indonesian Rupiah (Rp)',
            'GBP' => 'British Pound (£)',
            'JPY' => 'Japanese Yen (¥)',
        ];

        return view('pages.dashboard.products.create', compact('data', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['id'] = Str::uuid();

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('products', $filename, 'public');
            $data['thumbnail'] = $filename;
        }

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }
}
