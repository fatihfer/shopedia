<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const OLD = "'pending','processing','shipped','completed','cancelled'";
    private const NEW = "'awaiting_payment','awaiting_verification','processing','shipped','completed','cancelled','expired'";

    public function up(): void
    {
        if (! Schema::hasTable('payment_methods')) {
            Schema::create('payment_methods', function (Blueprint $table) {
                $table->id();
                $table->string('slug', 50)->unique();
                $table->string('name', 100);
                $table->enum('type', ['qris', 'bank_transfer'])->default('bank_transfer');
                $table->string('account_number', 100)->nullable();
                $table->string('account_name', 100)->nullable();
                $table->string('qr_image', 255)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'payment_method_id')) {
                $table->foreignId('payment_method_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained()
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            }
            foreach (['payment_proof', 'paid_at', 'expired_at', 'rejection_reason'] as $column) {
                if (! Schema::hasColumn('orders', $column)) {
                    if ($column === 'payment_proof') {
                        $table->string($column, 255)->nullable()->after('shipping_address');
                    } elseif ($column === 'rejection_reason') {
                        $table->string($column, 500)->nullable()->after('expired_at');
                    } else {
                        $table->timestamp($column)->nullable()->after('payment_proof');
                    }
                }
            }
        });

        // Perluas enum dulu (gabungan lama+baru), migrasi data, baru kunci ke enum baru.
        // Khusus MySQL — SQLite (testing) menyimpan enum sebagai varchar biasa.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE orders MODIFY status ENUM(' . self::OLD . ',' . "'awaiting_payment','awaiting_verification','expired'" . ') NOT NULL');
        }
        DB::table('orders')->where('status', 'pending')->update(['status' => 'awaiting_payment']);
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE orders MODIFY status ENUM(' . self::NEW . ") NOT NULL DEFAULT 'awaiting_payment'");
        }
    }

    public function down(): void
    {
        DB::table('orders')->whereIn('status', ['awaiting_payment', 'awaiting_verification', 'expired'])->update(['status' => 'pending']);
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE orders MODIFY status ENUM(' . self::OLD . ") NOT NULL DEFAULT 'pending'");
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_method_id');
            $table->dropColumn(['payment_proof', 'paid_at', 'expired_at', 'rejection_reason']);
        });

        Schema::dropIfExists('payment_methods');
    }
};
