<?php

namespace App\Http\Controllers;

use App\Enums\WishListEnum;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WishListController extends Controller
{
    public function add(Request $request, Product $product)
    {
        $data = $request->validate([
            'type' => Rule::enum(WishListEnum::class),
        ]);

        auth()->user()->addToWish($product, WishListEnum::from($data['type']));

        notify()->success('Product added to wishlist');

        return redirect()->back();
    }

    public function remove(Request $request, Product $product)
    {
        $data = $request->validate([
            'type' => Rule::enum(WishListEnum::class),
        ]);

        auth()->user()->removeFromWish($product, WishListEnum::from($data['type']));

        notify()->success('Product removed from wishlist');

        return redirect()->back();
    }
}
