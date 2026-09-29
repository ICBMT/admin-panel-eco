<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Enums\OrderStatus;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total',
        'status',
        'payment_method',
        'payment_status',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
        ];
    }
}
