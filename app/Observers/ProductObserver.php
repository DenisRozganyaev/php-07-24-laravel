<?php

namespace App\Observers;

use App\Models\Image;
use App\Models\Product;
use App\Services\Contracts\FileServiceContract;

class ProductObserver
{
    public function deleted(Product $product): void
    {
        app(FileServiceContract::class)->delete($product->thumbnail);
        $product->categories()->detach();
        $product->images()->each(fn (Image $image) => $image->delete());
    }
}
