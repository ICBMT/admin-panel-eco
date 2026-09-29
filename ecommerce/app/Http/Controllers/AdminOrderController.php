<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Enums\OrderStatus;
use Illuminate\Validation\Rule;
use App\Events\OrderStatusChanged;


class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(10);
        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => OrderStatus::cases(),
        ]);
    }
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', Rule::enum(OrderStatus::class)],
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        if (
            $order->payment_method === 'cod' &&
            $request->status === OrderStatus::Delivered->value
        ) {
            $order->update([
                'payment_status' => 'paid',
            ]);
        }

        $order->statusHistory()->create([
            'status' => $request->status,
        ]);

        event(new OrderStatusChanged($order));

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order status updated successfully.');
    }


    public function show(Order $order)
    {
        $order->load('user', 'items.product', 'statusHistory');

        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }
}
