<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_affiliate_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('item_id');
            $table->string('product_name');
            $table->string('image_url')->nullable();
            $table->string('product_link');
            $table->string('affiliate_link');
            $table->string('price_min')->nullable();
            $table->string('commission_rate')->nullable();
            $table->string('shop_name')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_affiliate_links');
    }
};
