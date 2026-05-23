<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained('platforms')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('penggunas')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');     // 1-5
            $table->text('komentar');
            $table->unsignedInteger('helpful_count')->default(0);
            $table->timestamps();

            $table->unique(['platform_id', 'user_id']); // 1 review / user / produk
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
