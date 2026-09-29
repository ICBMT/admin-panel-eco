<?php

use App\Enums\OrderStatus;
use App\Imports\ProductsImport;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($this->admin);
});

it('forbids non-admins from the admin panel', function () {
    $customer = User::factory()->create();

    $this->actingAs($customer);
    $this->get(route('admin.dashboard'))->assertForbidden();
    $this->get(route('admin.orders.index'))->assertForbidden();
    $this->get(route('admin.products.index'))->assertForbidden();
    $this->get(route('admin.categories.index'))->assertForbidden();
});

it('renders the admin dashboard with store statistics', function () {
    $category = Category::create(['name' => 'Gadgets']);
    Product::create(['name' => 'Widget', 'price' => 10, 'stock' => 5, 'category_id' => $category->id]);

    $customer = User::factory()->create();
    Order::create([
        'user_id' => $customer->id,
        'total' => 123.45,
        'status' => OrderStatus::Processing->value,
        'payment_method' => 'cod',
        'payment_status' => 'pending',
    ]);

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Total Revenue')
        ->assertSee('$123')
        ->assertSee('Product Overview')
        ->assertSee('Sales Breakdown');
});

it('lists, searches and filters orders in the admin panel', function () {
    $buyer = User::factory()->create(['name' => 'Eleanor Pena', 'email' => 'eleanor@example.com']);
    $other = User::factory()->create(['name' => 'Wade Warren', 'email' => 'wade@example.com']);

    $match = Order::create([
        'user_id' => $buyer->id,
        'total' => 50,
        'status' => OrderStatus::Pending->value,
        'payment_method' => 'cod',
        'payment_status' => 'pending',
    ]);

    $shipped = Order::create([
        'user_id' => $other->id,
        'total' => 75,
        'status' => OrderStatus::Shipped->value,
        'payment_method' => 'stripe',
        'payment_status' => 'paid',
    ]);

    $this->get(route('admin.orders.index'))
        ->assertOk()
        ->assertSee('#ORD-'.str_pad((string) $match->id, 4, '0', STR_PAD_LEFT))
        ->assertSee('Wade Warren');

    $this->get(route('admin.orders.index', ['search' => 'eleanor']))
        ->assertOk()
        ->assertSee('eleanor@example.com')
        ->assertDontSee('wade@example.com');

    $this->get(route('admin.orders.index', ['status' => 'shipped']))
        ->assertOk()
        ->assertSee('wade@example.com')
        ->assertDontSee('eleanor@example.com');

    // Invalid status values are rejected by validation.
    $this->get(route('admin.orders.index', ['status' => 'not-a-status']))
        ->assertSessionHasErrors('status');
});

it('shows an order with items, history and status management', function () {
    $buyer = User::factory()->create();
    $category = Category::create(['name' => 'Gadgets']);
    $product = Product::create([
        'name' => 'Laser Mouse',
        'price' => 45.5,
        'stock' => 9,
        'category_id' => $category->id,
    ]);

    $order = Order::create([
        'user_id' => $buyer->id,
        'total' => 91.0,
        'status' => OrderStatus::Processing->value,
        'payment_method' => 'cod',
        'payment_status' => 'pending',
    ]);
    $order->items()->create([
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => 45.5,
    ]);
    $order->statusHistory()->create(['status' => OrderStatus::Processing->value]);

    $this->get(route('admin.orders.show', $order))
        ->assertOk()
        ->assertSee('Laser Mouse')
        ->assertSee('Status History')
        ->assertSee('Update Status');
});

it('updates an order status, records history and marks cod orders as paid on delivery', function () {
    Notification::fake();

    $buyer = User::factory()->create();
    $order = Order::create([
        'user_id' => $buyer->id,
        'total' => 30,
        'status' => OrderStatus::Shipped->value,
        'payment_method' => 'cod',
        'payment_status' => 'pending',
    ]);

    $this->put(route('admin.orders.update-status', $order), [
        'status' => OrderStatus::Delivered->value,
    ])
        ->assertRedirect(route('admin.orders.show', $order))
        ->assertSessionHas('success');

    expect($order->refresh()->status)->toBe(OrderStatus::Delivered)
        ->and($order->payment_status)->toBe('paid')
        ->and(OrderStatusHistory::where('order_id', $order->id)->where('status', OrderStatus::Delivered->value)->exists())->toBeTrue();

    $this->put(route('admin.orders.update-status', $order), [
        'status' => 'bogus',
    ])->assertSessionHasErrors('status');
});

it('manages products from the admin panel', function () {
    Storage::fake('public');

    $category = Category::create(['name' => 'Gadgets']);

    $this->get(route('admin.products.index'))
        ->assertOk()
        ->assertSee('Import Products');

    $this->post(route('products.store'), [
        'name' => 'Mechanical Keyboard',
        'price' => 149.99,
        'stock' => 12,
        'category_id' => $category->id,
        'description' => 'Hot-swappable switches.',
        'image' => UploadedFile::fake()->image('keyboard.jpg'),
    ])->assertRedirect(route('products.index'));

    $product = Product::where('name', 'Mechanical Keyboard')->firstOrFail();

    expect($product->price)->toBe('149.99')
        ->and($product->stock)->toBe(12);

    Storage::disk('public')->assertExists($product->image_path);

    $this->get(route('products.edit', $product))
        ->assertOk()
        ->assertSee('Mechanical Keyboard');

    $this->put(route('products.update', $product), [
        'name' => 'Mechanical Keyboard PRO',
        'price' => 179.99,
        'stock' => 7,
        'category_id' => $category->id,
        'description' => 'Updated description.',
    ])->assertRedirect(route('products.index'));

    expect($product->refresh()->name)->toBe('Mechanical Keyboard PRO')
        ->and($product->stock)->toBe(7);

    $this->delete(route('products.destroy', $product))->assertRedirect(route('products.index'));

    expect(Product::find($product->id))->toBeNull();
});

it('validates product creation input', function () {
    $this->post(route('products.store'), [
        'name' => '',
        'price' => -5,
        'stock' => 'not-a-number',
        'category_id' => 99999,
    ])->assertSessionHasErrors(['name', 'price', 'stock', 'category_id']);
});

it('lists and searches categories in the admin panel', function () {
    $electronics = Category::create(['name' => 'Electronics', 'description' => 'Gadgets and gear.']);
    Category::create(['name' => 'Books', 'description' => 'Paper adventures.']);

    $this->get(route('admin.categories.index'))
        ->assertOk()
        ->assertSee('Electronics')
        ->assertSee('Books');

    $this->get(route('admin.categories.index', ['q' => 'electro']))
        ->assertOk()
        ->assertSee('Electronics')
        ->assertDontSee('Paper adventures');

    $this->get(route('categories.create'))->assertOk();
    $this->get(route('categories.edit', $electronics))->assertOk();
});

it('accepts product import uploads into the admin panel', function () {
    Excel::fake();

    $this->post(route('products.import'), [
        'file' => UploadedFile::fake()->create(
            'products.xlsx',
            100,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ),
    ])->assertRedirect(route('admin.products.index'))
        ->assertSessionHas('success', 'Products imported successfully.');

    Excel::assertImported(ProductsImport::class);
});
