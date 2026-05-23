<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dev_id')->constrained('penggunas')->cascadeOnDelete();
            $table->enum('category', ['app', 'game']);
            $table->string('nama_platform', 200);
            $table->string('slug', 220)->unique();
            $table->string('genre', 100);
            $table->unsignedInteger('harga')->default(0);          // Rupiah
            $table->double('rating', 3, 2)->default(0);
            $table->string('icon', 255)->nullable();
            $table->text('deskripsi');
            $table->string('cuplikan', 255)->nullable();           // video preview
            $table->string('file_path', 255)->nullable();          // file APK/EXE/ZIP
            $table->unsignedBigInteger('file_size')->default(0);   // bytes
            $table->enum('scan_status', ['pending', 'scanning', 'clean', 'infected', 'error'])->default('pending');
            $table->json('scan_result')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_taken_down')->default(false);
            $table->timestamp('taken_down_at')->nullable();
            $table->string('taken_down_reason', 255)->nullable();
            $table->unsignedBigInteger('upload_fee_transaksi_id')->nullable();  // referensi transaksi upload fee
            $table->timestamps();

            $table->index(['category', 'is_published', 'is_taken_down']);
            $table->index('dev_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platforms');
    }
};
