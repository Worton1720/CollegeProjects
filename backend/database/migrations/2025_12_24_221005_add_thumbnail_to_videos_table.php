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
        Schema::table('videos', function (Blueprint $table) {
            // Тип обложки: 'auto' - автоматическая, 'frame' - выбранный кадр, 'custom' - загруженная картинка
            $table->enum('thumbnailType', ['auto', 'frame', 'custom'])->default('auto')->after('isBlocked');

            // URL обложки (для custom) или publicId для Cloudinary изображения
            $table->string('thumbnailUrl')->nullable()->after('thumbnailType');

            // Время кадра в секундах (для frame)
            $table->decimal('thumbnailFrameTime', 8, 2)->nullable()->after('thumbnailUrl');

            // Индексация для производительности
            $table->index('thumbnailType');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropIndex(['thumbnailType']);
            $table->dropColumn(['thumbnailType', 'thumbnailUrl', 'thumbnailFrameTime']);
        });
    }
};
