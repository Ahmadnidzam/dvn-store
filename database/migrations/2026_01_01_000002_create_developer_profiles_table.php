<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('developer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('penggunas')->cascadeOnDelete();
            $table->string('nama_studio', 150);
            $table->text('deskripsi')->nullable();
            $table->string('website', 255)->nullable();
            $table->string('bank_name', 100)->nullable();           // mis. BCA, BRI, Mandiri
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_account_holder', 150)->nullable();
            $table->timestamps();

            $table->unique('pengguna_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('developer_profiles');
    }
};
