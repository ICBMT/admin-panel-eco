@extends('layouts.admin')

@section('title', 'Create Product')
@section('page-title', 'Create Product')
@section('page-subtitle', 'Add a new product to your store catalog.')

@section('page-actions')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                    class="text-decoration-none text-muted-green">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}"
                    class="text-decoration-none text-muted-green">Products</a></li>
            <li class="breadcrumb-item active text-main" aria-current="page">Create</li>
        </ol>
    </nav>
@endsection

@section('content')

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card p-4">
                <h5 class="card-title mb-4">Product Details</h5>

                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label-custom">Product Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                            placeholder="Enter product name"
                            class="form-control-custom @error('name') is-invalid-custom @enderror">
                        @error('name')
                            <p class="table-user-sub text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="price" class="form-label-custom">Price (USD)</label>
                            <div class="input-group-custom">
                                <span class="input-group-text-custom">$</span>
                                <input id="price" type="number" name="price" value="{{ old('price') }}" step="0.01"
                                    min="0" placeholder="0.00"
                                    class="form-control-custom @error('price') is-invalid-custom @enderror">
                            </div>
                            @error('price')
                                <p class="table-user-sub text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="stock" class="form-label-custom">Stock Quantity</label>
                            <input id="stock" type="number" name="stock" value="{{ old('stock') }}" min="0"
                                placeholder="0"
                                class="form-control-custom @error('stock') is-invalid-custom @enderror">
                            @error('stock')
                                <p class="table-user-sub text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label-custom">Category</label>
                        <select id="category_id" name="category_id"
                            class="form-select-custom @error('category_id') is-invalid-custom @enderror">
                            <option value="">Select a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="table-user-sub text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label-custom">Product Image</label>
                        <input id="image" type="file" name="image"
                            class="form-control-custom @error('image') is-invalid-custom @enderror" accept="image/*">
                        @error('image')
                            <p class="table-user-sub text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label-custom">Description</label>
                        <textarea id="description" name="description" rows="4"
                            placeholder="Describe the product..."
                            class="form-control-custom @error('description') is-invalid-custom @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="table-user-sub text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-custom btn-custom-primary">
                            <i class="bi bi-check-lg me-1"></i> Create Product
                        </button>
                        <a href="{{ route('admin.products.index') }}"
                            class="btn-custom btn-custom-outline-secondary text-decoration-none">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="alert-custom alert-custom-info mb-0">
                <i class="bi bi-lightbulb-fill alert-custom-icon"></i>
                <div class="alert-custom-content">
                    <strong>Tip:</strong> Need to add many products at once? Use the
                    <a href="{{ route('admin.products.index') }}#import">spreadsheet import</a> instead.
                </div>
            </div>
        </div>
    </div>

@endsection
