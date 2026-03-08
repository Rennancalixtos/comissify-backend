<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('hashtags')->nullable()->comment('Array of hashtag strings, e.g. ["#shopee","#promo"]');
            $table->string('affiliate_link')->nullable()->comment('Shortened Shopee affiliate URL');
            $table->unsignedTinyInteger('priority')->default(5)->comment('1=highest, 10=lowest');
            $table->enum('status', ['pending', 'processing', 'posted', 'failed', 'cancelled'])
                  ->default('pending')->index();
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamp('posted_at')->nullable();
            $table->string('shopee_video_id')->nullable()->comment('Video ID returned by Shopee after posting');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_queue');
    }
};
