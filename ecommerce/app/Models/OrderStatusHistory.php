<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Enums\OrderStatus;

class OrderStatusHistory extends Model
{
    protected $fillable = [
        'order_id',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
        ];
    }
}
