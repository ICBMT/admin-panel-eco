<x-layouts::app :title="$product->name">

    <div class="space-y-8">

        {{-- Back --}}
        <div>
            <a
                href="{{ route('products.index') }}"
                class="text-sm font-medium text-gray-500 transition hover:text-gray-900"
            >
                ← Back to Products
            </a>
        </div>


        {{-- Product --}}
        <div class="grid gap-10 lg:grid-cols-2">

            {{-- Image --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                <div class="aspect-square bg-gray-100">

                    @if ($product->image_path)

                        <img
                            src="{{ asset('storage/' . $product->image_path) }}"
                            alt="{{ $product->name }}"
                            class="h-full w-full object-cover"
                        >

                    @else

                        <div class="flex h-full items-center justify-center text-sm text-gray-400">
                            No Image Available
                        </div>

                    @endif

                </div>

            </div>


            {{-- Information --}}
            <div class="flex flex-col justify-center">

                <p class="text-sm font-medium uppercase tracking-wide text-gray-400">
                    {{ $product->category->name }}
                </p>

                <h1 class="mt-2 text-4xl font-bold tracking-tight text-gray-900">
                    {{ $product->name }}
                </h1>

                <p class="mt-5 text-3xl font-semibold text-gray-900">
                    ${{ number_format($product->price, 2) }}
                </p>


                {{-- Stock --}}
                <div class="mt-5">

                    @if ($product->stock > 0)

                        <span class="inline-flex rounded-full bg-green-50 px-3 py-1.5 text-sm font-medium text-green-700">
                            {{ $product->stock }} in stock
                        </span>

                    @else

                        <span class="inline-flex rounded-full bg-red-50 px-3 py-1.5 text-sm font-medium text-red-700">
                            Out of stock
                        </span>

                    @endif

                </div>


                {{-- Description --}}
                <div class="mt-8 border-t border-gray-200 pt-8">

                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-900">
                        Description
                    </h2>

                    <p class="mt-3 text-sm leading-7 text-gray-600">
                        {{ $product->description ?: 'No description available.' }}
                    </p>

                </div>


                {{-- Add to Cart --}}
                <div class="mt-8">

                    @if ($product->stock > 0)

                        <form
                            action="{{ route('cart.add', $product) }}"
                            method="POST"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="w-full rounded-xl bg-gray-900 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-gray-800"
                            >
                                Add to Cart
                            </button>
                        </form>

                    @else

                        <button
                            type="button"
                            disabled
                            class="w-full cursor-not-allowed rounded-xl bg-gray-100 px-6 py-3.5 text-sm font-semibold text-gray-400"
                        >
                            Currently Out of Stock
                        </button>

                    @endif

                </div>


                {{-- Product Information --}}
                <div class="mt-8 grid grid-cols-2 gap-4 border-t border-gray-200 pt-8">

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                            Category
                        </p>

                        <p class="mt-1 text-sm font-medium text-gray-900">
                            {{ $product->category->name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                            Availability
                        </p>

                        <p class="mt-1 text-sm font-medium text-gray-900">
                            {{ $product->stock > 0 ? 'Available' : 'Sold Out' }}
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </div>

</x-layouts::app>
