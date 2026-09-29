<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Notifications\OrderPlacedNotification;
use Illuminate\Support\Facades\Log;

class LogOrderPlaced
{
    public function handle(OrderPlaced $event): void
    {
        Log::info('Order placed', [
            'order_id' => $event->order->id,
            'user_id' => $event->order->user_id,
            'total' => $event->order->total,
        ]);

        $event->order->load('items.product');

        $event->order->user->notify(
            new OrderPlacedNotification($event->order)
        );
    }
}
