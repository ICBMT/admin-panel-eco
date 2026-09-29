<x-layouts::app :title="'Order #'.$order->id">

<div class="mx-auto max-w-6xl space-y-8">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <a
                href="{{ route('admin.orders.index') }}"
                class="text-sm font-medium text-gray-500 transition hover:text-gray-900"
            >
                ← Back to Orders
            </a>

            <div class="mt-3">
                <p class="text-sm font-medium text-gray-500">Order</p>

                <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                    #{{ $order->id }}
                </h1>
            </div>
        </div>

        @php
            $statusClasses = match ($order->status->value) {
                'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-200',
                'processing' => 'bg-blue-50 text-blue-700 ring-blue-200',
                'shipped' => 'bg-purple-50 text-purple-700 ring-purple-200',
                'delivered' => 'bg-green-50 text-green-700 ring-green-200',
                'cancelled' => 'bg-red-50 text-red-700 ring-red-200',
                default => 'bg-gray-50 text-gray-700 ring-gray-200',
            };
        @endphp

        <span
            class="inline-flex w-fit rounded-full px-4 py-2 text-sm font-semibold ring-1 {{ $statusClasses }}"
        >
            {{ $order->status->name }}
        </span>

    </div>

    {{-- Order information --}}
    <div class="grid gap-4 md:grid-cols-3">

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Customer</p>

            <p class="mt-2 font-semibold text-gray-900">
                {{ $order->user->name }}
            </p>

            <p class="mt-1 text-sm text-gray-500">
                {{ $order->user->email }}
            </p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Placed On</p>

            <p class="mt-2 font-semibold text-gray-900">
                {{ $order->created_at->format('d M Y, h:i A') }}
            </p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Order Total</p>

            <p class="mt-2 text-xl font-bold text-gray-900">
                ${{ number_format($order->total, 2) }}
            </p>
        </div>

    </div>

    <div class="grid gap-8 lg:grid-cols-[1fr_320px]">

        {{-- Order Items --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">

            <div class="border-b border-gray-200 px-6 py-5">
                <h2 class="text-lg font-semibold text-gray-900">
                    Order Items
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Products included in this order.
                </p>
            </div>

            <div class="divide-y divide-gray-100">

                @foreach ($order->items as $item)

                    <div class="flex flex-col gap-5 px-6 py-6 sm:flex-row sm:items-center">

                        {{-- Image --}}
                        <div class="h-24 w-24 shrink-0 overflow-hidden rounded-xl bg-gray-100">

                            @if ($item->product->image_path)

                                <img
                                    src="{{ asset('storage/'.$item->product->image_path) }}"
                                    alt="{{ $item->product->name }}"
                                    class="h-full w-full object-cover"
                                >

                            @else

                                <div class="flex h-full items-center justify-center text-xs text-gray-400">
                                    No image
                                </div>

                            @endif

                        </div>

                        {{-- Product --}}
                        <div class="min-w-0 flex-1">

                            <h3 class="font-semibold text-gray-900">
                                {{ $item->product->name }}
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                ${{ number_format($item->price, 2) }} each
                            </p>

                            <p class="mt-2 text-sm text-gray-500">
                                Quantity: {{ $item->quantity }}
                            </p>

                        </div>

                        {{-- Subtotal --}}
                        <div class="sm:text-right">

                            <p class="text-sm text-gray-500">
                                Subtotal
                            </p>

                            <p class="mt-1 text-lg font-bold text-gray-900">
                                ${{ number_format($item->price * $item->quantity, 2) }}
                            </p>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

        {{-- Right Sidebar --}}
        <div class="space-y-6">

            {{-- Status --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

                <h2 class="text-lg font-semibold text-gray-900">
                    Update Status
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Change the current order status.
                </p>

                <form
                    action="{{ route('admin.orders.update-status', $order) }}"
                    method="POST"
                    class="mt-5 space-y-4"
                >
                    @csrf
                    @method('PUT')

                    <select
                        name="status"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-gray-500 focus:ring-2 focus:ring-gray-200"
                    >
                        @foreach ($order->status::cases() as $status)

                            <option
                                value="{{ $status->value }}"
                                @selected($order->status === $status)
                            >
                                {{ $status->name }}
                            </option>

                        @endforeach
                    </select>

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                    >
                        Update Status
                    </button>

                </form>

            </div>

            {{-- Status History --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

                <h2 class="text-lg font-semibold text-gray-900">
                    Status History
                </h2>

                <div class="mt-5 space-y-4">

                    @forelse ($order->statusHistory->sortByDesc('created_at') as $history)

                        <div class="border-l-2 border-gray-200 pl-4">

                            <p class="font-semibold text-gray-900">
                                {{ $history->status->name }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                Changed:
                                {{ $history->created_at->format('d M Y, h:i A') }}
                            </p>

                        </div>

                    @empty

                        <p class="text-sm text-gray-500">
                            No status history available.
                        </p>

                    @endforelse

                </div>

            </div>

            {{-- Summary --}}
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6">

                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Items</span>

                    <span class="font-medium text-gray-900">
                        {{ $order->items->sum('quantity') }}
                    </span>
                </div>

                <div class="mt-3 flex justify-between text-sm">
                    <span class="text-gray-500">Shipping</span>

                    <span class="font-medium text-green-600">
                        Free
                    </span>
                </div>

                <div class="mt-4 border-t border-gray-200 pt-4">

                    <div class="flex items-center justify-between">

                        <span class="font-semibold text-gray-900">
                            Total
                        </span>

                        <span class="text-xl font-bold text-gray-900">
                            ${{ number_format($order->total, 2) }}
                        </span>

                    </div>

                </div>

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

            </div>

        </div>

    </div>

</div>

</x-layouts::app>
