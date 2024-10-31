<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Product;

class ProductsController extends Controller
{
    public function index()
    {

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
