<?php

namespace App\Http\Controllers;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __invoke()
    {
        if (Cart::instance('cart')->content()->isEmpty()) {
            notify()->warning('Your cart is empty, you are can not proceed to checkout');
            return redirect()->route('home');
        }

        $cart = Cart::instance('cart');
        $user = auth()?->user();

        return view('checkout/index', compact('cart', 'user'));
    }
}
