<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    public function index(): View
    {
        $methods = PaymentMethod::withCount('orders')->orderBy('name')->get();

        return view('admin.payment-methods.index', compact('methods'));
    }

    public function create(): View
    {
        return view('admin.payment-methods.form', ['method' => new PaymentMethod(['is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateMethod($request);
        $validated['slug'] = $this->uniqueSlug($validated['name']);

        if ($request->hasFile('qr_image')) {
            $validated['qr_image'] = $request->file('qr_image')->store('payment-methods', 'public');
        }

        PaymentMethod::create($validated);

        return redirect()->route('admin.payment-methods.index')->with('success', 'Metode pembayaran dibuat.');
    }

    public function edit(PaymentMethod $paymentMethod): View
    {
        return view('admin.payment-methods.form', ['method' => $paymentMethod]);
    }

    public function update(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $validated = $this->validateMethod($request);

        if ($validated['name'] !== $paymentMethod->name) {
            $validated['slug'] = $this->uniqueSlug($validated['name'], $paymentMethod->id);
        }

        if ($request->hasFile('qr_image')) {
            if ($paymentMethod->qr_image) {
                Storage::disk('public')->delete($paymentMethod->qr_image);
            }
            $validated['qr_image'] = $request->file('qr_image')->store('payment-methods', 'public');
        }

        $paymentMethod->update($validated);

        return redirect()->route('admin.payment-methods.index')->with('success', 'Metode pembayaran diperbarui.');
    }

    public function destroy(PaymentMethod $paymentMethod): RedirectResponse
    {
        if ($paymentMethod->qr_image) {
            Storage::disk('public')->delete($paymentMethod->qr_image);
        }

        $paymentMethod->delete();

        return back()->with('success', 'Metode pembayaran dihapus. Order lama tetap tersimpan (metodenya jadi "-").');
    }

    /** @return array<string, mixed> */
    private function validateMethod(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:qris,bank_transfer'],
            'gateway' => ['nullable', 'in:manual,midtrans'],
            'account_number' => ['nullable', 'required_if:type,bank_transfer', 'string', 'max:100'],
            'account_name' => ['nullable', 'string', 'max:100'],
            'qr_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Transfer bank selalu manual; QRIS default otomatis Midtrans.
        $validated['gateway'] = $validated['type'] === 'qris'
            ? ($validated['gateway'] ?? 'midtrans')
            : 'manual';

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: Str::random(8);
        $slug = $base;
        $i = 2;

        while (
            PaymentMethod::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
