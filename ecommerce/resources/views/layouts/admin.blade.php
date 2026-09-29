<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | {{ config('app.name', 'Ecommerce') }} Admin</title>

    <!-- Favicon (moved from Spark Admin template) -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Local Third-Party Libraries (100% Offline Compatible) -->
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/apexcharts/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">

    <!-- Main Design System & Custom Stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
</head>

@php
    $panelOrders = \App\Models\Order::with('user')->latest()->limit(5)->get();
    $panelUser = auth()->user();
@endphp

<body>

    <!-- ==========================================
         START: Sidebar Component
         Highly polished, dark-green sticky navigation
         ========================================== -->
    <div class="sidebar-wrapper" id="sidebar">
        <!-- Brand Logo / Identity -->
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <i class="bi bi-asterisk"></i>
            <span>Ecommerce</span>
        </a>

        <!-- Navigation Menu -->
        <div class="flex-grow-1 overflow-y-auto">
            <!-- Group: Management -->
            <div class="sidebar-menu-section">
                <div class="sidebar-menu-title">Management</div>
                <ul class="sidebar-menu-list">
                    <li class="sidebar-menu-item">
                        <a href="{{ route('admin.dashboard') }}"
                            class="sidebar-menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                            title="Overview">
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="{{ route('admin.orders.index') }}"
                            class="sidebar-menu-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                            title="Orders">
                            <i class="bi bi-bag-check-fill"></i>
                            <span>Orders</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="{{ route('admin.products.index') }}"
                            class="sidebar-menu-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"
                            title="Products">
                            <i class="bi bi-box-seam-fill"></i>
                            <span>Products</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="{{ route('admin.categories.index') }}"
                            class="sidebar-menu-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                            title="Categories">
                            <i class="bi bi-tags-fill"></i>
                            <span>Categories</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Group: Quick Create -->
            <div class="sidebar-menu-section">
                <div class="sidebar-menu-title">Create</div>
                <ul class="sidebar-menu-list">
                    <li class="sidebar-menu-item">
                        <a href="{{ route('products.create') }}"
                            class="sidebar-menu-link {{ request()->routeIs('products.create') ? 'active' : '' }}"
                            title="New Product">
                            <i class="bi bi-plus-circle-fill"></i>
                            <span>New Product</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="{{ route('categories.create') }}"
                            class="sidebar-menu-link {{ request()->routeIs('categories.create') ? 'active' : '' }}"
                            title="New Category">
                            <i class="bi bi-bookmark-plus-fill"></i>
                            <span>New Category</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Group: Store -->
            <div class="sidebar-menu-section">
                <div class="sidebar-menu-title">Store</div>
                <ul class="sidebar-menu-list">
                    <li class="sidebar-menu-item">
                        <a href="{{ route('products.index') }}" title="Visit Storefront">
                            <i class="bi bi-shop"></i>
                            <span>Visit Storefront</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="{{ route('profile.edit') }}" title="My Account">
                            <i class="bi bi-person-fill-gear"></i>
                            <span>My Account</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Sidebar Profile Card (Dynamic Footer) -->
        <div class="sidebar-profile">
            <img src="{{ asset('assets/images/avatar.png') }}" alt="Administrator" class="sidebar-profile-img"
                onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=256&auto=format&fit=crop'">
            <div class="sidebar-profile-info">
                <div class="sidebar-profile-name">{{ $panelUser->name }}</div>
                <div class="sidebar-profile-email">{{ $panelUser->email }}</div>
            </div>
        </div>
    </div>
    <!-- ==========================================
         END: Sidebar Component
         ========================================== -->


    <!-- ==========================================
         START: Main Content Area
         ========================================== -->
    <div class="main-wrapper">

        <!-- START: Top Navbar Component -->
        <header class="navbar-custom">
            <div class="navbar-left">
                <!-- Desktop sidebar toggle (visible on large screens only) -->
                <button class="btn-desktop-toggle d-none d-xl-flex align-items-center justify-content-center me-3"
                    id="desktop-sidebar-toggle" aria-label="Minimize Sidebar">
                    <i class="bi bi-chevron-bar-left"></i>
                </button>
                <!-- Mobile sidebar toggle -->
                <button class="sidebar-toggle-btn me-2" id="sidebar-toggle" aria-label="Toggle Navigation">
                    <i class="bi bi-list"></i>
                </button>

                <!-- Quick Actions Dropdown -->
                <div class="dropdown ms-2">
                    <button class="btn-quick-action dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false" id="quick-actions-dropdown">
                        <i class="bi bi-plus-lg"></i>
                        <span>Create</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-quick-action" aria-labelledby="quick-actions-dropdown">
                        <li class="dropdown-header">Quick Action Shortcuts</li>
                        <li><a class="dropdown-item" href="{{ route('products.create') }}"><i
                                    class="bi bi-box-seam"></i> New Product</a></li>
                        <li><a class="dropdown-item" href="{{ route('categories.create') }}"><i
                                    class="bi bi-tags"></i> New Category</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.products.index') }}#import"><i
                                    class="bi bi-upload"></i> Import Products</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}"><i
                                    class="bi bi-bag-check"></i> Manage Orders</a></li>
                    </ul>
                </div>
            </div>

            <!-- Mid navbar: search pill -->
            <form action="{{ route('admin.orders.index') }}" method="GET" class="navbar-search-wrapper">
                <input type="text" class="navbar-search-input" placeholder="Search orders by id, customer or email..."
                    id="main-search" name="search" value="{{ request('search') }}">
                <button class="navbar-search-btn" type="submit" aria-label="Search">
                    <i class="bi bi-search"></i>
                </button>
            </form>

            <!-- Right actions -->
            <div class="navbar-actions">
                <!-- Fullscreen Toggle -->
                <button class="navbar-action-btn me-1" aria-label="Toggle Fullscreen" id="btn-fullscreen">
                    <i class="bi bi-arrows-fullscreen"></i>
                </button>

                <!-- Latest Orders Dropdown -->
                <div class="dropdown">
                    <button class="navbar-action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false" id="btn-notifications" data-bs-auto-close="outside">
                        <i class="bi bi-bell"></i>
                        @if ($panelOrders->isNotEmpty())
                            <span class="navbar-action-badge"></span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-notification p-0"
                        aria-labelledby="btn-notifications">
                        <div class="notification-header">
                            <h6 class="notification-title">Latest Orders</h6>
                            <a href="{{ route('admin.orders.index') }}" class="btn-clear-all text-decoration-none">View
                                all</a>
                        </div>
                        <div class="notification-list">
                            @forelse ($panelOrders as $panelOrder)
                                <a href="{{ route('admin.orders.show', $panelOrder) }}" class="notification-item">
                                    <div
                                        class="notification-icon {{ $panelOrder->status === \App\Enums\OrderStatus::Cancelled ? 'bg-warning text-dark' : 'bg-success text-white' }}">
                                        <i class="bi {{ $panelOrder->payment_method === 'stripe' ? 'bi-credit-card-2-front' : 'bi-cash-coin' }}"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p class="notification-text">
                                            <strong>#{{ $panelOrder->id }}</strong>
                                            {{ $panelOrder->user?->name ?? 'Unknown' }} —
                                            ${{ number_format($panelOrder->total, 2) }}
                                        </p>
                                        <span class="notification-time">
                                            {{ $panelOrder->created_at->diffForHumans() }} ·
                                            {{ ucfirst($panelOrder->status->value) }}
                                        </span>
                                    </div>
                                    @unless ($panelOrder->status === \App\Enums\OrderStatus::Delivered)
                                        <span class="notification-unread-dot"></span>
                                    @endunless
                                </a>
                            @empty
                                <a href="{{ route('admin.orders.index') }}" class="notification-item">
                                    <div class="notification-icon bg-forest-light text-lime">
                                        <i class="bi bi-bell-slash"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p class="notification-text">No orders yet</p>
                                        <span class="notification-time">New orders will appear here</span>
                                    </div>
                                </a>
                            @endforelse
                        </div>
                        <a href="{{ route('admin.orders.index') }}" class="notification-footer">View All Orders</a>
                    </div>
                </div>

                <!-- Profile Dropdown -->
                <div class="dropdown ms-2">
                    <button class="navbar-profile-btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false" id="profile-dropdown">
                        <img src="{{ asset('assets/images/avatar.png') }}" alt="Profile Image"
                            class="navbar-profile-img">
                        <span class="navbar-profile-name d-none d-md-inline">{{ $panelUser->name }}</span>
                        <i class="bi bi-chevron-down navbar-profile-caret"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-profile"
                        aria-labelledby="profile-dropdown">
                        <li class="dropdown-header">Administrator</li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> My
                                Account</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.index') }}"><i class="bi bi-shop"></i>
                                Visit Storefront</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        <!-- END: Top Navbar Component -->

        <!-- START: Page Header Banner -->
        <div class="page-header">
            <div>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                <p class="page-subtitle">@yield('page-subtitle', 'Manage your store with care and precision.')</p>
            </div>
            @yield('page-actions')
        </div>
        <!-- END: Page Header Banner -->

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert-custom alert-custom-success">
                <i class="bi bi-check-circle-fill alert-custom-icon"></i>
                <div class="alert-custom-content">
                    <strong>Success:</strong> {{ session('success') }}
                </div>
                <button class="alert-custom-close" type="button" aria-label="Close"
                    onclick="this.parentElement.remove();">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert-custom alert-custom-danger">
                <i class="bi bi-exclamation-triangle-fill alert-custom-icon"></i>
                <div class="alert-custom-content">
                    <strong>Error:</strong> {{ session('error') }}
                </div>
                <button class="alert-custom-close" type="button" aria-label="Close"
                    onclick="this.parentElement.remove();">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-custom alert-custom-danger">
                <i class="bi bi-exclamation-triangle-fill alert-custom-icon"></i>
                <div class="alert-custom-content">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button class="alert-custom-close" type="button" aria-label="Close"
                    onclick="this.parentElement.remove();">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        <!-- START: Page Content -->
        @yield('content')
        <!-- END: Page Content -->

        <!-- START: Footer Component -->
        <footer class="footer-custom">
            <div class="footer-left">
                <span class="footer-logo">
                    <i class="bi bi-asterisk"></i> {{ config('app.name', 'Ecommerce') }} Admin
                </span>
                <span class="footer-separator">|</span>
                <span class="footer-copy">&copy; {{ date('Y') }} Admin panel UI based on
                    <a href="https://sparkadmin.web.id" target="_blank" rel="noopener">Spark Admin</a> • Distributed by
                    <a href="https://www.themewagon.com/" target="_blank" rel="noopener">ThemeWagon</a>
                </span>
            </div>
            <div class="footer-right">
                <ul class="footer-links">
                    <li><a href="{{ route('admin.dashboard') }}" class="footer-link">Dashboard</a></li>
                    <li><a href="{{ route('admin.orders.index') }}" class="footer-link">Orders</a></li>
                    <li><a href="{{ route('admin.products.index') }}" class="footer-link">Products</a></li>
                    <li><a href="{{ route('products.index') }}" class="footer-link">Storefront <span
                                class="status-dot"></span></a></li>
                </ul>
            </div>
        </footer>
        <!-- END: Footer Component -->

    </div>
    <!-- ==========================================
         END: Main Content Area
         ========================================== -->

    <!-- Local Third-Party Libraries Script dependencies -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    @stack('libraries')

    <!-- Local dashboard interactions controller (sidebar, fullscreen, charts) -->
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>

    @stack('scripts')
</body>

</html>
