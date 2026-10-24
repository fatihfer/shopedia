<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keranjang session-based: tabel carts/cart_items tidak terpakai.
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['gateway_ref']);
            $table->dropColumn(['gateway_ref', 'qr_image_url']);
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropColumn(['gateway', 'qr_image']);
        });
    }

    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->string('gateway', 20)->default('manual')->after('type');
            $table->string('qr_image', 255)->nullable()->after('account_name');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('gateway_ref', 100)->nullable()->unique()->after('payment_method_id');
            $table->string('qr_image_url', 500)->nullable()->after('qr_string');
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('product_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
            $table->unique(['cart_id', 'product_id']);
        });
    }
};
