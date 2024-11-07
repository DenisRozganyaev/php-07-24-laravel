<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\User;
use App\Services\Contracts\InvoicesServiceContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use NotificationChannels\Telegram\TelegramMessage;

class OrderCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Order $order)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(User $user): array
    {
        return $user?->telegram_id ? ['telegram', 'mail'] : ['mail'];
    }

    public function viaQueues(): array
    {
        return [
            'mail' => 'admin-mail',
            'telegram' => 'admin-telegram',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $user): MailMessage
    {
        logs()->info('notify admin by email');

        $url = route('admin.dashboard');

        return (new MailMessage)
            ->subject('New Order')
            ->line("Hello $user->name $user->lastname")
            ->line('A new order here')
            ->line('')
            ->line('Total: '.$this->order->total.' '.config('paypal.currency'))
            ->action('Visit admin dashboard', $url);
    }

    public function toTelegram(User $user)
    {
        logs()->info('notify admin by telegram');

        $url = route('admin.dashboard');

        logs()->info('TOKEN => '.config('services.telegram-bot-api.token'));

        return TelegramMessage::create()
            ->to($user->telegram_id)
            ->content("Hello $user->name $user->lastname")
            ->line('A new order here')
            ->line('')
            ->line('Total: '.$this->order->total.' '.config('paypal.currency'))
            ->button('Visit admin dashboard', $url);
    }
}
