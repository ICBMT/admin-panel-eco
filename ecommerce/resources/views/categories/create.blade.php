@extends('layouts.admin')

@section('title', 'Create Category')
@section('page-title', 'Create Category')
@section('page-subtitle', 'Group products into a category customers can browse.')

@section('page-actions')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                    class="text-decoration-none text-muted-green">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}"
                    class="text-decoration-none text-muted-green">Categories</a></li>
            <li class="breadcrumb-item active text-main" aria-current="page">Create</li>
        </ol>
    </nav>
@endsection

@section('content')

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card p-4">
                <h5 class="card-title mb-4">Category Details</h5>

                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label-custom">Category Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                            placeholder="e.g. Electronics"
                            class="form-control-custom @error('name') is-invalid-custom @enderror">
                        @error('name')
                            <p class="table-user-sub text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label-custom">Description</label>
                        <textarea id="description" name="description" rows="4"
                            placeholder="Short description shown on the category page (optional)"
                            class="form-control-custom @error('description') is-invalid-custom @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="table-user-sub text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-custom btn-custom-primary">
                            <i class="bi bi-check-lg me-1"></i> Create Category
                        </button>
                        <a href="{{ route('admin.categories.index') }}"
                            class="btn-custom btn-custom-outline-secondary text-decoration-none">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="alert-custom alert-custom-info mb-0">
                <i class="bi bi-lightbulb-fill alert-custom-icon"></i>
                <div class="alert-custom-content">
                    <strong>Tip:</strong> Categories appear on the storefront at
                    <a href="{{ route('categories.index') }}">/categories</a> and in the product filters.
                </div>
            </div>
        </div>
    </div>

@endsection
