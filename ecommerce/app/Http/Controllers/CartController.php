<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        return view('cart.index', [
            'cart' => $cart,
        ]);
    }

    public function add(Product $product)
    {
    $cart = session()->get('cart', []);
    $quantity = $cart[$product->id]['quantity'] ?? 0;
    if ($quantity >= $product->stock) {
        return redirect()->route('products.index')
            ->with('error', 'Not enough stock available.');
    }
    if (isset($cart[$product->id])) {
        $cart[$product->id]['quantity']++;
    } else {
        $cart[$product->id] = [
            'name' => $product->name,
            'price' => $product->price,
            'image_path' => $product->image_path,
            'quantity' => 1,
        ];
    }
    session()->put('cart', $cart);
    return redirect()->route('cart.index');
    }


    public function update(Request $request, $id)
    {
    $request->validate([
    'quantity' => 'required|integer|min:1',
    ]);

    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
        $product = Product::findOrFail($id);

        if ($request->quantity > $product->stock) {
            return redirect()->route('cart.index')
                ->with('error', 'Not enough stock available.');
        }

        $cart[$id]['quantity'] = $request->quantity;
    }

    session()->put('cart', $cart);

    return redirect()->route('cart.index');

    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index');
    }
}
