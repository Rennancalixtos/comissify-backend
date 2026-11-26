<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->setTypes(['text', 'image', 'video', 'pinterest', 'telegram', 'tiktok', 'tiktok_shop', 'fb_adlibrary']);
    }

    public function down(): void
    {
        $this->setTypes(['text', 'image', 'video', 'pinterest', 'telegram']);
    }

    private function setTypes(array $types): void
    {
        if (DB::getDriverName() === 'mysql') {
            $list = "'" . implode("','", $types) . "'";
            DB::statement("ALTER TABLE user_posts MODIFY COLUMN type ENUM({$list}) NOT NULL");
            return;
        }

        // SQLite/Postgres (modo demonstração): o schema builder recria a coluna.
        Schema::table('user_posts', function (Blueprint $table) use ($types) {
            $table->enum('type', $types)->change();
        });
    }
};
