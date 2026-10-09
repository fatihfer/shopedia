<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category')->latest();

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categorySlug = $request->string('category')->toString()) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::withCount('products')->orderBy('name')->get();

        // Data khusus homepage (disembunyikan saat filter/pencarian aktif)
        $isBrowsing = ! $request->filled('q') && ! $request->filled('category') && ! $request->filled('page');
        $showHero = ! $request->filled('q') && ! $request->filled('category');
        $featured = $isBrowsing
            ? Product::with('category')->where('stock', '>', 0)->inRandomOrder()->take(8)->get()
            : collect();

        $stats = [
            'products' => Product::count(),
            'categories' => Category::count(),
        ];

        return view('products.index', compact('products', 'categories', 'featured', 'stats', 'isBrowsing', 'showHero'));
    }

    public function show(Product $product): View
    {
        $product->load('category');

        $related = Product::with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'related'));
    }
}
