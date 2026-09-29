<x-layouts::app :title="__('Products')">

<div class="space-y-8">

    @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3">
                <div class="flex items-center justify-between gap-4">
                    <p class="text-sm font-semibold text-red-700">
                        Import failed. Please fix the spreadsheet and try again.
                    </p>
    
                    <button
                        type="button"
                        onclick="this.parentElement.parentElement.remove()"
                        class="text-sm font-medium text-red-600 hover:text-red-800"
                    >
                        Dismiss
                    </button>
                </div>

                <ul class="mt-2 list-disc space-y-1 pl-5 text-xs text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <p class="text-sm font-medium text-gray-500">
                Store
            </p>

            <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">
                Products
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                Browse our products and find something you like.
            </p>
        </div>

        <div class="flex flex-wrap gap-3">

            <a
                href="{{ route('cart.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
            >
                Cart
            </a>

            @auth
                @if (auth()->user()->isAdmin())
                    <a
                        href="{{ route('products.create') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                    >
                        + Add Product
                    </a>

                    <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-3">
                        @csrf

                        <input
                            type="file"
                            name="file"
                            accept=".csv,.xlsx,.xls,.ods"
                            required
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm"
                        >

                        <button
                            type="submit"
                            class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800"
                        >
                            Import Products
                        </button>
                    </form>

                @endif
            @endauth

        </div>

    </div>

    {{-- Success message --}}
    @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Livewire Product Search --}}
    <livewire:product-search />

</div>

</x-layouts::app>
