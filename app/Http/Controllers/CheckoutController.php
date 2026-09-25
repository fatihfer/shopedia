<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        $products = Product::whereIn('id', array_keys($cart))->get();
        $items = $products->map(fn (Product $p) => [
            'product' => $p,
            'quantity' => $cart[$p->id],
            'subtotal' => $p->price * $cart[$p->id],
        ]);
        $total = $items->sum('subtotal');

        return view('checkout.index', compact('items', 'total'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shipping_address' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        return DB::transaction(function () use ($cart, $validated, $request) {
            $products = Product::whereIn('id', array_keys($cart))->lockForUpdate()->get()->keyBy('id');

            $total = 0;
            foreach ($cart as $productId => $qty) {
                $product = $products->get($productId);
                if (! $product) {
                    continue;
                }
                if ($product->stock < $qty) {
                    throw new \Exception("Stok {$product->name} tidak cukup. Sisa {$product->stock}.");
                }
                $total += $product->price * $qty;
            }

            /** @var Order $order */
            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_price' => $total,
                'status' => 'pending',
                'shipping_address' => $validated['shipping_address'],
            ]);

            foreach ($cart as $productId => $qty) {
                $product = $products->get($productId);
                if (! $product) {
                    continue;
                }

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $product->price,
                ]);

                $product->decrement('stock', $qty);
            }

            session()->forget('cart');

            return redirect()->route('orders.show', $order)->with('success', 'Pesanan berhasil dibuat!');
        });
    }
}
