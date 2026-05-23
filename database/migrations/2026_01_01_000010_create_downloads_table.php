<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('penggunas')->cascadeOnDelete();
            $table->foreignId('platform_id')->constrained('platforms')->cascadeOnDelete();
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksis')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'platform_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
