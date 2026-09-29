<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        {{ $title ?? config('app.name', 'Ecommerce') }}
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">

    {{-- Header --}}
    <header class="sticky top-0 z-40 border-b border-gray-200 bg-white/95 backdrop-blur">

        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">

            {{-- Logo --}}
            <a
                href="{{ route('dashboard') }}"
                class="text-xl font-bold tracking-tight text-gray-900 transition hover:text-gray-700"
            >
                Ecommerce
            </a>


            {{-- Navigation --}}
            <nav class="hidden items-center gap-7 md:flex">

                <a
                    href="{{ route('products.index') }}"
                    class="text-sm font-medium text-gray-600 transition hover:text-gray-900"
                >
                    Products
                </a>

                <a
                    href="{{ route('categories.index') }}"
                    class="text-sm font-medium text-gray-600 transition hover:text-gray-900"
                >
                    Categories
                </a>

                @auth

                    <a
                        href="{{ route('orders.index') }}"
                        class="text-sm font-medium text-gray-600 transition hover:text-gray-900"
                    >
                        My Orders
                    </a>

                    @if (auth()->user()->isAdmin())

                        <a
                            href="{{ route('admin.orders.index') }}"
                            class="text-sm font-semibold text-gray-900 transition hover:text-gray-600"
                        >
                            Admin
                        </a>

                    @endif

                @endauth

                <a
                    href="{{ route('cart.index') }}"
                    class="text-sm font-medium text-gray-600 transition hover:text-gray-900"
                >
                    Cart
                </a>

            </nav>


            {{-- User Actions --}}
            <div class="flex items-center gap-4">

                @auth

                    <div class="hidden text-right sm:block">

                        <p class="text-sm font-semibold text-gray-900">
                            {{ auth()->user()->name }}
                        </p>

                        @if (auth()->user()->isAdmin())
                            <p class="text-xs text-gray-500">
                                Administrator
                            </p>
                        @endif

                    </div>


                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 hover:text-gray-900"
                        >
                            Logout
                        </button>
                    </form>

                @else

                    <a
                        href="{{ route('login') }}"
                        class="text-sm font-medium text-gray-600 transition hover:text-gray-900"
                    >
                        Login
                    </a>

                    <a
                        href="{{ route('register') }}"
                        class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-800"
                    >
                        Register
                    </a>

                @endauth

            </div>

        </div>

    </header>


    {{-- Flash Messages --}}
    <div class="mx-auto max-w-7xl px-6">

        @if (session('success'))

            <div class="mt-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                {{ session('success') }}
            </div>

        @endif


        @if (session('error'))

            <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ session('error') }}
            </div>

        @endif

    </div>


    {{-- Main Content --}}
    <main class="mx-auto w-full max-w-7xl px-6 py-10">

        {{ $slot }}

    </main>


    {{-- Footer --}}
    <footer class="mt-20 border-t border-gray-200 bg-white">

        <div class="mx-auto max-w-7xl px-6 py-10">

            <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-center">

                <div>

                    <p class="text-lg font-bold tracking-tight text-gray-900">
                        Ecommerce
                    </p>

                    <p class="mt-1 text-sm text-gray-500">
                        Simple shopping, built with Laravel.
                    </p>

                </div>


                <div class="text-sm text-gray-500">

                    <p>
                        © {{ date('Y') }} Ecommerce
                    </p>

                    <p class="mt-1">
                        All rights reserved.
                    </p>

                </div>

            </div>

        </div>

    </footer>


    @livewireScripts

</body>
</html>
