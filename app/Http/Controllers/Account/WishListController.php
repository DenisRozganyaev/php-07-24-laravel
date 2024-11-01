<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;

class WishListController extends Controller
{
    public function __invoke(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
    {
        $wishes = auth()->user()->wishes()->orderBy('product_id')->paginate(5);

        return view('account.wishlist', compact('wishes'));
    }
}
