<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('check_role:USER')->post('/', [CommentController::class, 'createComment']);
    
    Route::delete('/{id}', [CommentController::class, 'deleteComment']);
});

Route::get('/video/{videoId}', [CommentController::class, 'getCommentsByVideo']);