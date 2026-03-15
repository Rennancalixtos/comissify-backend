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
        Schema::table('user_credentials', function (Blueprint $table) {
            $table->string('app_id_hash', 64)->nullable()->unique()->after('app_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_credentials', function (Blueprint $table) {
            $table->dropUnique(['app_id_hash']);
            $table->dropColumn('app_id_hash');
        });
    }
};
