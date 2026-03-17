<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_telegram_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('chat_id');
            $table->string('chat_title');
            $table->text('message_template')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'chat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_telegram_groups');
    }
};
