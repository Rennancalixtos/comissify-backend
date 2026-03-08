<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index();
            $table->text('value')->nullable();
            $table->string('description')->nullable()->comment('Human-readable explanation of this setting');
            $table->timestamps();
        });

        // Seed default settings
        DB::table('settings')->insert([
            ['key' => 'discovery.max_products_per_run',   'value' => '50',    'description' => 'Maximum number of products to discover per cron run', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'discovery.min_commission_rate',    'value' => '5',     'description' => 'Minimum commission rate (%) to consider a product', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'discovery.min_rating',             'value' => '4.0',   'description' => 'Minimum product rating (0-5) for inclusion', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'posting.posts_per_account_day',    'value' => '10',    'description' => 'Default daily post limit per account', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'posting.interval_minutes',         'value' => '90',    'description' => 'Minimum minutes between posts from the same account', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'video.max_duration_seconds',       'value' => '60',    'description' => 'Maximum allowed video duration for Shopee upload', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'video.max_file_size_mb',           'value' => '50',    'description' => 'Maximum allowed video file size (MB) for Shopee upload', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ai.title_max_chars',               'value' => '100',   'description' => 'Maximum characters for AI-generated title', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ai.hashtags_count',                'value' => '10',    'description' => 'Number of hashtags to generate per post', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
