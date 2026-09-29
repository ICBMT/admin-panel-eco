<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use Illuminate\Support\Facades\Log;
use App\Notifications\OrderStatusChangedNotification;

class LogOrderStatusChanged
{
    public function handle(OrderStatusChanged $event): void
    {
        Log::info('Order status changed', [
            'order_id' => $event->order->id,
            'status' => $event->order->status->value,
        ]);
        $event->order->user->notify(
            new OrderStatusChangedNotification($event->order)
        );
    }
}
