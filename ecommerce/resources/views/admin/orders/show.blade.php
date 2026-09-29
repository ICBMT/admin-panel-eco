@extends('layouts.admin')

@section('title', 'Order #' . $order->id)
@section('page-title', 'Order #ORD-' . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT))
@section('page-subtitle', 'Placed on ' . $order->created_at->format('d M Y, h:i A'))

@section('page-actions')
    <div class="d-flex align-items-center gap-3">
        @php
            $statusClass = match ($order->status) {
                \App\Enums\OrderStatus::Delivered => 'success',
                \App\Enums\OrderStatus::Cancelled => 'failed',
                default => 'pending',
            };
        @endphp
        <span class="badge-table {{ $statusClass }}">{{ $order->status->name }}</span>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                        class="text-decoration-none text-muted-green">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}"
                        class="text-decoration-none text-muted-green">Orders</a></li>
                <li class="breadcrumb-item active text-main" aria-current="page">#{{ $order->id }}</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')

    <div class="row g-4">

        <!-- LEFT: Items & Status History -->
        <div class="col-xl-8 col-lg-7">
            <!-- Order Items -->
            <div class="table-card-custom mb-4">
                <div class="p-4 pb-0">
                    <h2 class="card-title mb-0">Order Items</h2>
                </div>
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th class="text-end">Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="table-user-cell">
                                            @if ($item->product?->image_path)
                                                <img src="{{ asset('storage/' . $item->product->image_path) }}"
                                                    alt="{{ $item->product->name }}" class="table-user-avatar"
                                                    onerror="this.src='{{ asset('assets/images/avatar.png') }}'">
                                            @else
                                                <span class="table-user-avatar d-flex align-items-center justify-content-center bg-forest-light text-lime">
                                                    <i class="bi bi-box-seam"></i>
                                                </span>
                                            @endif
                                            <div>
                                                <div class="table-user-name">
                                                    {{ $item->product?->name ?? 'Deleted product' }}
                                                </div>
                                                @if ($item->product?->category)
                                                    <div class="table-user-sub">{{ $item->product->category->name }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-amount">${{ number_format($item->price, 2) }}</td>
                                    <td class="table-product-name">{{ $item->quantity }}</td>
                                    <td class="table-amount text-end">
                                        ${{ number_format($item->price * $item->quantity, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 table-user-sub">No items on this order.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="table-footer-control">
                    <span class="table-pagination-info">Grand Total</span>
                    <span class="table-amount fs-5">${{ number_format($order->total, 2) }}</span>
                </div>
            </div>

            <!-- Status History -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Status History</h2>
                </div>
                <div class="transaction-list">
                    @forelse ($order->statusHistory->sortByDesc('created_at') as $history)
                        @php
                            $historyIcon = match ($history->status) {
                                \App\Enums\OrderStatus::Pending => 'bi-hourglass-split',
                                \App\Enums\OrderStatus::Processing => 'bi-arrow-repeat',
                                \App\Enums\OrderStatus::Shipped => 'bi-truck',
                                \App\Enums\OrderStatus::Delivered => 'bi-check-circle-fill',
                                \App\Enums\OrderStatus::Cancelled => 'bi-x-circle-fill',
                            };
                        @endphp
                        <div class="transaction-item">
                            <div
                                class="transaction-icon {{ $history->status === \App\Enums\OrderStatus::Cancelled ? 'bg-brand-orange text-white' : 'bg-forest-light text-lime' }}">
                                <i class="bi {{ $historyIcon }}"></i>
                            </div>
                            <div class="transaction-info">
                                <div class="transaction-name">{{ $history->status->name }}</div>
                                <div class="transaction-date">
                                    {{ $history->created_at->format('d M Y') }} • {{ $history->created_at->format('h:i A') }}
                                </div>
                            </div>
                            <span class="badge-table {{ $history->status === \App\Enums\OrderStatus::Delivered ? 'success' : ($history->status === \App\Enums\OrderStatus::Cancelled ? 'failed' : 'pending') }}">
                                {{ $history->status->name }}
                            </span>
                        </div>
                    @empty
                        <div class="transaction-item">
                            <div class="transaction-icon bg-forest-light text-lime">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div class="transaction-info">
                                <div class="transaction-name">No status history available</div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RIGHT: Customer, Payment & Actions -->
        <div class="col-xl-4 col-lg-5">
            <div class="right-panel-wrapper d-flex flex-column gap-4 h-100">

                <!-- Customer -->
                <div class="card mb-0">
                    <div class="card-header mb-1">
                        <h2 class="card-title">Customer</h2>
                    </div>
                    <div class="table-user-cell px-4 pb-3">
                        <img src="{{ asset('assets/images/user_' . (($order->user->id % 8) + 1) . '.jpg') }}"
                            alt="{{ $order->user->name }}" class="table-user-avatar"
                            onerror="this.src='{{ asset('assets/images/avatar.png') }}'">
                        <div>
                            <div class="table-user-name">{{ $order->user->name }}</div>
                            <div class="table-user-sub">{{ $order->user->email }}</div>
                            <div class="table-user-sub mt-1">
                                Customer since {{ $order->user->created_at->format('M Y') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment -->
                <div class="card mb-0">
                    <div class="card-header mb-1">
                        <h2 class="card-title">Payment</h2>
                    </div>
                    <div class="px-4 pb-4 d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="table-user-sub">Method</span>
                            <span class="table-product-name">
                                {{ $order->payment_method === 'stripe' ? 'Card Payment' : 'Cash on Delivery' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="table-user-sub">Payment Status</span>
                            <span class="badge-table {{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'failed' : 'pending') }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="table-user-sub">Order Total</span>
                            <span class="table-amount">${{ number_format($order->total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="table-user-sub">Last Updated</span>
                            <span class="table-product-name">{{ $order->updated_at->format('d M Y, h:i A') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Update Status -->
                <div class="card flex-grow-1">
                    <div class="card-header mb-1">
                        <h2 class="card-title">Update Status</h2>
                    </div>
                    <div class="px-4 pb-4">
                        <p class="table-user-sub mb-3">Change the current order status. The customer is notified by
                            email on every change.</p>
                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <label for="status" class="form-label-custom">Order Status</label>
                            <select name="status" id="status" class="form-select-custom mb-3">
                                @foreach ($order->status::cases() as $status)
                                    <option value="{{ $status->value }}" @selected($order->status === $status)>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn-custom btn-custom-primary w-100">
                                <i class="bi bi-check2-circle me-1"></i> Update Status
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection
