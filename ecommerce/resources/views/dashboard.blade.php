<x-layouts::app :title="__('Dashboard')">

    <div class="space-y-8">

        {{-- Welcome --}}
        <div>
            <p class="text-sm font-medium text-gray-500">
                Welcome back
            </p>

            <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">
                {{ auth()->user()->name }}
            </h1>

            <p class="mt-2 text-gray-600">
                Manage your shopping, orders, and account from one place.
            </p>
        </div>


        {{-- Quick Actions --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

            <a
                href="{{ route('products.index') }}"
                class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-gray-300 hover:shadow-md"
            >
                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Shopping
                        </p>

                        <h2 class="mt-1 text-xl font-semibold text-gray-900">
                            Browse Products
                        </h2>
                    </div>

                    <div class="rounded-xl bg-gray-100 p-3 text-gray-700 transition group-hover:bg-gray-900 group-hover:text-white">
                        →
                    </div>

                </div>

                <p class="mt-4 text-sm leading-6 text-gray-500">
                    Explore available products and add items to your cart.
                </p>
            </a>


            <a
                href="{{ route('orders.index') }}"
                class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-gray-300 hover:shadow-md"
            >
                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Orders
                        </p>

                        <h2 class="mt-1 text-xl font-semibold text-gray-900">
                            My Orders
                        </h2>
                    </div>

                    <div class="rounded-xl bg-gray-100 p-3 text-gray-700 transition group-hover:bg-gray-900 group-hover:text-white">
                        →
                    </div>

                </div>

                <p class="mt-4 text-sm leading-6 text-gray-500">
                    View your orders, track their status, and see order details.
                </p>
            </a>


            <a
                href="{{ route('cart.index') }}"
                class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-gray-300 hover:shadow-md"
            >
                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Shopping Cart
                        </p>

                        <h2 class="mt-1 text-xl font-semibold text-gray-900">
                            View Cart
                        </h2>
                    </div>

                    <div class="rounded-xl bg-gray-100 p-3 text-gray-700 transition group-hover:bg-gray-900 group-hover:text-white">
                        →
                    </div>

                </div>

                <p class="mt-4 text-sm leading-6 text-gray-500">
                    Review your selected products before checking out.
                </p>
            </a>

        </div>


        {{-- Account Overview --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

            <div class="border-b border-gray-100 pb-5">
                <h2 class="text-lg font-semibold text-gray-900">
                    Account Overview
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Your current account information.
                </p>
            </div>

            <div class="mt-6 grid gap-6 sm:grid-cols-2">

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                        Name
                    </p>

                    <p class="mt-1 font-medium text-gray-900">
                        {{ auth()->user()->name }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                        Email
                    </p>

                    <p class="mt-1 font-medium text-gray-900">
                        {{ auth()->user()->email }}
                    </p>
                </div>

            </div>

        </div>


        {{-- Getting Started --}}
        <div class="rounded-2xl bg-gray-900 p-8 text-white">

            <p class="text-sm font-medium text-gray-400">
                Ready to shop?
            </p>

            <h2 class="mt-2 text-2xl font-bold tracking-tight">
                Find something you like.
            </h2>

            <p class="mt-2 max-w-xl text-sm leading-6 text-gray-400">
                Browse our products, add your favorites to the cart,
                and place your order whenever you're ready.
            </p>

            <div class="mt-6">
                <a
                    href="{{ route('products.index') }}"
                    class="inline-flex items-center rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-gray-900 transition hover:bg-gray-100"
                >
                    Start Shopping
                </a>
            </div>

        </div>

    </div>

</x-layouts::app>
