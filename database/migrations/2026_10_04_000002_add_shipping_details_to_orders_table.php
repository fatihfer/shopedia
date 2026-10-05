<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('recipient_name', 100)->nullable()->after('user_id');
            $table->string('recipient_phone', 20)->nullable()->after('recipient_name');
            $table->string('courier', 50)->nullable()->after('shipping_address');
            $table->string('shipping_service', 50)->nullable()->after('courier');
            $table->decimal('shipping_cost', 12, 2)->default(0)->after('shipping_service');
            $table->decimal('latitude', 10, 7)->nullable()->after('shipping_cost');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'recipient_name',
                'recipient_phone',
                'courier',
                'shipping_service',
                'shipping_cost',
                'latitude',
                'longitude',
            ]);
        });
    }
};
