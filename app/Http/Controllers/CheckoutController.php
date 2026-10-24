<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(Request $request): View|RedirectResponse
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

        $methods = PaymentMethod::where('is_active', true)->orderBy('name')->get();
        $couriers = config('shipping.couriers');
        $services = config('shipping.services');
        $map = config('shipping.map_default');

        return view('checkout.index', compact('items', 'total', 'methods', 'couriers', 'services', 'map'));
    }

    public function store(Request $request): RedirectResponse
    {
        $couriers = array_keys(config('shipping.couriers'));
        $services = config('shipping.services');

        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:100'],
            'recipient_phone' => ['required', 'string', 'max:20', 'regex:/^(\+62|62|0)8\d{7,11}$/'],
            'shipping_address' => ['required', 'string', 'min:10', 'max:500'],
            'courier' => ['required', Rule::in($couriers)],
            'shipping_service' => ['required', Rule::in(array_keys($services))],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'save_profile' => ['sometimes', 'boolean'],
            'payment_method_id' => [
                'required',
                Rule::exists('payment_methods', 'id')->where('is_active', true),
            ],
        ]);

        // Layanan harus tersedia untuk kurir yang dipilih (ongkir diambil server-side).
        $service = $services[$validated['shipping_service']];
        if (! in_array($validated['courier'], $service['couriers'], true)) {
            return back()
                ->withErrors(['shipping_service' => 'Layanan tidak tersedia untuk kurir tersebut.'])
                ->withInput();
        }
        $shippingCost = $service['cost'];

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        $method = PaymentMethod::findOrFail($validated['payment_method_id']);

        /** @var Order $order */
        $order = DB::transaction(function () use ($cart, $validated, $shippingCost, $request) {            $products = Product::whereIn('id', array_keys($cart))->lockForUpdate()->get()->keyBy('id');

            $itemsTotal = 0;
            foreach ($cart as $productId => $qty) {
                $product = $products->get($productId);
                if (! $product) {
                    continue;
                }
                if ($product->stock < $qty) {
                    throw new \Exception("Stok {$product->name} tidak cukup. Sisa {$product->stock}.");
                }
                $itemsTotal += $product->price * $qty;
            }

            /** @var Order $order */
            $order = Order::create([
                'user_id' => $request->user()->id,
                'payment_method_id' => $validated['payment_method_id'],
                'total_price' => $itemsTotal + $shippingCost,
                'status' => 'awaiting_payment',
                'recipient_name' => $validated['recipient_name'],
                'recipient_phone' => $validated['recipient_phone'],
                'shipping_address' => $validated['shipping_address'],
                'courier' => $validated['courier'],
                'shipping_service' => $validated['shipping_service'],
                'shipping_cost' => $shippingCost,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'expired_at' => now()->addHours(Order::EXPIRY_HOURS),
            ]);

            foreach ($cart as $productId => $qty) {
                $product = $products->get($productId);
                if (! $product) {
                    continue;
                }

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $qty,
                    'price' => $product->price,
                ]);

                $product->decrement('stock', $qty);
            }

            session()->forget('cart');

            return $order;
        });

        // QRIS: QR berisi link bayar (dibuat setelah order tersimpan).
        if ($method->isQris()) {
            $token = Str::random(32);
            while (Order::where('pay_token', $token)->exists()) {
                $token = Str::random(32);
            }

            $order->update([
                'pay_token' => $token,
                'qr_string' => route('pay.show', ['token' => $token]),
                'expired_at' => now()->addMinutes(Order::PAYLINK_EXPIRY_MINUTES),
            ]);
        }

        // Simpan ke profil bila dicentang (biar checkout berikutnya terisi otomatis).
        if ($request->boolean('save_profile')) {
            $request->user()->update([
                'name' => $validated['recipient_name'],
                'phone' => $validated['recipient_phone'],
                'address' => $validated['shipping_address'],
            ]);
        }

        return redirect()->route('orders.show', $order)->with('success', 'Pesanan berhasil dibuat! Lanjutkan ke pembayaran.');
    }
}
