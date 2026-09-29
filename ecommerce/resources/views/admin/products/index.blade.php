@extends('layouts.admin')

@section('title', 'Products')
@section('page-title', 'Products')
@section('page-subtitle', 'Manage catalog items, stock levels and imports.')

@section('page-actions')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('products.create') }}" class="btn-custom btn-custom-primary text-decoration-none">
            <i class="bi bi-plus-lg me-1"></i> Add Product
        </a>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                        class="text-decoration-none text-muted-green">Home</a></li>
                <li class="breadcrumb-item active text-main" aria-current="page">Products</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')

    <!-- START: Import Products -->
    <div class="card p-4 mb-4" id="import">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h5 class="card-title mb-1"><i class="bi bi-upload me-2"></i>Import Products</h5>
                <p class="table-user-sub mb-0">
                    Upload an <strong>xlsx, xls, ods or csv</strong> spreadsheet with the columns:
                    <code>name</code>, <code>price</code>, <code>stock</code>, <code>category_id</code>,
                    <code>description</code>.
                </p>
            </div>
        </div>
        <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data"
            class="d-flex flex-wrap align-items-end gap-3 mt-3">
            @csrf
            <div class="flex-grow-1" style="min-width: 260px;">
                <label for="import-file" class="form-label-custom">Spreadsheet File</label>
                <input type="file" name="file" id="import-file"
                    class="form-control-custom @error('file') is-invalid-custom @enderror"
                    accept=".xlsx,.xls,.ods,.csv" required>
                @error('file')
                    <p class="table-user-sub text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="btn-custom btn-custom-primary">
                <i class="bi bi-cloud-arrow-up me-1"></i> Import
            </button>
        </form>
    </div>
    <!-- END: Import Products -->

    <!-- START: Basic Table Card Container -->
    <div class="table-card-custom">
        <!-- Header Controls -->
        <form method="GET" action="{{ route('admin.products.index') }}">
            <div class="table-header-control">
                <!-- Search bar -->
                <div class="table-search-box">
                    <i class="bi bi-search table-search-icon"></i>
                    <input type="text" class="table-search-input" name="q" value="{{ $search }}"
                        placeholder="Search products by name or category...">
                </div>
                <!-- Action buttons / Filter options -->
                <div class="table-filter-group">
                    <button class="btn-table-action" type="submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                    <a class="btn-table-action" href="{{ route('admin.products.index') }}">
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
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Created</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>
                                <div class="table-user-cell">
                                    @if ($product->image_path)
                                        <img src="{{ asset('storage/' . $product->image_path) }}"
                                            alt="{{ $product->name }}" class="table-user-avatar"
                                            onerror="this.src='{{ asset('assets/images/avatar.png') }}'">
                                    @else
                                        <span class="table-user-avatar d-flex align-items-center justify-content-center bg-forest-light text-lime">
                                            <i class="bi bi-box-seam"></i>
                                        </span>
                                    @endif
                                    <div>
                                        <div class="table-user-name">{{ $product->name }}</div>
                                        <div class="table-user-sub">{{ \Illuminate\Support\Str::limit($product->description, 48) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="table-product-name">{{ $product->category?->name ?? '—' }}</td>
                            <td class="table-amount">${{ number_format($product->price, 2) }}</td>
                            <td>
                                @if ($product->stock <= 0)
                                    <span class="badge-table failed">Out of Stock</span>
                                @elseif ($product->stock < 5)
                                    <span class="badge-table pending">Low ({{ $product->stock }})</span>
                                @else
                                    <span class="badge-table success">{{ $product->stock }} in stock</span>
                                @endif
                            </td>
                            <td>{{ $product->created_at->format('M d, Y') }}</td>
                            <td class="text-center">
                                <a href="{{ route('products.edit', $product) }}" class="table-btn-action"
                                    title="Edit product"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Delete product “{{ $product->name }}”? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="table-btn-action delete" title="Delete product">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <p class="table-product-name mb-1">No products found</p>
                                <p class="table-user-sub">Add a product or import a spreadsheet to get started.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Controls / Pagination -->
        <div class="table-footer-control">
            <span class="table-pagination-info">
                @if ($products->count())
                    Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} entries
                @else
                    Showing 0 entries
                @endif
            </span>
            @if ($products->hasPages())
                {{ $products->links('admin.partials.pagination') }}
            @endif
        </div>
    </div>
    <!-- END: Basic Table Card Container -->

@endsection
