<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalRevenue' => Order::whereNotIn('status', ['cancelled'])->sum('total_price'),
            'totalOrders' => Order::count(),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'totalProducts' => Product::count(),
            'totalCustomers' => User::where('role', 'customer')->count(),
            'recentOrders' => Order::with('user')->latest()->take(8)->get(),
            'lowStock' => Product::orderBy('stock')->take(8)->get(),
        ]);
    }
}
