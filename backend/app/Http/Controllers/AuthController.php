<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // 1. Регистрация (аналог register)
    public function register(Request $request)
    {
        // Валидация (в Laravel это делается одной командой)
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'nullable|string'
        ], [
            'email.unique' => 'Пользователь с таким email уже существует',
            'password.min' => 'Пароль должен быть не менее 6 символов'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        // Создание пользователя (аналог prisma.user.create)
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password), // аналог bcrypt.hash
            'role' => $request->role ?? 'USER',
        ]);

        // Генерация токена (аналог generateToken)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Регистрация успешна',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'createdAt' => $user->created_at,
            ],
        ], 201);
    }

    // 2. Вход (аналог login)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Поиск юзера (аналог findUnique)
        $user = User::where('email', $request->email)->first();

        // Проверка пароля (аналог bcrypt.compare)
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Неверный email или пароль'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Вход выполнен успешно',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    // 3. Текущий пользователь (аналог getMe)
    public function getMe(Request $request)
    {
        // $request->user() уже содержит текущего юзера благодаря middleware auth:sanctum
        $user = $request->user();

        // Добавляем подсчет связей (аналог _count в Prisma)
        $user->loadCount(['videos', 'comments', 'likes']);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'createdAt' => $user->created_at,
                '_count' => [
                    'videos' => $user->videos_count,
                    'comments' => $user->comments_count,
                    'likes' => $user->likes_count,
                ]
            ]
        ]);
    }
}
