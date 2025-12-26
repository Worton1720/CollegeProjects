<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{
    use HasUuids;

    protected $fillable = [
        'title',
        'description',
        'url',
        'publicId',
        'isBlocked',
        'authorId',
        'thumbnailType',
        'thumbnailUrl',
        'thumbnailFrameTime'
    ];

    // Автоматически добавлять accessor в JSON
    protected $appends = ['thumbnail'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorId');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'videoId');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, 'videoId');
    }

    /**
     * Получить URL обложки (thumbnail) на основе типа
     */
    public function getThumbnailAttribute(): string
    {
        // Определяем тип обложки
        $type = $this->attributes['thumbnailType'] ?? 'auto';

        switch ($type) {
            case 'custom':
                // Если пользователь загрузил свою картинку
                return $this->attributes['thumbnailUrl'] ?? $this->getDefaultThumbnail();

            case 'frame':
                // Генерация URL для конкретного кадра
                if (str_contains($this->url, 'cloudinary.com')) {
                    // Cloudinary: используем so_X.X трансформацию (second offset)
                    $time = $this->attributes['thumbnailFrameTime'] ?? 0;
                    return str_replace(
                        '/video/upload/',
                        "/video/upload/so_{$time},w_400,h_225,c_fill,q_auto/",
                        $this->url
                    );
                } else {
                    // Локальное видео: возвращаем заглушку
                    return $this->attributes['thumbnailUrl'] ?? $this->getDefaultThumbnail();
                }

            case 'auto':
            default:
                // Автоматическая обложка из видео
                if (str_contains($this->url, 'cloudinary.com')) {
                    // Cloudinary автоматически выбирает хороший кадр
                    return str_replace(
                        '/video/upload/',
                        '/video/upload/w_400,h_225,c_fill,q_auto/',
                        $this->url
                    );
                } else {
                    // Для локальных видео - статическая заглушка
                    return $this->getDefaultThumbnail();
                }
        }
    }

    /**
     * Получить заглушку для видео без обложки
     */
    private function getDefaultThumbnail(): string
    {
        // SVG заглушка для видео без обложки
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="225">
            <rect width="400" height="225" fill="#181818"/>
            <text x="50%" y="50%" text-anchor="middle" fill="#717171" font-size="14" dy=".3em" font-family="Arial">
                Нет превью
            </text>
        </svg>';
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
