<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('affiliate_link_id')->constrained('user_affiliate_links')->cascadeOnDelete();
            $table->enum('type', ['text', 'image', 'video', 'pinterest']);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->decimal('credits_charged', 8, 4)->default(0);
            $table->json('result')->nullable();  // { text, image_url, video_url, pins: [...] }
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_posts');
    }
};
