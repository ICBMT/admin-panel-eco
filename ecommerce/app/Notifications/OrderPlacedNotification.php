<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderPlacedNotification extends Notification implements ShouldQueue
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
        $mail = (new MailMessage)
            ->subject('Order #' . $this->order->id . ' Confirmed')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your order has been placed successfully.')
            ->line('Order Number: #' . $this->order->id);

        foreach ($this->order->items as $item) {
            $mail->line(
                $item->product->name .
                ' x ' .
                $item->quantity .
                ' — ' .
                $item->price
            );
        }

        return $mail
            ->line('Total: ' . $this->order->total)
            ->line('Status: ' . $this->order->status->value)
            ->action('View Your Orders', url('/orders'))
            ->line('Thank you for shopping with us!');
    }
}
