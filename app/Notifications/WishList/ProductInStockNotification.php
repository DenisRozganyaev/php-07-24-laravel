<?php

namespace App\Notifications\WishList;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductInStockNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Product $product)
    {
        $this->onQueue('wishlist-notifications');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $user): MailMessage
    {
        return (new MailMessage)
            ->line("Hey, $user->name $user->lastname")
            ->line('Product '.$this->product->title.' from your wish list is available!')
            ->line('Hurry up!')
            ->action('Visit product page', url(route('products.show', $this->product)))
            ->line('Thank you for using our application!');
    }
}
