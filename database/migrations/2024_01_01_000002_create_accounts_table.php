<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('shopee_username')->unique();
            $table->text('session_data')->nullable()->comment('Encrypted Appium session data (cookies, tokens)');
            $table->unsignedSmallInteger('daily_post_limit')->default(10)->comment('Max posts per day for this account');
            $table->unsignedSmallInteger('posts_today')->default(0);
            $table->timestamp('last_post_at')->nullable();
            $table->enum('status', ['active', 'inactive', 'banned', 'cooldown'])->default('active')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
