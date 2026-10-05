<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->string('channel', 20)->default('whatsapp');
            $table->string('recipient', 30);
            $table->text('message');
            $table->string('status', 20)->default('opened');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
