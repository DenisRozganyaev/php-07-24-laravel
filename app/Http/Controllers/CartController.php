<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::instance('cart');

        return view('cart/index', compact('cart'));
    }

    public function add(Request $request, Product $product)
    {
        $product->load(['options']);
        $data = $request->validate([
            'option' => ['nullable', 'exists:attribute_options,id'],
        ]);
        $cartArgs = [
            'id' => $product->id,
            'name' => $product->title,
            'qty' => 1,
            'price' => $product->finalPrice(),
            'weight' => 0,
        ];

        if ($data['option'] && $option = $product->options->where('id', $data['option'])->first()) {
            $cartArgs['price'] = $product->finalPrice($option->pivot->price);
            $cartArgs['options'] = [
                $option->attribute->name => $option->value,
            ];
        }

        Cart::instance('cart')
            ->add($cartArgs)
            ->associate(Product::class);

        notify()->success('Product was added to the cart');

        return redirect()->back();
    }

    public function update(Request $request, Product $product)
    {
        $product->load(['options']);
        $data = $request->validate([
            'rowId' => ['required', 'string'],
            'qty' => ['required', 'numeric', 'min:1'],
        ]);

        $option = Cart::instance('cart')->get($data['rowId'])?->options?->first();
        $maxQuantity = $product->options->where('value', $option)?->first()?->pivot?->quantity;
    }

    public function delete(Request $request)
    {

    }
}
