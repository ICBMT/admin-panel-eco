<?php

use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImportController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Admin product and category management
    Route::middleware('admin')->group(function () {

        // Admin panel (Spark Admin UI)
        Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        Route::get('/admin/orders', [AdminOrderController::class, 'index'])
            ->name('admin.orders.index');

        Route::put('/admin/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
            ->name('admin.orders.update-status');

        Route::get('/admin/orders/{order}', [AdminOrderController::class, 'show'])
            ->name('admin.orders.show');

        Route::get('/admin/products', [AdminProductController::class, 'index'])
            ->name('admin.products.index');

        Route::get('/admin/categories', [AdminCategoryController::class, 'index'])
            ->name('admin.categories.index');

        Route::post('/products/import', [ProductImportController::class, 'import'])
            ->name('products.import');

        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');

        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    });

    // Public product routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    // Public category routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');

    // Orders
    Route::post('/checkout', [OrderController::class, 'checkout'])
        ->middleware(['auth', 'verified'])
        ->name('checkout');

    Route::get('/orders', [OrderController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('orders.index');

    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->middleware(['auth', 'verified'])
        ->name('orders.show');

    Route::get('/stripe/success', [OrderController::class, 'stripeSuccess'])
        ->middleware(['auth', 'verified'])
        ->name('stripe.success');

    Route::get('/stripe/cancel', [OrderController::class, 'stripeCancel'])
        ->middleware(['auth', 'verified'])
        ->name('stripe.cancel');

});

require __DIR__.'/settings.php';
