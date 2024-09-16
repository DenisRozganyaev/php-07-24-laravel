<?php

use Illuminate\Support\Facades\Route;

Route::get('/', \App\Http\Controllers\HomeController::class)->name('home');

Auth::routes();

Route::resource('products', \App\Http\Controllers\ProductsController::class)
    ->only(['index', 'show']);

Route::name('admin.')->prefix('admin')->middleware('role:admin|moderator')->group(function() {
    Route::get('/', \App\Http\Controllers\Admin\DashboardController::class)->name('dashboard'); // admin.dashboard
    Route::resource('categories', \App\Http\Controllers\Admin\CategoriesController::class)
        ->except(['show']);
    Route::resource('products', \App\Http\Controllers\Admin\ProductsController::class)
        ->except(['show']);
});

Route::name('ajax.')->prefix('ajax')->group(function() {
    Route::middleware(['auth', 'role:admin|moderator'])->group(function() {
        Route::delete('images/{image}', \App\Http\Controllers\Ajax\RemoveImageController::class)->name('images.remove');
    });
});
