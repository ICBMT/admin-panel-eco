<x-layouts::app :title="__('Manage Orders')">

<div class="space-y-8">

    {{-- Header --}}
    <div>
        <p class="text-sm font-medium text-gray-500">Admin Panel</p>

        <div class="mt-1 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                    Manage Orders
                </h1>

                <p class="mt-2 text-sm text-gray-500">
                    View customer orders and update their status.
                </p>
            </div>

            <div class="rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700">
                Orders
            </div>
        </div>
    </div>

    {{-- Success message --}}
    @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Orders --}}
    <livewire:admin-order-search />

</div>

</x-layouts::app>
