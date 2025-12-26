<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LikeController;

Route::middleware(['auth:sanctum', 'check_role:USER'])->post('/', [LikeController::class, 'toggleLike']);

Route::get('/{videoId}', [LikeController::class, 'getLikeStats']);