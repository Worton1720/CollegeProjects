<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        // Если юзера нет (isAuth не прошел)
        if (!$user) {
            return response()->json(['error' => 'Пользователь не найден'], 401);
        }

        // Логика isAdmin: если нужна роль ADMIN, а у юзера её нет
        if ($role === 'ADMIN' && $user->role !== 'ADMIN') {
            return response()->json(['error' => 'Доступ запрещён. Требуются права администратора'], 403);
        }

        // Логика isUser: если юзер GUEST, а нужно быть USER или ADMIN
        if ($role === 'USER' && $user->role === 'GUEST') {
            return response()->json(['error' => 'Доступ запрещён. Требуется роль USER или выше'], 403);
        }

        return $next($request);
    }
}
