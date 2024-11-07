<?php

namespace App\Jobs;

use App\Enums\RolesEnum;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class OrderNotificationsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Notification::send(
            User::role(RolesEnum::ADMIN->value)->get(),
            app(
                OrderCreatedNotification::class,
                ['order' => $this->order]
            )
        );
    }
}
