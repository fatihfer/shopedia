<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Produk yang sudah pernah dipesan tidak boleh memblokir penghapusan.
     * Saat produk dihapus, product_id di order_items di-set NULL supaya
     * riwayat pesanan (qty + harga saat transaksi) tetap utuh.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->change();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        // product_id NULL tidak bisa dikembalikan ke NOT NULL tanpa
        // menghapus riwayat pesanan, jadi kolom dibiarkan nullable.
        if (! DB::table('order_items')->whereNull('product_id')->exists()) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->unsignedBigInteger('product_id')->nullable(false)->change();
            });
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }
};
