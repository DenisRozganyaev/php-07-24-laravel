<?php

namespace App\Http\Controllers;

use App\Models\Attributes\Option;
use App\Models\Image;
use App\Models\Product;
use App\Repositories\Contracts\ProductsRepositoryContract;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request, ProductsRepositoryContract $repository)
    {
        $products = $repository->paginate($request);
        $attributes = Option::filter(
            $request->has('options') ? $products->pluck('id')->toArray() : []
        );
        $selectedAttrs = $request->input('options', []);
        $per_page = $request->input('per_page', 10);

        return view('products.index', compact('products', 'attributes', 'selectedAttrs', 'per_page'));
    }

    public function show(Product $product)
    {
        $product->load(['categories', 'images']);
        $gallery = [
            $product->thumbnailUrl,
            ...$product->images->map(fn (Image $image) => $image->url)
        ];
        return view('products.show', compact('product', 'gallery'));
    }
}
