<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Таблица Юзеров
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['GUEST', 'USER', 'ADMIN'])->default('USER');
            $table->timestamps(); // Это создаст created_at и updated_at
        });

        // Таблица Видео
        Schema::create('videos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('url');
            $table->string('publicId');
            $table->boolean('isBlocked')->default(false);
            $table->foreignUuid('authorId')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('authorId');
            $table->index('isBlocked');
        });

        // Таблица Комментариев
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('text');
            $table->foreignUuid('userId')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('videoId')->constrained('videos')->onDelete('cascade');
            $table->timestamps();

            $table->index(['videoId', 'userId']);
        });

        // Таблица Лайков
        Schema::create('likes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('isLike'); // true = like, false = dislike
            $table->foreignUuid('userId')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('videoId')->constrained('videos')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['userId', 'videoId']); // Один пользователь - одна реакция
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('videos');
        Schema::dropIfExists('users');
    }
};
