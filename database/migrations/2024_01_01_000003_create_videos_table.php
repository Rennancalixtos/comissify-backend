<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('pinterest_url')->nullable()->comment('Original Pinterest video URL');
            $table->string('local_path')->nullable()->comment('Relative path inside storage/app/videos');
            $table->unsignedSmallInteger('duration_seconds')->nullable();
            $table->string('resolution')->nullable()->comment('e.g. 1280x720');
            $table->decimal('file_size_mb', 8, 2)->nullable();
            $table->string('codec')->nullable()->comment('e.g. h264');
            $table->enum('validation_status', ['pending', 'valid', 'invalid', 'normalized'])
                  ->default('pending')->index();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
