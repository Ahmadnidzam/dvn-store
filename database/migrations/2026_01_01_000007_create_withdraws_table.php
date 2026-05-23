<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdraws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dev_id')->constrained('penggunas')->cascadeOnDelete();
            $table->unsignedBigInteger('amount');
            $table->json('bank_snapshot');                          // bank_name, account_number, holder (snapshot saat request)
            $table->string('iris_payout_reference_no', 100)->nullable();
            $table->enum('status', ['pending', 'processing', 'success', 'failed', 'rejected'])->default('pending');
            $table->string('failure_reason', 255)->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->json('iris_response')->nullable();
            $table->timestamps();

            $table->index(['dev_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdraws');
    }
};
