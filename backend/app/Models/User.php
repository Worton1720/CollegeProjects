<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Полезно для тестов
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <--- 1. Добавь этот импорт

class User extends Authenticatable
{
    // 2. Добавь HasApiTokens и Notifiable в список use
    use HasApiTokens, HasUuids, HasFactory, Notifiable; 

    protected $fillable = ['email', 'password', 'role'];
    
    protected $hidden = [
        'password',
        'remember_token', // Скрываем служебный токен Laravel
    ];

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class, 'authorId');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'userId');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, 'userId');
    }
}