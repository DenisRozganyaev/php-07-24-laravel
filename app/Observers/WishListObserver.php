<?php

namespace App\Observers;

use App\Jobs\WishList\PriceUpdatedJob;
use App\Jobs\WishList\ProductInStockJob;
use App\Models\Product;

class WishListObserver
{
    public function updated(Product $product): void
    {
        if ($product->price < $product->getOriginal('price') || $product->discount > $product->getOriginal('discount')) {
            PriceUpdatedJob::dispatch($product);
        }

        if ($product->is_in_stock && ! $product->getOriginal('is_in_stock')) {
            ProductInStockJob::dispatch($product);
        }
    }
}
