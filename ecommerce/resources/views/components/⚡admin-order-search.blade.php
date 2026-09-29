<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Enums\OrderStatus;

new class extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $orders = Order::with('user')
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('id', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($query) {
                            $query->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->latest()
            ->paginate(10);

        return $this->view([
            'orders' => $orders,
            'statuses' => OrderStatus::cases(),
        ]);
    }
};
?>

<div class="space-y-6">

{{-- Search & Filter --}}
<div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">

    <div class="grid gap-4 md:grid-cols-[1fr_220px]">

        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">
                Search orders
            </label>

            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by order ID, customer name or email..."
                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
            >
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">
                Status
            </label>

            <select
                wire:model.live="status"
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
            >
                <option value="">All statuses</option>

                @foreach ($statuses as $orderStatus)
                    <option value="{{ $orderStatus->value }}">
                        {{ $orderStatus->name }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>

</div>


{{-- Loading --}}
<div
    wire:loading
    class="text-sm text-gray-500"
>
    Searching orders...
</div>


{{-- Orders --}}
<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

    <div class="overflow-x-auto">

        <table class="w-full text-left text-sm">

            <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase tracking-wide text-gray-500">

                <tr>
                    <th class="px-6 py-4">Order</th>
                    <th class="px-6 py-4">Customer</th>
                    <th class="px-6 py-4">Total</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">Payment</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>

            </thead>

            <tbody class="divide-y divide-gray-100">

                @forelse ($orders as $order)

                    <tr class="transition hover:bg-gray-50">

                        <td class="px-6 py-5 font-semibold text-gray-900">
                            #{{ $order->id }}
                        </td>

                        <td class="px-6 py-5">

                            <div class="font-medium text-gray-900">
                                {{ $order->user->name }}
                            </div>

                            <div class="mt-1 text-xs text-gray-500">
                                {{ $order->user->email }}
                            </div>

                        </td>

                        <td class="px-6 py-5 font-medium text-gray-900">
                            ${{ number_format($order->total, 2) }}
                        </td>

                        <td class="px-6 py-5">

                            @php
                                $statusClasses = match ($order->status->value) {
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'processing' => 'bg-blue-100 text-blue-700',
                                    'shipped' => 'bg-purple-100 text-purple-700',
                                    'delivered' => 'bg-green-100 text-green-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp

                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">
                                {{ $order->status->name }}
                            </span>

                        </td>

                        <td class="px-6 py-5 text-gray-500">
                            {{ $order->created_at->format('M d, Y') }}
                        </td>

                        <td class="px-6 py-5 text-gray-500">
                            <div>
                                <p class="text-xs text-gray-500">
                                    Payment Method
                                </p>

                                <p class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ $order->payment_method === 'stripe' ? 'Card Payment' : 'Cash on Delivery' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500">
                                    Payment Status
                                </p>

                                <p class="mt-1 text-sm font-semibold capitalize text-gray-900">
                                    {{ $order->payment_status }}
                                </p>
                            </div>
                        </td>

                        <td class="px-6 py-5 text-right">

                            <a
                                href="{{ route('admin.orders.show', $order) }}"
                                class="font-semibold text-gray-900 transition hover:text-gray-500"
                            >
                                View
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">

                            <p class="font-medium text-gray-900">
                                No orders found
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                Try changing your search or status filter.
                            </p>

                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>


{{-- Pagination --}}
@if ($orders->hasPages())
    <div>
        {{ $orders->links() }}
    </div>
@endif

</div>
