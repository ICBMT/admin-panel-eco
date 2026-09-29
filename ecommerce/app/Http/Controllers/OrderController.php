<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Events\OrderPlaced;
use Stripe\StripeClient;


class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())->latest()->paginate(10);
        return view('orders.index', [
            'orders' => $orders,
        ]);
    }
    public function checkout(Request $request)
    {
            $request->validate([
            'payment_method' => ['required', 'in:cod,stripe'],
        ]);

        $paymentMethod = $request->payment_method;
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        foreach ($cart as $productId => $item) {
            $product = Product::findOrFail($productId);

            if ($product->stock < $item['quantity']) {
                return redirect()->route('cart.index')
                    ->with('error', 'Not enough stock for ' . $product->name);
            }
        }
        if ($paymentMethod === 'stripe') {

            $stripe = new StripeClient(config('services.stripe.secret'));

            $order = DB::transaction(function () use ($cart, $total, $paymentMethod) {
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'total' => $total,
                    'status' => 'pending',
                    'payment_method' => $paymentMethod,
                    'payment_status' => 'pending',
                ]);
                $order->statusHistory()->create([
                    'status' => 'pending',
                ]);

                foreach ($cart as $productId => $item) {
                    $product = Product::findOrFail($productId);

                    $order->items()->create([
                        'product_id' => $productId,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }

                return $order;
            });

            $session = $stripe->checkout->sessions->create([
                'mode' => 'payment',

                'line_items' => collect($cart)->map(function ($item) {
                    return [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => $item['name'],
                            ],
                            'unit_amount' => (int) round($item['price'] * 100),
                        ],
                        'quantity' => $item['quantity'],
                    ];
                })->values()->all(),

                'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.cancel'),

                'metadata' => [
                    'order_id' => (string) $order->id,
                ],
            ]);

            return redirect($session->url);


        }


            $order = DB::transaction(function () use ($cart, $total, $paymentMethod) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'total' => $total,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
            ]);

            $order->statusHistory()->create([
                'status' => 'pending',
            ]);
            foreach ($cart as $productId => $item) {
                $product = Product::findOrFail($productId);

                $order->items()->create([
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
                $product->decrement('stock', $item['quantity']);
            }
            return $order;
        });
        event(new OrderPlaced($order));
        session()->forget('cart');
        return redirect()->route('orders.index')
            ->with('success', 'Order placed successfully.');
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load('items.product');

        return view('orders.show', [
            'order' => $order,
        ]);
    }

    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('cart.index')
                ->with('error', 'Invalid Stripe payment session.');
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        $session = $stripe->checkout->sessions->retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            return redirect()->route('cart.index')
                ->with('error', 'Payment was not completed.');
        }

        $order = Order::findOrFail($session->metadata->order_id);

        abort_unless($order->user_id === auth()->id(), 403);

        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.index')
                ->with('success', 'This order has already been paid.');
        }


        DB::transaction(function () use ($order) {

            $order->load('items');

            foreach ($order->items as $item) {
                $product = Product::findOrFail($item->product_id);

                if ($product->stock < $item->quantity) {
                    throw new \RuntimeException(
                        'Not enough stock for ' . $product->name
                    );
                }
            }

            foreach ($order->items as $item) {
                $product = Product::findOrFail($item->product_id);

                $product->decrement('stock', $item->quantity);
            }

            $order->update([
                'payment_status' => 'paid',
            ]);
        });

        event(new OrderPlaced($order));

        session()->forget('cart');

        return redirect()->route('orders.index')
            ->with('success', 'Payment successful and order confirmed.');

    }

    public function stripeCancel()
    {
        return redirect()->route('cart.index')
            ->with('error', 'Stripe payment was cancelled.');
    }


}
