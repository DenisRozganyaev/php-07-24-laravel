<?php

namespace App\Jobs\WishList;

use App\Enums\WishListEnum;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

abstract class BaseJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Product $product)
    {
        $this->onQueue('wishlist');
    }

    /**
     * Execute the job.
     */
    abstract public function handle(): void;

    protected function sendNotification(string $notificationClass, WishListEnum $type): void
    {
        $this->product
            ->followers()
            ->wherePivot($type->value,  true)
            ->chunk( // 1500 -> 3 iterates 500 500 500
                500,
                fn (Collection $users) => Notification::send(
                    $users,
                    app($notificationClass, [
                        'product' => $this->product,
                    ])
                )
            );
    }
}
