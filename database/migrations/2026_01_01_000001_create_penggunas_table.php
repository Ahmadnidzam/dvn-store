<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penggunas', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 150)->unique();
            $table->string('password', 255);
            $table->enum('role', ['user', 'developer', 'admin'])->default('user');
            $table->enum('status', ['active', 'blocked'])->default('active');
            $table->timestamp('blocked_at')->nullable();
            $table->string('blocked_reason', 255)->nullable();
            $table->string('kode_unik', 100)->nullable();   // untuk forget password
            $table->string('avatar', 255)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index(['role', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penggunas');
    }
};
