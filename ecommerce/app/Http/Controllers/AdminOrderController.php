<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Enums\OrderStatus;
use Illuminate\Validation\Rule;
use App\Events\OrderStatusChanged;
use Illuminate\View\View;


class AdminOrderController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(OrderStatus::class)],
        ]);

        $search = (string) ($validated['search'] ?? '');
        $status = (string) ($validated['status'] ?? '');

        $orders = Order::with(['user', 'items.product'])
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('id', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function (Builder $query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($status !== '', function (Builder $query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => OrderStatus::cases(),
            'search' => $search,
            'status' => $status,
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

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order status updated successfully.');
    }


    public function show(Order $order): View
    {
        $order->load('user', 'items.product', 'statusHistory');

        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }
}
