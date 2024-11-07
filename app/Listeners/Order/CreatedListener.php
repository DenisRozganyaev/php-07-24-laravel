<?php

namespace App\Listeners\Order;

use App\Enums\RolesEnum;
use App\Events\OrderCreatedEvent;
use App\Jobs\OrderNotificationsJob;
use App\Models\User;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class CreatedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function viaQueue(): string
    {
        return 'listeners';
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreatedEvent $event): void
    {
        logs()->info('CreatedListener event');
        OrderNotificationsJob::dispatch($event->order)->delay(now()->addMinutes(2));

//        Notification::send(
//            User::role(RolesEnum::ADMIN->value)->get(),
//            app(
//                OrderCreatedNotification::class,
//                ['order' => $event->order]
//            )
//        );
    }
}
