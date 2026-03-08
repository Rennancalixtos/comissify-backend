<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('shopee_product_id')->unique()->index();
            $table->string('name');
            $table->string('category')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(0)->comment('Percentage 0-100');
            $table->decimal('rating', 3, 2)->nullable()->comment('0.00 - 5.00');
            $table->unsignedBigInteger('sales_count')->default(0);
            $table->decimal('opportunity_score', 6, 2)->default(0)->comment('Calculated score for prioritization');
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending')->index();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
