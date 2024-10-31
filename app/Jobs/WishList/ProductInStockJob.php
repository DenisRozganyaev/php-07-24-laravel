<?php

namespace App\Jobs\WishList;

use App\Enums\WishListEnum;
use App\Notifications\WishList\ProductInStockNotification;

class ProductInStockJob extends BaseJob
{
    public function handle(): void
    {
        $this->sendNotification(ProductInStockNotification::class, WishListEnum::In_Stock);
    }
}
