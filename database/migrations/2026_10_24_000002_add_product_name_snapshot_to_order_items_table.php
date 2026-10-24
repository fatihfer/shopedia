<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Simpan nama produk saat transaksi terjadi, supaya riwayat pesanan
     * tetap menampilkan nama aslinya walau produk sudah dihapus.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('product_id');
        });

        // Isi data lama dari tabel products.
        DB::table('order_items')
            ->whereNull('product_name')
            ->orderBy('id')
            ->chunkById(500, function ($items) {
                $names = DB::table('products')
                    ->whereIn('id', $items->pluck('product_id')->filter()->unique()->values())
                    ->pluck('name', 'id');

                foreach ($items as $item) {
                    if ($item->product_id && isset($names[$item->product_id])) {
                        DB::table('order_items')
                            ->where('id', $item->id)
                            ->update(['product_name' => $names[$item->product_id]]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('product_name');
        });
    }
};
