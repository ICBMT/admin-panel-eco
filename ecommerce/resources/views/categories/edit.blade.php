<x-layouts::app :title="__('Edit Category')">

<div class="mx-auto max-w-3xl space-y-8">

    {{-- Header --}}
    <div>
        <a
            href="{{ route('categories.index') }}"
            class="text-sm font-medium text-gray-500 transition hover:text-gray-900"
        >
            ← Back to Categories
        </a>

        <div class="mt-4">
            <p class="text-sm font-medium text-gray-500">
                Store Management
            </p>

            <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">
                Edit Category
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                Update the details of this category.
            </p>
        </div>
    </div>

    {{-- Form --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">

        <form
            action="{{ route('categories.update', $category) }}"
            method="POST"
            class="space-y-6"
        >

            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <label
                    for="name"
                    class="mb-2 block text-sm font-semibold text-gray-700"
                >
                    Category Name
                </label>

                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name', $category->name) }}"
                    placeholder="Enter category name"
                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition placeholder:text-gray-400 focus:border-gray-500 focus:ring-2 focus:ring-gray-200"
                >

                @error('name')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label
                    for="description"
                    class="mb-2 block text-sm font-semibold text-gray-700"
                >
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    placeholder="Describe this category..."
                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition placeholder:text-gray-400 focus:border-gray-500 focus:ring-2 focus:ring-gray-200"
                >{{ old('description', $category->description) }}</textarea>

                @error('description')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end">

                <a
                    href="{{ route('categories.index') }}"
                    class="rounded-xl border border-gray-300 px-5 py-3 text-center text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                >
                    Update Category
                </button>

            </div>

        </form>

    </div>

</div>

</x-layouts::app>
