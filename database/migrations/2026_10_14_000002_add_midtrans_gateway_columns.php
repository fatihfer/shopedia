<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->string('gateway', 20)->default('manual')->after('type');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('gateway_ref', 100)->nullable()->unique()->after('payment_method_id');
            $table->text('qr_string')->nullable()->after('gateway_ref');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['gateway_ref']);
            $table->dropColumn(['gateway_ref', 'qr_string']);
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropColumn('gateway');
        });
    }
};
