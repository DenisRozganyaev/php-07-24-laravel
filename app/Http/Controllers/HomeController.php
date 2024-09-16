<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function __invoke()
    {
        $categories = Category::orderBy('id')->take(5)->get();
        $products = Product::orderBy('id')->take(8)->get();

        return view('home', compact('categories', 'products'));
    }
}
