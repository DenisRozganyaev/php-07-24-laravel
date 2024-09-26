<?php

use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::get('/', \App\Http\Controllers\HomeController::class)->name('home');

Auth::routes();

Route::resource('products', \App\Http\Controllers\ProductsController::class)
    ->only(['index', 'show']);

Route::name('cart.')->prefix('cart')->group(function() {
   Route::get('/', [\App\Http\Controllers\CartController::class, 'index'])->name('index');
   Route::post('{product}', [\App\Http\Controllers\CartController::class, 'add'])->name('add');
   Route::delete('/', [\App\Http\Controllers\CartController::class, 'remove'])->name('remove');
   Route::put('{product}', [\App\Http\Controllers\CartController::class, 'update'])->name('update');
});

Route::get('checkout', CheckoutController::class)->name('checkout');

Route::name('admin.')->prefix('admin')->middleware('role:admin|moderator')->group(function() {
    Route::get('/', \App\Http\Controllers\Admin\DashboardController::class)->name('dashboard'); // admin.dashboard
    Route::resource('categories', \App\Http\Controllers\Admin\CategoriesController::class)
        ->except(['show']);
    Route::resource('attributes', \App\Http\Controllers\Admin\AttributesController::class)
        ->except(['show']);
    Route::resource('products', \App\Http\Controllers\Admin\ProductsController::class)
        ->except(['show']);
});

Route::name('ajax.')->prefix('ajax')->group(function() {
    Route::middleware(['auth', 'role:admin|moderator'])->group(function() {
        Route::delete('images/{image}', \App\Http\Controllers\Ajax\RemoveImageController::class)->name('images.remove');
    });
});
