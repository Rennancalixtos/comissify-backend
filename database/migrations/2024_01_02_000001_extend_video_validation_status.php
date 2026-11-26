<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->setStatuses(['pending', 'valid', 'invalid', 'normalized', 'queued']);
    }

    public function down(): void
    {
        $this->setStatuses(['pending', 'valid', 'invalid', 'normalized']);
    }

    private function setStatuses(array $statuses): void
    {
        if (DB::getDriverName() === 'mysql') {
            $list = "'" . implode("','", $statuses) . "'";
            DB::statement("ALTER TABLE videos MODIFY COLUMN validation_status ENUM({$list}) NOT NULL DEFAULT 'pending'");
            return;
        }

        // SQLite/Postgres (modo demonstração): o schema builder recria a coluna.
        Schema::table('videos', function (Blueprint $table) use ($statuses) {
            $table->enum('validation_status', $statuses)->default('pending')->change();
        });
    }
};
