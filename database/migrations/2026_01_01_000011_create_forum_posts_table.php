<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('penggunas')->cascadeOnDelete();
            $table->text('content');
            $table->unsignedInteger('helpful_count')->default(0);
            $table->boolean('is_hidden')->default(false);   // dimoderasi admin
            $table->timestamps();

            $table->index('created_at');
        });

        Schema::create('forum_post_helpfuls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('forum_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('penggunas')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['post_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_post_helpfuls');
        Schema::dropIfExists('forum_posts');
    }
};
