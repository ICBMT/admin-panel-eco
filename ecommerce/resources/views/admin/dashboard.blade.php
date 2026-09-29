@extends('layouts.admin')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'An easy way to manage your store with care and precision.')

@section('page-actions')
    <button class="btn-date-picker" type="button" id="date-picker-trigger">
        <i class="bi bi-calendar4-event"></i>
        <span id="selected-date-range">{{ now()->subDays(7)->format('F j, Y') }} - {{ now()->format('F j, Y') }}</span>
        <i class="bi bi-chevron-down ms-1"></i>
    </button>
@endsection

@php
    // Snap computed percentages to the width utility classes provided by the template CSS.
    $barWidth = function (int $percent): string {
        return match (true) {
            $percent <= 10 => 'w-25',
            $percent <= 33 => 'w-38',
            $percent <= 48 => 'w-45',
            $percent <= 57 => 'w-50',
            $percent <= 70 => 'w-65',
            $percent <= 80 => 'w-75',
            $percent <= 92 => 'w-85',
            default => 'w-100',
        };
    };
@endphp

@section('content')

    <!-- START: Main Layout Grid (2 Columns: Dashboard + Performance Pane) -->
    <div class="row g-4">

        <!-- TOP AREA: Quick Info Stat Cards Row (Full Width) -->
        <div class="col-12">
            <div class="row g-4">
                <!-- Stat Card 1: Green Alert Banner -->
                <div class="col-md-4">
                    <div class="card alert-green-card">
                        <div class="position-relative z-index-2">
                            <span class="alert-green-badge">Update</span>
                            <div class="alert-green-date">{{ now()->format('M jS Y') }}</div>
                            <div class="alert-green-text">
                                {{ $revenueTrend >= 0 ? 'Sales revenue increased ' . abs($revenueTrend) . '%' : 'Sales revenue dropped ' . abs($revenueTrend) . '%' }}
                                in the last 7 days
                            </div>
                        </div>
                        <a href="{{ route('admin.orders.index') }}" class="alert-green-link z-index-2"
                            id="alert-link-statistics">
                            <span>See Orders</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>

                        <!-- Inline SVG geometric decoration (Lime green 6-pointed star/asterisk with rounded caps) -->
                        <svg class="alert-green-bg-shape" viewBox="0 0 100 100" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <g transform="translate(50,50)">
                                <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#B4F105" />
                                <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#B4F105"
                                    transform="rotate(60)" />
                                <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#B4F105"
                                    transform="rotate(120)" />
                            </g>
                        </svg>
                    </div>
                </div>

                <!-- Stat Card 2: Total Revenue -->
                <div class="col-md-4">
                    <div class="card card-stat d-flex flex-column justify-content-between">
                        <div>
                            <div class="card-header">
                                <span class="stat-label">Total Revenue</span>
                                <div class="dropdown">
                                    <button class="card-more-btn" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false" aria-label="More Options" id="btn-more-income">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}"><i
                                                    class="bi bi-bag-check"></i> View Orders</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('admin.products.index') }}"><i
                                                    class="bi bi-box-seam"></i> Manage Products</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="stat-value">${{ number_format($totalRevenue, 0) }}</div>
                            <div class="trend-badge {{ $revenueTrend >= 0 ? 'trend-up' : 'trend-down' }}">
                                <i class="bi {{ $revenueTrend >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-left' }}"></i>
                                <span>{{ ($revenueTrend >= 0 ? '+' : '') . $revenueTrend }}% vs previous 7 days</span>
                            </div>
                        </div>
                        <div class="sparkline-container sparkline-card-footer">
                            <div id="income-sparkline"></div>
                        </div>
                    </div>
                </div>

                <!-- Stat Card 3: Cancelled Orders -->
                <div class="col-md-4">
                    <div class="card card-stat d-flex flex-column justify-content-between">
                        <div>
                            <div class="card-header">
                                <span class="stat-label">Cancelled Orders</span>
                                <div class="dropdown">
                                    <button class="card-more-btn" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false" aria-label="More Options" id="btn-more-return">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}?status=cancelled"><i
                                                    class="bi bi-funnel"></i> Filter Cancelled</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}"><i
                                                    class="bi bi-bag-check"></i> All Orders</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="stat-value">${{ number_format($totalCancelled, 0) }}</div>
                            <div class="trend-badge {{ $cancelledTrend > 0 ? 'trend-down' : 'trend-up' }}">
                                <i class="bi {{ $cancelledTrend > 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-left' }}"></i>
                                <span>{{ ($cancelledTrend >= 0 ? '+' : '') . $cancelledTrend }}% vs previous 7 days</span>
                            </div>
                        </div>
                        <div class="sparkline-container sparkline-card-footer">
                            <div id="return-sparkline"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END: TOP AREA -->

        <!-- LEFT AREA: Primary Dashboard Stats & Tables -->
        <div class="col-xl-9 col-lg-8">

            <!-- START: Details Area (Transactions + Performance Charts) -->
            <div class="row g-4">
                <!-- Column: Revenue Chart (Full Width / Wider) -->
                <div class="col-12">
                    <div class="card mb-0">
                        <div class="card-header mb-2">
                            <h2 class="card-title">Revenue</h2>
                            <!-- Custom Static Legends -->
                            <div class="d-flex gap-3 align-items-center">
                                <div class="chart-legend-item">
                                    <span class="legend-dot bg-forest-medium"></span>
                                    <span class="chart-legend-label">Income</span>
                                </div>
                                <div class="chart-legend-item">
                                    <span class="legend-dot bg-lime-accent"></span>
                                    <span class="chart-legend-label">Cancelled</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-2 mb-3">
                            <span class="stat-value-amount">${{ number_format($totalRevenue, 2) }}</span>
                            <span class="trend-badge {{ $revenueTrend >= 0 ? 'trend-up' : 'trend-down' }} fs-xs">
                                {{ ($revenueTrend >= 0 ? '+' : '') . $revenueTrend }}% vs previous 7 days
                            </span>
                        </div>
                        <div id="revenue-chart"></div>
                    </div>
                </div>

                <!-- Column: Latest Orders List -->
                <div class="col-md-7 d-flex flex-column">
                    <div class="card h-100 flex-grow-1">
                        <div class="card-header">
                            <h2 class="card-title">Latest Orders</h2>
                            <div class="dropdown">
                                <button class="card-more-btn" type="button" data-bs-toggle="dropdown"
                                    aria-expanded="false" aria-label="More Options" id="btn-more-transaction">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                                    <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}"><i
                                                class="bi bi-bag-check"></i> All Orders</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}?status=pending"><i
                                                class="bi bi-hourglass-split"></i> Pending Only</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Transaction Items List -->
                        <div class="transaction-list">
                            @forelse ($recentOrders as $recentOrder)
                                <a href="{{ route('admin.orders.show', $recentOrder) }}"
                                    class="transaction-item text-decoration-none">
                                    <div class="transaction-icon bg-forest-light text-lime">
                                        <i class="bi {{ $recentOrder->payment_method === 'stripe' ? 'bi-stripe' : 'bi-cash-coin' }}"></i>
                                    </div>
                                    <div class="transaction-info">
                                        <div class="transaction-name">
                                            Order #{{ $recentOrder->id }} — {{ $recentOrder->user?->name ?? 'Unknown' }}
                                        </div>
                                        <div class="transaction-date">
                                            {{ $recentOrder->created_at->format('M d, Y') }} •
                                            {{ $recentOrder->created_at->format('h:i A') }}
                                        </div>
                                    </div>
                                    <div
                                        class="transaction-amount {{ $recentOrder->payment_status === 'paid' ? 'text-success' : 'text-main' }}">
                                        {{ $recentOrder->payment_status === 'paid' ? '+' : '' }}${{ number_format($recentOrder->total, 2) }}
                                    </div>
                                </a>
                            @empty
                                <div class="transaction-item">
                                    <div class="transaction-icon bg-forest-light text-lime">
                                        <i class="bi bi-bag"></i>
                                    </div>
                                    <div class="transaction-info">
                                        <div class="transaction-name">No orders yet</div>
                                        <div class="transaction-date">New orders will show up here</div>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                    </div>
                </div>

                <!-- Column: Product Overview Progress -->
                <div class="col-md-5 d-flex flex-column">
                    <div class="card h-100 flex-grow-1">
                        <div class="card-header">
                            <h2 class="card-title">Product Overview</h2>
                            <div class="dropdown">
                                <button class="card-more-btn" type="button" data-bs-toggle="dropdown"
                                    aria-expanded="false" aria-label="More Options" id="btn-more-products">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                                    <li><a class="dropdown-item" href="{{ route('products.create') }}"><i
                                                class="bi bi-plus-lg"></i> Add Product</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.products.index') }}"><i
                                                class="bi bi-gear"></i> Manage</a></li>
                                </ul>
                            </div>
                        </div>

                        @foreach ($overview as $row)
                            <div class="progress-container">
                                <div class="progress-label-row">
                                    <span class="progress-label">{{ $row['label'] }}</span>
                                    <span class="progress-value">{{ $row['value'] }}</span>
                                </div>
                                <div class="progress" role="progressbar"
                                    aria-label="{{ $row['label'] }} Progress" aria-valuenow="{{ $row['percent'] }}"
                                    aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar {{ $row['bar'] }} {{ $barWidth($row['percent']) }}"></div>
                                </div>
                            </div>
                        @endforeach

                    </div>

                </div>
            </div>
            <!-- END: Details Area -->

        </div>

        <!-- RIGHT AREA: Performance Details Sidebar Panel -->
        <div class="col-xl-3 col-lg-4">
            <div class="right-panel-wrapper d-flex flex-column gap-4 h-100">

                <!-- Sales Breakdown Donut Chart card -->
                <div class="card flex-grow-1 d-flex flex-column justify-content-between mb-0">
                    <div class="card-header mb-1">
                        <h2 class="card-title">Sales Breakdown</h2>
                    </div>

                    <div id="views-chart"></div>

                    <!-- Custom Legends below the chart -->
                    <div class="chart-legends-container">
                        <div class="chart-legend-item">
                            <span class="legend-dot bg-lime-accent"></span>
                            <span class="text-muted-green">Cash on Delivery</span>
                        </div>
                        <div class="chart-legend-item">
                            <span class="legend-dot bg-forest-medium"></span>
                            <span class="text-muted-green">Card Payments</span>
                        </div>
                        <div class="chart-legend-item">
                            <span class="legend-dot bg-brand-orange"></span>
                            <span class="text-muted-green">Cancelled</span>
                        </div>
                    </div>
                </div>

                <!-- Promotion CTA banner -->
                <div class="promo-banner-card">
                    <!-- Inline SVG geometric decoration (Lime green 6-pointed star/asterisk with rounded caps) -->
                    <svg class="promo-banner-bg-shape" viewBox="0 0 100 100" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <g transform="translate(50,50)">
                            <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#B4F105" />
                            <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#B4F105"
                                transform="rotate(60)" />
                            <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#B4F105"
                                transform="rotate(120)" />
                        </g>
                    </svg>

                    <h3 class="promo-title">Level up your store management to the next level.</h3>
                    <p class="promo-desc">An easy way to manage sales with care and precision.</p>
                    <a href="{{ route('admin.orders.index') }}" class="btn-promo text-decoration-none"
                        id="btn-promo-action">Review the latest orders</a>
                </div>
            </div>
        </div>
        <!-- END: RIGHT AREA -->

    </div>
    <!-- END: Main Layout Grid -->

@endsection

@push('libraries')
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
@endpush

@push('scripts')
    <script>
        window.SPARK_ADMIN_DATA = @js([
            'revenueChart' => [
                'categories' => $chart['categories'],
                'income' => $chart['income'],
                'cancelled' => $chart['cancelled'],
            ],
            'incomeSparkline' => $incomeSparkline,
            'returnSparkline' => $returnSparkline,
            'viewsChart' => [
                'series' => $donut['series'],
                'labels' => ['Cash on Delivery', 'Card Payments', 'Cancelled'],
                'total' => $donut['total'],
            ],
            'datePickerDefault' => [
                now()->subDays(7)->format('Y-m-d'),
                now()->format('Y-m-d'),
            ],
        ]);
    </script>
@endpush
