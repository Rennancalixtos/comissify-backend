<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE user_posts MODIFY COLUMN type ENUM('text','image','video','pinterest','telegram','tiktok','tiktok_shop','fb_adlibrary') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE user_posts MODIFY COLUMN type ENUM('text','image','video','pinterest','telegram') NOT NULL");
    }
};
