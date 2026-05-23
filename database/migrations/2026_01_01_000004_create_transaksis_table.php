<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('penggunas')->cascadeOnDelete();
            $table->foreignId('platform_id')->nullable()->constrained('platforms')->nullOnDelete();
            $table->enum('tipe', ['purchase', 'upload_fee']);
            $table->unsignedInteger('amount');             // total bayar customer/dev
            $table->unsignedInteger('platform_fee')->default(0);    // 10% untuk purchase, full untuk upload_fee
            $table->unsignedInteger('net_amount')->default(0);      // yang masuk ke wallet developer
            $table->string('metode', 50)->nullable();      // diisi setelah Midtrans callback
            $table->string('kode_transaksi', 100)->unique();
            $table->string('midtrans_order_id', 100)->nullable()->unique();
            $table->string('snap_token', 255)->nullable();
            $table->enum('status', ['pending', 'paid', 'failed', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->json('midtrans_response')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('tipe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
