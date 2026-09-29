@extends('layouts.admin')

@section('title', 'Orders')
@section('page-title', 'Orders')
@section('page-subtitle', 'View customer orders and keep their status up to date.')

@section('page-actions')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                    class="text-decoration-none text-muted-green">Home</a></li>
            <li class="breadcrumb-item active text-main" aria-current="page">Orders</li>
        </ol>
    </nav>
@endsection

@section('content')

    <!-- START: Basic Table Card Container -->
    <div class="table-card-custom">
        <!-- Header Controls -->
        <form method="GET" action="{{ route('admin.orders.index') }}">
            <div class="table-header-control">
                <!-- Search bar -->
                <div class="table-search-box">
                    <i class="bi bi-search table-search-icon"></i>
                    <input type="text" class="table-search-input" name="search" value="{{ $search }}"
                        placeholder="Search by order id, customer name or email...">
                </div>
                <!-- Action buttons / Filter options -->
                <div class="table-filter-group">
                    <select name="status" class="form-select-custom" aria-label="Filter by status" style="width: 190px;">
                        <option value="">All Statuses</option>
                        @foreach ($statuses as $orderStatus)
                            <option value="{{ $orderStatus->value }}" @selected($status === $orderStatus->value)>
                                {{ $orderStatus->name }}
                            </option>
                        @endforeach
                    </select>
                    <button class="btn-table-action" type="submit">
                        <i class="bi bi-funnel"></i> Apply
                    </button>
                    <a class="btn-table-action" href="{{ route('admin.orders.index') }}">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Responsive Table Wrapper -->
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td class="table-order-id">#ORD-{{ str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div class="table-user-cell">
                                    <img src="{{ asset('assets/images/user_' . (($order->user->id % 8) + 1) . '.jpg') }}"
                                        alt="{{ $order->user->name }}" class="table-user-avatar"
                                        onerror="this.src='{{ asset('assets/images/avatar.png') }}'">
                                    <div>
                                        <div class="table-user-name">{{ $order->user->name }}</div>
                                        <div class="table-user-sub">{{ $order->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $firstItem = $order->items->first();
                                    $extraItems = $order->items->count() - 1;
                                @endphp
                                @if ($firstItem)
                                    <div class="table-product-name">
                                        {{ $firstItem->product?->name ?? 'Deleted product' }}
                                    </div>
                                    @if ($extraItems > 0)
                                        <div class="table-user-sub">+ {{ $extraItems }} more item{{ $extraItems > 1 ? 's' : '' }}</div>
                                    @endif
                                @else
                                    <span class="table-user-sub">No items</span>
                                @endif
                            </td>
                            <td class="table-amount">${{ number_format($order->total, 2) }}</td>
                            <td>
                                <div class="table-user-sub mb-1">
                                    {{ $order->payment_method === 'stripe' ? 'Card Payment' : 'Cash on Delivery' }}
                                </div>
                                <span class="badge-table {{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'failed' : 'pending') }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>
                                @php
                                    $statusClass = match ($order->status) {
                                        \App\Enums\OrderStatus::Delivered => 'success',
                                        \App\Enums\OrderStatus::Cancelled => 'failed',
                                        default => 'pending',
                                    };
                                @endphp
                                <span class="badge-table {{ $statusClass }}">{{ $order->status->name }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.orders.show', $order) }}" class="table-btn-action"
                                    title="View details"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <p class="table-product-name mb-1">No orders found</p>
                                <p class="table-user-sub">Try changing your search or status filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Controls / Pagination -->
        <div class="table-footer-control">
            <span class="table-pagination-info">
                @if ($orders->count())
                    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} entries
                @else
                    Showing 0 entries
                @endif
            </span>
            @if ($orders->hasPages())
                {{ $orders->links('admin.partials.pagination') }}
            @endif
        </div>
    </div>
    <!-- END: Basic Table Card Container -->

@endsection
