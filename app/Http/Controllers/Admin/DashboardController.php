<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $paidStatuses = ['processing', 'shipped', 'completed'];

        $revenue30 = Order::whereIn('status', $paidStatuses)
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('total_price');
        $orders30 = Order::where('created_at', '>=', now()->subDays(30))->count();

        $salesWeek = Order::selectRaw('DATE(created_at) as day, SUM(total_price) as revenue, COUNT(*) as orders')
            ->whereIn('status', $paidStatuses)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $week = collect(range(6, 0))->map(function ($ago) use ($salesWeek) {
            $date = now()->subDays($ago);
            $row = $salesWeek->get($date->format('Y-m-d'));

            return [
                'label' => $date->format('d M'),
                'revenue' => (float) ($row->revenue ?? 0),
                'orders' => (int) ($row->orders ?? 0),
            ];
        });

        $maxRevenue = max(1, $week->max('revenue'));

        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as sold'))
            ->whereHas('order', fn ($q) => $q->whereIn('status', $paidStatuses))
            ->with('product.category')
            ->groupBy('product_id')
            ->orderByDesc('sold')
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'totalRevenue' => Order::whereIn('status', $paidStatuses)->sum('total_price'),
            'totalOrders' => Order::count(),
            'pendingOrders' => Order::whereIn('status', Order::UNPAID_STATUSES)->count(),
            'totalProducts' => Product::count(),
            'totalCustomers' => User::where('role', 'customer')->count(),
            'revenue30' => $revenue30,
            'orders30' => $orders30,
            'week' => $week,
            'maxRevenue' => $maxRevenue,
            'topProducts' => $topProducts,
            'recentOrders' => Order::with('user')->latest()->take(8)->get(),
            'lowStock' => Product::orderBy('stock')->take(8)->get(),
        ]);
    }
}
