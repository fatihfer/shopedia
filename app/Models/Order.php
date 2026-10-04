<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    public const EXPIRY_HOURS = 24;

    public const UNPAID_STATUSES = ['awaiting_payment', 'awaiting_verification'];

    protected $fillable = [
        'user_id',
        'payment_method_id',
        'total_price',
        'status',
        'shipping_address',
        'payment_proof',
        'paid_at',
        'expired_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function isUnpaid(): bool
    {
        return in_array($this->status, self::UNPAID_STATUSES, true);
    }

    /** Kembalikan stok lalu tandai expired bila sudah lewat batas bayar. */
    public function checkExpiry(): void
    {
        if (! $this->isUnpaid() || ! $this->expired_at || $this->expired_at->isFuture()) {
            return;
        }

        DB::transaction(function () {
            /** @var Order $order */
            $order = self::with('items.product')->whereKey($this->id)->lockForUpdate()->first();

            if (! $order->isUnpaid()) {
                return;
            }

            foreach ($order->items as $item) {
                $item->product?->increment('stock', $item->quantity);
            }

            $order->update(['status' => 'expired']);
        });

        $this->refresh();
    }

    /** Kembalikan stok (dipakai saat cancel/expired). */
    public function restoreStock(): void
    {
        $this->loadMissing('items.product');

        foreach ($this->items as $item) {
            $item->product?->increment('stock', $item->quantity);
        }
    }
}
