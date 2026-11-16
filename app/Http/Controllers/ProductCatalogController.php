<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->active()
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = $request->string('q')->toString();
                $query->where(function ($sub) use ($keyword) {
                    // Pencarian nama dicocokkan dari awal kata (prefix) agar lebih spesifik
                    $sub->where('name', 'like', "{$keyword}%")
                        // Pencarian SKU tetap fleksibel di bagian mana saja
                        ->orWhere('sku', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->integer('category')))
            ->when($request->filled('min_price'), fn ($q) => $q->where('price', '>=', $request->input('min_price')))
            ->when($request->filled('max_price'), fn ($q) => $q->where('price', '<=', $request->input('max_price')))
            ->when($request->boolean('ready') || $request->boolean('in_stock'), fn ($q) => $q->where('stock', '>', 0));

        match ($request->input('sort')) {
            'price_asc' => $products->orderBy('price'),
            'price_desc' => $products->orderByDesc('price'),
            'name' => $products->orderBy('name'),
            default => $products->latest(),
        };

        return view('shop.index', [
            'products' => $products->paginate(12)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);
        $product->load('category');

        return view('shop.show', compact('product'));
    }
}