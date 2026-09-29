<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    /**
     * Display the admin product management table (Spark Admin UI).
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $search = (string) ($validated['q'] ?? '');

        $products = Product::with('category')
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhereHas('category', function (Builder $query) use ($search) {
                            $query->where('name', 'like', '%'.$search.'%');
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'search' => $search,
        ]);
    }
}
