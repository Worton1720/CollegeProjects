<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;

Route::get('/', [VideoController::class, 'getVideos']);
Route::get('/{id}', [VideoController::class, 'getVideoById']);

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('check_role:USER')->post('/', [VideoController::class, 'uploadVideo']);
    
    Route::delete('/{id}', [VideoController::class, 'deleteVideo']);

    Route::middleware('check_role:ADMIN')->patch('/{id}/block', [VideoController::class, 'toggleBlockVideo']);
});