<?php

namespace App\Http\Controllers;

use App\Enums\WishListEnum;
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

    public function show(Request $request, Product $product)
    {
        $product->load(['categories', 'images']);
        $gallery = [
            $product->thumbnailUrl,
            ...$product->images->map(fn (Image $image) => $image->url),
        ];
        $attributes = $product->optionsWithAttributes();
        $attributeKey = $attributes->keys()->first();
        $attributes = $attributes?->first();

        $quantity = $product->quantity;
        $price = $product->finalPrice();
        $selectedOption = $request->get('option');

        if ($selectedOption && $attributes) {
            $option = $attributes->where('id', $selectedOption)->first();
            $quantity = $option->pivot->quantity;
            $price = $product->finalPrice($option->pivot->price);
        }

        $wishes = [
            'in_stock' => auth()->check() ? auth()->user()->isWishedProduct($product, WishListEnum::In_Stock) : false,
            'price' => auth()->check() ? auth()->user()->isWishedProduct($product, WishListEnum::Price) : false,
        ];

        return view(
            'products.show',
            compact(
                'product',
                'gallery',
                'attributes',
                'attributeKey',
                'quantity',
                'price',
                'selectedOption',
                'wishes'
            )
        );
    }
}
