<x-layouts::app :title="__('Create Product')">

<div class="mx-auto max-w-3xl space-y-8">

    {{-- Header --}}
    <div>
        <a
            href="{{ route('products.index') }}"
            class="text-sm font-medium text-gray-500 transition hover:text-gray-900"
        >
            ← Back to Products
        </a>

        <div class="mt-4">
            <p class="text-sm font-medium text-gray-500">Store Management</p>

            <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900">
                Create Product
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                Add a new product to your store.
            </p>
        </div>
    </div>

    {{-- Form --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">

        <form
            action="{{ route('products.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6"
        >

            @csrf

            {{-- Product name --}}
            <div>
                <label
                    for="name"
                    class="mb-2 block text-sm font-semibold text-gray-700"
                >
                    Product Name
                </label>

                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Enter product name"
                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition placeholder:text-gray-400 focus:border-gray-500 focus:ring-2 focus:ring-gray-200"
                >

                @error('name')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Price + Stock --}}
            <div class="grid gap-6 sm:grid-cols-2">

                <div>
                    <label
                        for="price"
                        class="mb-2 block text-sm font-semibold text-gray-700"
                    >
                        Price
                    </label>

                    <input
                        id="price"
                        type="number"
                        name="price"
                        value="{{ old('price') }}"
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition placeholder:text-gray-400 focus:border-gray-500 focus:ring-2 focus:ring-gray-200"
                    >

                    @error('price')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="stock"
                        class="mb-2 block text-sm font-semibold text-gray-700"
                    >
                        Stock
                    </label>

                    <input
                        id="stock"
                        type="number"
                        name="stock"
                        value="{{ old('stock') }}"
                        min="0"
                        placeholder="0"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition placeholder:text-gray-400 focus:border-gray-500 focus:ring-2 focus:ring-gray-200"
                    >

                    @error('stock')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            {{-- Category --}}
            <div>
                <label
                    for="category_id"
                    class="mb-2 block text-sm font-semibold text-gray-700"
                >
                    Category
                </label>

                <select
                    id="category_id"
                    name="category_id"
                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-gray-500 focus:ring-2 focus:ring-gray-200"
                >
                    <option value="">Select a category</option>

                    @foreach ($categories as $category)
                        <option
                            value="{{ $category->id }}"
                            @selected(old('category_id') == $category->id)
                        >
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                @error('category_id')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Image --}}
            <div>
                <label
                    for="image"
                    class="mb-2 block text-sm font-semibold text-gray-700"
                >
                    Product Image
                </label>

                <input
                    id="image"
                    type="file"
                    name="image"
                    accept="image/*"
                    class="block w-full rounded-xl border border-gray-300 bg-white text-sm text-gray-600 file:mr-4 file:border-0 file:bg-gray-100 file:px-4 file:py-3 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200"
                >

                <p class="mt-2 text-xs text-gray-500">
                    Upload a clear product image.
                </p>

                @error('image')
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
                    placeholder="Describe the product..."
                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition placeholder:text-gray-400 focus:border-gray-500 focus:ring-2 focus:ring-gray-200"
                >{{ old('description') }}</textarea>

                @error('description')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end">

                <a
                    href="{{ route('products.index') }}"
                    class="rounded-xl border border-gray-300 px-5 py-3 text-center text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                >
                    Create Product
                </button>

            </div>

        </form>

    </div>

</div>

</x-layouts::app>
