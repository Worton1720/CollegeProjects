<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model
{
    use HasUuids;

    protected $fillable = [
        'isLike',
        'userId',
        'videoId'
    ];

    // Указываем, что поле isLike должно всегда приводиться к типу boolean (true/false)
    protected $casts = [
        'isLike' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, 'videoId');
    }
}
