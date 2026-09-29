@extends('layouts.admin')

@section('title', 'Categories')
@section('page-title', 'Categories')
@section('page-subtitle', 'Group products into categories your customers can browse.')

@section('page-actions')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('categories.create') }}" class="btn-custom btn-custom-primary text-decoration-none">
            <i class="bi bi-plus-lg me-1"></i> Add Category
        </a>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                        class="text-decoration-none text-muted-green">Home</a></li>
                <li class="breadcrumb-item active text-main" aria-current="page">Categories</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')

    <!-- START: Basic Table Card Container -->
    <div class="table-card-custom">
        <!-- Header Controls -->
        <form method="GET" action="{{ route('admin.categories.index') }}">
            <div class="table-header-control">
                <!-- Search bar -->
                <div class="table-search-box">
                    <i class="bi bi-search table-search-icon"></i>
                    <input type="text" class="table-search-input" name="q" value="{{ $search }}"
                        placeholder="Search categories by name or description...">
                </div>
                <!-- Action buttons / Filter options -->
                <div class="table-filter-group">
                    <button class="btn-table-action" type="submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                    <a class="btn-table-action" href="{{ route('admin.categories.index') }}">
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
                        <th>Category</th>
                        <th>Description</th>
                        <th>Products</th>
                        <th>Created</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="table-product-name">{{ $category->name }}</td>
                            <td>
                                @if ($category->description)
                                    <span class="table-user-sub">{{ \Illuminate\Support\Str::limit($category->description, 70) }}</span>
                                @else
                                    <span class="table-user-sub">—</span>
                                @endif
                            </td>
                            <td><span class="badge-table success">{{ $category->products_count }}</span></td>
                            <td>{{ $category->created_at->format('M d, Y') }}</td>
                            <td class="text-center">
                                <a href="{{ route('categories.edit', $category) }}" class="table-btn-action"
                                    title="Edit category"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Delete category “{{ $category->name }}”? Its {{ $category->products_count }} product(s) will also be deleted. This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="table-btn-action delete" title="Delete category">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <p class="table-product-name mb-1">No categories found</p>
                                <p class="table-user-sub">Create your first category to start adding products.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Controls / Pagination -->
        <div class="table-footer-control">
            <span class="table-pagination-info">
                @if ($categories->count())
                    Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} entries
                @else
                    Showing 0 entries
                @endif
            </span>
            @if ($categories->hasPages())
                {{ $categories->links('admin.partials.pagination') }}
            @endif
        </div>
    </div>
    <!-- END: Basic Table Card Container -->

@endsection
