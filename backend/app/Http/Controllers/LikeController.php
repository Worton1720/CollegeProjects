<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LikeController extends Controller
{
    // 1. Добавление или изменение реакции (аналог toggleLike)
    public function toggleLike(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'videoId' => 'required|uuid|exists:videos,id',
            'isLike' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $videoId = $request->videoId;
        $isLike = $request->isLike;
        $userId = $request->user()->id;

        // Проверка блокировки видео
        $video = Video::findOrFail($videoId);
        if ($video->isBlocked) {
            return response()->json(['error' => 'Нельзя реагировать на заблокированное видео'], 403);
        }

        // Ищем существующую реакцию
        $existingLike = Like::where('userId', $userId)
                            ->where('videoId', $videoId)
                            ->first();

        $message = "";
        $like = null;

        if ($existingLike) {
            if ($existingLike->isLike === $isLike) {
                // Если нажали ту же кнопку — удаляем
                $existingLike->delete();
                $message = "Реакция удалена";
            } else {
                // Если нажали другую кнопку — обновляем
                $existingLike->update(['isLike' => $isLike]);
                $message = $isLike ? "Изменено на лайк" : "Изменено на дизлайк";
                $like = $existingLike;
            }
        } else {
            // Создаем новую реакцию
            $like = Like::create([
                'userId' => $userId,
                'videoId' => $videoId,
                'isLike' => $isLike
            ]);
            $message = $isLike ? "Лайк добавлен" : "Дизлайк добавлен";
        }

        return response()->json([
            'message' => $message,
            'like' => $like,
            'stats' => $this->getStats($videoId)
        ]);
    }

    // 2. Получение статистики (аналог getLikeStats)
    public function getLikeStats(Request $request, $videoId)
    {
        $userLike = null;
        
        // В Laravel sanctum, если маршрут не защищен middleware auth, 
        // мы можем проверить юзера вручную через 'sanctum' guard
        $user = auth('sanctum')->user();
        
        if ($user) {
            $like = Like::where('userId', $user->id)
                        ->where('videoId', $videoId)
                        ->first();
            $userLike = $like ? $like->isLike : null;
        }

        return response()->json([
            'stats' => $this->getStats($videoId),
            'userLike' => $userLike
        ]);
    }

    // Вспомогательный метод для подсчета (чтобы не дублировать код)
    private function getStats($videoId)
    {
        return [
            'likes' => Like::where('videoId', $videoId)->where('isLike', true)->count(),
            'dislikes' => Like::where('videoId', $videoId)->where('isLike', false)->count(),
        ];
    }
}