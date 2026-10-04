<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class ExpireOrders extends Command
{
    protected $signature = 'orders:expire';

    protected $description = 'Tandai expired order yang melewati batas bayar dan kembalikan stoknya.';

    public function handle(): int
    {
        $orders = Order::with('items.product')
            ->whereIn('status', Order::UNPAID_STATUSES)
            ->where('expired_at', '<', now())
            ->get();

        foreach ($orders as $order) {
            $order->checkExpiry();
        }

        $this->info("Expired {$orders->count()} order.");

        return self::SUCCESS;
    }
}
