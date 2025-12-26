<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    // 1. Создание комментария (аналог createComment)
    public function createComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|min:1',
            'videoId' => 'required|uuid|exists:videos,id',
        ], [
            'text.required' => 'Текст комментария обязателен',
            'videoId.exists' => 'Видео не найдено'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        // Проверка блокировки видео
        $video = Video::findOrFail($request->videoId);
        if ($video->isBlocked) {
            return response()->json(['error' => 'Нельзя комментировать заблокированное видео'], 403);
        }

        // Создание (аналог prisma.comment.create)
        $comment = Comment::create([
            'text' => trim($request->text),
            'userId' => $request->user()->id,
            'videoId' => $request->videoId,
        ]);

        // Подгружаем автора для ответа (аналог include в Prisma)
        $comment->load(['user' => function ($query) {
            $query->select('id', 'email', 'role');
        }]);

        return response()->json([
            'message' => 'Комментарий добавлен',
            'comment' => $comment
        ], 201);
    }

    // 2. Получение комментариев к видео (аналог getCommentsByVideo)
    public function getCommentsByVideo(Request $request, $videoId)
    {
        $limit = $request->query('limit', 20);

        // Laravel делает пагинацию одной командой paginate()
        // Она сама считает total, skip и текущую страницу
        $comments = Comment::where('videoId', $videoId)
            ->with(['user' => function ($query) {
                $query->select('id', 'email', 'role');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return response()->json([
            'comments' => $comments->items(), // Сами данные
            'pagination' => [
                'page' => $comments->currentPage(),
                'limit' => $comments->perPage(),
                'total' => $comments->total(),
                'totalPages' => $comments->lastPage(),
            ],
        ]);
    }

    // 3. Удаление комментария (аналог deleteComment)
    public function deleteComment(Request $request, $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['error' => 'Комментарий не найден'], 404);
        }

        // Проверка прав (аналог if (comment.userId !== req.user.id...))
        $user = $request->user();
        if ($comment->userId !== $user->id && $user->role !== 'ADMIN') {
            return response()->json(['error' => 'Недостаточно прав для удаления комментария'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Комментарий удалён']);
    }
}
