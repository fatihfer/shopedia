<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Kunci semua URL (route, redirect, asset) ke APP_URL agar konsisten
        // baik diakses via localhost, ngrok, maupun domain produksi.
        $appUrl = (string) config('app.url');

        if ($appUrl !== '') {
            URL::forceRootUrl($appUrl);
        }

        // Ikuti skema APP_URL (penting saat diakses via tunnel HTTPS seperti ngrok)
        if (str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        }

        // Data footer di semua halaman toko (tabel kecil, aman di-cache per request).
        View::composer('layouts.app', function ($view) {
            $view->with('footerCategories', Category::orderBy('name')->take(6)->get());
            $view->with('footerPayments', PaymentMethod::where('is_active', true)->orderBy('name')->get());
        });

        // Badge pesanan perlu verifikasi di sidebar admin.
        View::composer('layouts.admin', function ($view) {
            $view->with('adminPendingCount', Order::where('status', 'awaiting_verification')->count());
        });
    }
}
