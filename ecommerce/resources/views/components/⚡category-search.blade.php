<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;

new class extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $categories = Category::withCount('products')
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(9);

        return $this->view([
            'categories' => $categories,
        ]);
    }
};
?>

<div class="space-y-6">

{{-- Search --}}
<div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">

    <label class="mb-2 block text-sm font-medium text-gray-700">
        Search categories
    </label>

    <input
        type="text"
        wire:model.live.debounce.300ms="search"
        placeholder="Search by category name or description..."
        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
    >

</div>

{{-- Loading --}}
<div
    wire:loading
    class="text-sm text-gray-500"
>
    Searching categories...
</div>

{{-- Categories --}}
<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

    @forelse ($categories as $category)

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">

            <div class="flex items-start justify-between gap-4">

                <div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-900 text-lg font-bold text-white">
                        {{ strtoupper(substr($category->name, 0, 1)) }}
                    </div>

                    <h2 class="mt-4 text-xl font-bold text-gray-900">
                        {{ $category->name }}
                    </h2>
                </div>

                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                    {{ $category->products_count }}
                    {{ Str::plural('product', $category->products_count) }}
                </span>

            </div>

            @if ($category->description)
                <p class="mt-4 text-sm leading-6 text-gray-500">
                    {{ $category->description }}
                </p>
            @else
                <p class="mt-4 text-sm text-gray-400">
                    No description available.
                </p>
            @endif

            <div class="mt-6 flex items-center justify-between">

                <a
                    href="{{ route('categories.show', $category) }}"
                    class="font-semibold text-gray-900 transition hover:text-gray-500"
                >
                    View Category
                </a>

                @auth
                    @if (auth()->user()->isAdmin())

                        <div class="flex items-center gap-3">

                            <a
                                href="{{ route('categories.edit', $category) }}"
                                class="text-sm font-semibold text-blue-600 hover:text-blue-800"
                            >
                                Edit
                            </a>

                            <form
                                method="POST"
                                action="{{ route('categories.destroy', $category) }}"
                                onsubmit="return confirm('Are you sure you want to delete this category?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="text-sm font-semibold text-red-600 hover:text-red-800"
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

        <div class="sm:col-span-2 lg:col-span-3 rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center">

            <h2 class="font-semibold text-gray-900">
                No categories found
            </h2>

            <p class="mt-2 text-sm text-gray-500">
                Try a different search term.
            </p>

        </div>

    @endforelse

</div>

{{-- Pagination --}}
@if ($categories->hasPages())
    <div>
        {{ $categories->links() }}
    </div>
@endif

</div>
