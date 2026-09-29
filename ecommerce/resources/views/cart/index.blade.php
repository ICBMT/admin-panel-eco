<x-layouts::app :title="__('Shopping Cart')">

    <div class="space-y-8">

        {{-- Header --}}
        <div>
            <p class="text-sm font-medium text-gray-500">
                Shopping Cart
            </p>

            <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">
                Your Cart
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                Review your items before placing your order.
            </p>
        </div>


        {{-- Messages --}}
        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif


        @if (count($cart))

            <div class="grid gap-8 lg:grid-cols-[1fr_360px]">

                {{-- Cart Items --}}
                <div class="space-y-4">

                    @foreach ($cart as $id => $item)

                        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">

                            <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

                                {{-- Image --}}
                                <div class="h-24 w-24 shrink-0 overflow-hidden rounded-xl bg-gray-100">

                                    @if (!empty($item['image_path']))

                                        <img
                                            src="{{ asset('storage/' . $item['image_path']) }}"
                                            alt="{{ $item['name'] }}"
                                            class="h-full w-full object-cover"
                                        >

                                    @else

                                        <div class="flex h-full items-center justify-center text-xs text-gray-400">
                                            No Image
                                        </div>

                                    @endif

                                </div>


                                {{-- Product --}}
                                <div class="min-w-0 flex-1">

                                    <h2 class="font-semibold text-gray-900">
                                        {{ $item['name'] }}
                                    </h2>

                                    <p class="mt-1 text-sm text-gray-500">
                                        ${{ number_format($item['price'], 2) }} each
                                    </p>

                                    <p class="mt-2 font-semibold text-gray-900">
                                        ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                    </p>

                                </div>


                                {{-- Quantity --}}
                                <form
                                    action="{{ route('cart.update', $id) }}"
                                    method="POST"
                                    class="flex items-center gap-2"
                                >
                                    @csrf
                                    @method('PUT')

                                    <input
                                        type="number"
                                        name="quantity"
                                        value="{{ $item['quantity'] }}"
                                        min="1"
                                        class="w-20 rounded-lg border border-gray-200 px-3 py-2 text-center text-sm outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-900/10"
                                    >

                                    <button
                                        type="submit"
                                        class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                                    >
                                        Update
                                    </button>

                                </form>


                                {{-- Remove --}}
                                <form
                                    action="{{ route('cart.remove', $id) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="text-sm font-medium text-red-500 transition hover:text-red-700"
                                    >
                                        Remove
                                    </button>
                                </form>

                            </div>

                        </div>

                    @endforeach

                </div>


                {{-- Summary --}}
                @php
                    $total = 0;

                    foreach ($cart as $item) {
                        $total += $item['price'] * $item['quantity'];
                    }
                @endphp

                <div class="h-fit rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

                    <h2 class="text-lg font-semibold text-gray-900">
                        Order Summary
                    </h2>

                    <div class="mt-6 space-y-4 border-b border-gray-100 pb-6">

                        <div class="flex justify-between text-sm">

                            <span class="text-gray-500">
                                Items
                            </span>

                            <span class="font-medium text-gray-900">
                                {{ collect($cart)->sum('quantity') }}
                            </span>

                        </div>

                        <div class="flex justify-between text-sm">

                            <span class="text-gray-500">
                                Subtotal
                            </span>

                            <span class="font-medium text-gray-900">
                                ${{ number_format($total, 2) }}
                            </span>

                        </div>

                        <div class="flex justify-between text-sm">

                            <span class="text-gray-500">
                                Shipping
                            </span>

                            <span class="font-medium text-green-600">
                                Free
                            </span>

                        </div>

                    </div>


                    <div class="flex items-center justify-between py-6">

                        <span class="font-semibold text-gray-900">
                            Total
                        </span>

                        <span class="text-2xl font-bold text-gray-900">
                            ${{ number_format($total, 2) }}
                        </span>

                    </div>


                    @auth

                        <form
                            action="{{ route('checkout') }}"
                            method="POST"
                            class="space-y-4"
                        >
                            @csrf

                            <div>
                                <p class="text-sm font-semibold text-gray-900">
                                    Payment Method
                                </p>

                                <div class="mt-3 space-y-3">

                                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-4 transition hover:border-gray-400">
                                        <input
                                            type="radio"
                                            name="payment_method"
                                            value="cod"
                                            checked
                                            class="mt-1"
                                        >

                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">
                                                Cash on Delivery
                                            </p>

                                            <p class="mt-1 text-xs text-gray-500">
                                                Pay when your order arrives.
                                            </p>
                                        </div>
                                    </label>


                                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-4 transition hover:border-gray-400">
                                        <input
                                            type="radio"
                                            name="payment_method"
                                            value="stripe"
                                            class="mt-1"
                                        >

                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">
                                                Card Payment
                                            </p>

                                            <p class="mt-1 text-xs text-gray-500">
                                                Pay securely through Stripe.
                                            </p>
                                        </div>
                                    </label>

                                </div>
                            </div>


                            <button
                                type="submit"
                                class="w-full rounded-xl bg-gray-900 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-gray-800"
                            >
                                Proceed to Checkout
                            </button>

                        </form>

                    @else

                        <a
                            href="{{ route('login') }}"
                            class="block w-full rounded-xl bg-gray-900 px-6 py-3.5 text-center text-sm font-semibold text-white transition hover:bg-gray-800"
                        >
                            Login to Checkout
                        </a>

                    @endauth



                    <a
                        href="{{ route('products.index') }}"
                        class="mt-3 block text-center text-sm font-medium text-gray-500 transition hover:text-gray-900"
                    >
                        Continue Shopping
                    </a>

                </div>

            </div>

        @else

            {{-- Empty Cart --}}
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-20 text-center">

                <div class="mx-auto max-w-md">

                    <h2 class="text-xl font-semibold text-gray-900">
                        Your cart is empty
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-gray-500">
                        You haven't added anything to your cart yet.
                        Browse our products and find something you like.
                    </p>

                    <a
                        href="{{ route('products.index') }}"
                        class="mt-6 inline-flex rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800"
                    >
                        Browse Products
                    </a>

                </div>

            </div>

        @endif

    </div>

</x-layouts::app>
