<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Order #' . $this->order->id . ' Status Updated')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your order status has been updated.')
            ->line('Order Number: #' . $this->order->id)
            ->line('New Status: ' . $this->order->status->name)
            ->action('View Your Order', route('orders.show', $this->order))
            ->line('Thank you for shopping with us!');
    }
}
