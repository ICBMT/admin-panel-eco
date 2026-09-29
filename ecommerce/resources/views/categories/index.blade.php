<x-layouts::app :title="__('Categories')">

<div class="space-y-8">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <p class="text-sm font-medium text-gray-500">
                Store Management
            </p>

            <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">
                Categories
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                Organize your products into categories.
            </p>
        </div>

        @auth
            @if (auth()->user()->isAdmin())
                <a
                    href="{{ route('categories.create') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                >
                    + Add Category
                </a>
            @endif
        @endauth

    </div>

    {{-- Success message --}}
    @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Categories --}}
    <livewire:category-search />

</div>

</x-layouts::app>
