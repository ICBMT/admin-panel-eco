<x-layouts::app :title="$category->name">

<div class="mx-auto max-w-6xl space-y-8">

    {{-- Header --}}
    <div>
        <a
            href="{{ route('categories.index') }}"
            class="text-sm font-medium text-gray-500 transition hover:text-gray-900"
        >
            ← Back to Categories
        </a>

        <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

            <div>
                <p class="text-sm font-medium text-gray-500">
                    Category
                </p>

                <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">
                    {{ $category->name }}
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500">
                    {{ $category->description ?: 'No description available.' }}
                </p>
            </div>

            @auth
                @if (auth()->user()->isAdmin())
                    <a
                        href="{{ route('categories.edit', $category) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                    >
                        Edit Category
                    </a>
                @endif
            @endauth

        </div>
    </div>

    {{-- Products --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">

        <div class="flex flex-col gap-2 border-b border-gray-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Products
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Products belonging to this category.
                </p>
            </div>

            <span class="w-fit rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                {{ $category->products->count() }}
                {{ $category->products->count() === 1 ? 'product' : 'products' }}
            </span>

        </div>

        <div class="p-6">

            @forelse ($category->products as $product)

                <div class="flex flex-col gap-5 border-b border-gray-100 py-5 first:pt-0 last:border-b-0 last:pb-0 sm:flex-row sm:items-center">

                    {{-- Image --}}
                    <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-gray-100">

                        @if ($product->image_path)

                            <img
                                src="{{ asset('storage/'.$product->image_path) }}"
                                alt="{{ $product->name }}"
                                class="h-full w-full object-cover"
                            >

                        @else

                            <div class="flex h-full items-center justify-center text-xs text-gray-400">
                                No image
                            </div>

                        @endif

                    </div>

                    {{-- Details --}}
                    <div class="min-w-0 flex-1">

                        <a
                            href="{{ route('products.show', $product) }}"
                            class="font-semibold text-gray-900 transition hover:text-gray-600"
                        >
                            {{ $product->name }}
                        </a>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ $product->description ?: 'No description available.' }}
                        </p>

                    </div>

                    {{-- Price --}}
                    <div class="sm:text-right">

                        <p class="font-bold text-gray-900">
                            ${{ number_format($product->price, 2) }}
                        </p>

                        <p class="mt-1 text-xs text-gray-500">
                            {{ $product->stock }} in stock
                        </p>

                    </div>

                    {{-- Action --}}
                    <a
                        href="{{ route('products.show', $product) }}"
                        class="rounded-xl border border-gray-300 px-4 py-2.5 text-center text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                    >
                        View
                    </a>

                </div>

            @empty

                <div class="py-16 text-center">

                    <h3 class="text-lg font-semibold text-gray-900">
                        No products in this category
                    </h3>

                    <p class="mt-2 text-sm text-gray-500">
                        Products assigned to this category will appear here.
                    </p>

                    @auth
                        @if (auth()->user()->isAdmin())
                            <a
                                href="{{ route('products.create') }}"
                                class="mt-6 inline-flex rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                            >
                                Add Product
                            </a>
                        @endif
                    @endauth

                </div>

            @endforelse

        </div>

    </div>

</div>

</x-layouts::app>
