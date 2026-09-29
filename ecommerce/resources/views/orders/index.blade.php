<x-layouts::app :title="__('My Orders')">

    <div class="space-y-8">

        {{-- Header --}}
        <div>
            <p class="text-sm font-medium text-gray-500">
                Account
            </p>

            <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">
                My Orders
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                View your order history and track your purchases.
            </p>
        </div>


        {{-- Success --}}
        @if (session('success'))

            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>

        @endif


        {{-- Orders --}}
        <div class="space-y-4">

            @forelse ($orders as $order)

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                        {{-- Order Information --}}
                        <div>

                            <div class="flex flex-wrap items-center gap-3">

                                <h2 class="text-lg font-semibold text-gray-900">
                                    Order #{{ $order->id }}
                                </h2>


                                {{-- Status --}}
                                @php
                                    $statusClasses = match ($order->status->value) {
                                        'pending' => 'bg-yellow-50 text-yellow-700',
                                        'processing' => 'bg-blue-50 text-blue-700',
                                        'shipped' => 'bg-purple-50 text-purple-700',
                                        'delivered' => 'bg-green-50 text-green-700',
                                        default => 'bg-gray-50 text-gray-700',
                                    };
                                @endphp

                                <span
                                    class="rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses }}"
                                >
                                    {{ $order->status->name }}
                                </span>

                            </div>


                            <p class="mt-2 text-sm text-gray-500">
                                Placed {{ $order->created_at->format('d M Y') }}
                            </p>

                        </div>
                        <div>
                            <p class="text-xs text-gray-500">
                                Payment Method
                            </p>

                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $order->payment_method === 'stripe' ? 'Card Payment' : 'Cash on Delivery' }}
                            </p>
                        </div>



                        {{-- Total --}}
                        <div class="sm:text-right">

                            <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                                Total
                            </p>

                            <p class="mt-1 text-xl font-bold text-gray-900">
                                ${{ number_format($order->total, 2) }}
                            </p>

                        </div>

                    </div>


                    {{-- Action --}}
                    <div class="mt-6 border-t border-gray-100 pt-5">

                        <a
                            href="{{ route('orders.show', $order) }}"
                            class="inline-flex rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                        >
                            View Order
                        </a>

                    </div>

                </div>

            @empty

                <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-20 text-center">

                    <h2 class="text-xl font-semibold text-gray-900">
                        No orders yet
                    </h2>

                    <p class="mt-2 text-sm text-gray-500">
                        Once you place an order, it will appear here.
                    </p>

                    <a
                        href="{{ route('products.index') }}"
                        class="mt-6 inline-flex rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800"
                    >
                        Start Shopping
                    </a>

                </div>

            @endforelse

        </div>


        {{-- Pagination --}}
        @if ($orders->hasPages())

            <div>
                {{ $orders->links() }}
            </div>

        @endif

    </div>

</x-layouts::app>
