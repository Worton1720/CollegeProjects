<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasUuids; // Позволяет Laravel автоматически создавать UUID для новых комментариев

    // Указываем, какие поля можно заполнять массово (как в Prisma create)
    protected $fillable = [
        'text',
        'userId',
        'videoId'
    ];

    // Связь: Комментарий ПРИНАДЛЕЖИТ пользователю
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }

    // Связь: Комментарий ПРИНАДЛЕЖИТ видео
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, 'videoId');
    }
}