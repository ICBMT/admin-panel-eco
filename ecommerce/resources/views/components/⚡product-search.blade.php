<?php

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public $search = '';

    public $category_id = '';

    public function updated()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        return $this->view([
            'products' => $query->latest()->paginate(12),
            'categories' => Category::latest()->get(),
        ]);
    }
};
?>

<div class="space-y-8">

    {{-- Search & Filters --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">

        <div class="grid gap-3 md:grid-cols-[1fr_220px_auto_auto]">

            <div class="relative">

                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Search products..."
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-400 focus:bg-white focus:ring-2 focus:ring-gray-900/10"
                >

            </div>


            <select
                wire:model.live="category_id"
                class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-700 outline-none transition focus:border-gray-400 focus:bg-white focus:ring-2 focus:ring-gray-900/10"
            >
                <option value="">
                    All Categories
                </option>

                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>


            <button
                type="button"
                wire:click="$set('search', '')"
                class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
            >
                Clear Search
            </button>


            <button
                type="button"
                wire:click="$set('category_id', '')"
                class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50"
            >
                Clear Filter
            </button>

        </div>

    </div>


    {{-- Loading --}}
    <div
        wire:loading
        class="rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-500"
    >
        Loading products...
    </div>


    {{-- Product Count --}}
    <div class="flex items-center justify-between">

        <p class="text-sm text-gray-500">
            Showing
            <span class="font-semibold text-gray-900">
                {{ $products->total() }}
            </span>
            products
        </p>

    </div>


    {{-- Products --}}
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

        @forelse ($products as $product)

            <div
                class="group overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
            >

                {{-- Image --}}
                <a href="{{ route('products.show', $product) }}">

                    <div class="aspect-square overflow-hidden bg-gray-100">

                        @if ($product->image_path)

                            <img
                                src="{{ asset('storage/' . $product->image_path) }}"
                                alt="{{ $product->name }}"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            >

                        @else

                            <div class="flex h-full items-center justify-center text-sm text-gray-400">
                                No Image
                            </div>

                        @endif

                    </div>

                </a>


                {{-- Product Info --}}
                <div class="p-5">

                    <div class="flex items-start justify-between gap-3">

                        <div class="min-w-0">

                            <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                                {{ $product->category->name }}
                            </p>

                            <h2 class="mt-1 truncate text-base font-semibold text-gray-900">
                                {{ $product->name }}
                            </h2>

                        </div>

                        <p class="shrink-0 font-semibold text-gray-900">
                            ${{ number_format($product->price, 2) }}
                        </p>

                    </div>


                    <p class="mt-3 line-clamp-2 text-sm leading-6 text-gray-500">
                        {{ $product->description }}
                    </p>


                    {{-- Stock --}}
                    <div class="mt-4">

                        @if ($product->stock > 0)

                            <span class="inline-flex rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">
                                {{ $product->stock }} in stock
                            </span>

                        @else

                            <span class="inline-flex rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700">
                                Out of stock
                            </span>

                        @endif

                    </div>


                    {{-- Actions --}}
                    <div class="mt-5 flex items-center gap-2">

                        <a
                            href="{{ route('products.show', $product) }}"
                            class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-center text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                        >
                            View
                        </a>


                        @if ($product->stock > 0)

                            <form
                                action="{{ route('cart.add', $product) }}"
                                method="POST"
                                class="flex-1"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="w-full rounded-lg bg-gray-900 px-3 py-2 text-sm font-semibold text-white transition hover:bg-gray-800"
                                >
                                    Add to Cart
                                </button>
                            </form>

                        @else

                            <button
                                type="button"
                                disabled
                                class="flex-1 cursor-not-allowed rounded-lg bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-400"
                            >
                                Sold Out
                            </button>

                        @endif

                    </div>


                    {{-- Management --}}
                    @auth

                        @if (auth()->user()->isAdmin())

                            <div class="mt-4 flex gap-3 border-t border-gray-100 pt-4">

                                <a
                                    href="{{ route('products.edit', $product) }}"
                                    class="text-xs font-medium text-gray-500 hover:text-gray-900"
                                >
                                    Edit
                                </a>

                                <form
                                    action="{{ route('products.destroy', $product) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="text-xs font-medium text-red-500 hover:text-red-700"
                                    >
                                        Delete
                                    </button>
                                </form>

                            </div>

                        @endif

                    @endauth

                </div>

            </div>

        @empty

            <div class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center">

                <h2 class="text-lg font-semibold text-gray-900">
                    No products found
                </h2>

                <p class="mt-2 text-sm text-gray-500">
                    Try changing your search or category filter.
                </p>

            </div>

        @endforelse

    </div>


    {{-- Pagination --}}
    @if ($products->hasPages())

        <div class="pt-2">
            {{ $products->links() }}
        </div>

    @endif

</div>
