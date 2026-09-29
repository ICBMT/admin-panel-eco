@extends('layouts.admin')

@section('title', 'Edit Category')
@section('page-title', 'Edit Category')
@section('page-subtitle', 'Update “' . $category->name . '”.')

@section('page-actions')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                    class="text-decoration-none text-muted-green">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}"
                    class="text-decoration-none text-muted-green">Categories</a></li>
            <li class="breadcrumb-item active text-main" aria-current="page">Edit</li>
        </ol>
    </nav>
@endsection

@section('content')

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card p-4">
                <h5 class="card-title mb-4">Category Details</h5>

                <form action="{{ route('categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label-custom">Category Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $category->name) }}"
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
                            class="form-control-custom @error('description') is-invalid-custom @enderror">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <p class="table-user-sub text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-custom btn-custom-primary">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                        <a href="{{ route('admin.categories.index') }}"
                            class="btn-custom btn-custom-outline-secondary text-decoration-none">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="alert-custom alert-custom-warning mb-0">
                <i class="bi bi-exclamation-circle-fill alert-custom-icon"></i>
                <div class="alert-custom-content">
                    <strong>Notice:</strong> Deleting this category also deletes every product inside it, including
                    their order history entries.
                </div>
            </div>
        </div>
    </div>

@endsection
