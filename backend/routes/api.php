<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'VideoHub API is running',
        'timestamp' => now()->toISOString(),
    ]);
});

// Видео из локального хранилища с CORS заголовками
Route::get('/videos/file/{filename}', function ($filename) {
    $path = storage_path('app/public/videos/' . $filename);

    if (!file_exists($path)) {
        return response()->json(['error' => 'Video not found'], 404);
    }

    return response()->file($path, [
        'Content-Type' => 'video/mp4',
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
    ]);
})->name('video.file');

// Обложки видео из локального хранилища с CORS заголовками
Route::get('/thumbnails/file/{filename}', function ($filename) {
    $path = storage_path('app/public/thumbnails/' . $filename);

    if (!file_exists($path)) {
        return response()->json(['error' => 'Thumbnail not found'], 404);
    }

    return response()->file($path, [
        'Content-Type' => 'image/jpeg',
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
    ]);
})->name('thumbnail.file');

Route::prefix('auth')->group(base_path('routes/auth.php'));
Route::prefix('videos')->group(base_path('routes/videos.php'));
Route::prefix('comments')->group(base_path('routes/comments.php'));
Route::prefix('likes')->group(base_path('routes/likes.php'));