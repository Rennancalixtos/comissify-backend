<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: alter enum to add 'queued' status
        DB::statement("ALTER TABLE videos MODIFY COLUMN validation_status ENUM('pending','valid','invalid','normalized','queued') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE videos MODIFY COLUMN validation_status ENUM('pending','valid','invalid','normalized') NOT NULL DEFAULT 'pending'");
    }
};
