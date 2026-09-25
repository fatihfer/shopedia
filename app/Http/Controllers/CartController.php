<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /** @return array<int, int> product_id => qty */
    public static function cart(): array
    {
        return session()->get('cart', []);
    }

    public static function cartCount(): int
    {
        return array_sum(session()->get('cart', []));
    }

    public function index(): View
    {
        $cart = self::cart();
        $products = $cart
            ? Product::with('category')->whereIn('id', array_keys($cart))->get()
            : collect();

        $items = $products->map(fn (Product $p) => [
            'product' => $p,
            'quantity' => $cart[$p->id],
            'subtotal' => $p->price * $cart[$p->id],
        ]);

        $total = $items->sum('subtotal');

        return view('cart.index', compact('items', 'total'));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:' . max(1, $product->stock)],
        ]);

        $qty = $validated['quantity'] ?? 1;

        if ($product->stock < 1) {
            return back()->with('error', 'Stok produk habis.');
        }

        $cart = self::cart();
        $newQty = ($cart[$product->id] ?? 0) + $qty;

        if ($newQty > $product->stock) {
            return back()->with('error', "Stok tidak cukup. Sisa {$product->stock}.");
        }

        $cart[$product->id] = $newQty;
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', "{$product->name} masuk keranjang.");
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:' . max(0, $product->stock)],
        ]);

        $cart = self::cart();

        if ($validated['quantity'] === 0) {
            unset($cart[$product->id]);
        } else {
            if ($validated['quantity'] > $product->stock) {
                return back()->with('error', "Stok tidak cukup. Sisa {$product->stock}.");
            }
            $cart[$product->id] = $validated['quantity'];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $cart = self::cart();
        unset($cart[$product->id]);
        session()->put('cart', $cart);

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public function clear(): RedirectResponse
    {
        session()->forget('cart');

        return back()->with('success', 'Keranjang dikosongkan.');
    }
}
