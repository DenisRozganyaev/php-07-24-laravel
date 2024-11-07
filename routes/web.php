<?php

use App\Http\Controllers\Ajax\Payments\PaypalController;
use App\Http\Controllers\CheckoutController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

Route::get('test-job', function () {
    $order = Order::all()?->random();

    if ($order) {
        notify()->success('Run an event');
        \App\Events\OrderCreatedEvent::dispatch($order);
    } else {
        notify()->error('There are no orders yet');
    }

    return redirect()->route('home');
});

Route::get('/', \App\Http\Controllers\HomeController::class)->name('home');

Auth::routes();

Route::resource('products', \App\Http\Controllers\ProductsController::class)
    ->only(['index', 'show']);

Route::name('cart.')->prefix('cart')->group(function () {
    Route::get('/', [\App\Http\Controllers\CartController::class, 'index'])->name('index');
    Route::post('{product}', [\App\Http\Controllers\CartController::class, 'add'])->name('add');
    Route::delete('/', [\App\Http\Controllers\CartController::class, 'remove'])->name('remove');
    Route::put('{product}', [\App\Http\Controllers\CartController::class, 'update'])->name('update');
});

Route::get('checkout', CheckoutController::class)->name('checkout');
Route::get('orders/{vendorOrderId}/thank-you', \App\Http\Controllers\Pages\ThankYouController::class)->name('thankyou');

Route::middleware(['auth'])->group(function () {
    Route::get('invoices/{order}', \App\Http\Controllers\InvoicesController::class)->name('invoice');

    Route::post('wishlist/{product}', [\App\Http\Controllers\WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('wishlist/{product}', [\App\Http\Controllers\WishlistController::class, 'remove'])->name('wishlist.remove');

    Route::name('account.')->prefix('account')->group(function () {
        Route::get('/', [App\Http\Controllers\Account\HomeController::class, 'index'])->name('home');
        Route::get('wishlist', App\Http\Controllers\Account\WishListController::class)->name('wishlist');
    });
});

Route::name('admin.')->prefix('admin')->middleware('role:admin|moderator')->group(function () {
    Route::get('/', \App\Http\Controllers\Admin\DashboardController::class)->name('dashboard'); // admin.dashboard
    Route::resource('categories', \App\Http\Controllers\Admin\CategoriesController::class)
        ->except(['show']);
    Route::resource('attributes', \App\Http\Controllers\Admin\AttributesController::class)
        ->except(['show']);
    Route::resource('products', \App\Http\Controllers\Admin\ProductsController::class)
        ->except(['show']);
});

Route::name('ajax.')->prefix('ajax')->group(function () {
    Route::middleware(['auth', 'role:admin|moderator'])->group(function () {
        Route::delete('images/{image}', \App\Http\Controllers\Ajax\RemoveImageController::class)->name('images.remove');
    });

    Route::prefix('paypal')->name('paypal.')->group(function () {
        Route::post('order', [PaypalController::class, 'create'])->name('order.create');
        Route::post('order/{vendorOrderId}/capture', [PaypalController::class, 'capture'])->name('order.capture');
    });
});

Route::name('callback.')->prefix('callback')->group(function () {
    Route::get('telegram', \App\Http\Controllers\Callbacks\TelegramAuthController::class)
        ->name('telegram')
        ->middleware('role:admin');

});
