<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Like;
use Illuminate\Http\Request;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    private $cloudinary;

    public function __construct()
    {
        // Инициализация Cloudinary через URL из .env
        try {
            $this->cloudinary = new Cloudinary(config('services.cloudinary.url'));
        } catch (\Exception $e) {
            Log::warning('⚠️ [Cloudinary] Не удалось инициализировать Cloudinary, будет использовано локальное хранилище', [
                'error' => $e->getMessage()
            ]);
            $this->cloudinary = null;
        }
    }

    // 1. Загрузка видео
    public function uploadVideo(Request $request)
    {
        Log::info('📹 [Upload] Начало загрузки видео', [
            'user_id' => $request->user()->id ?? null,
            'has_title' => $request->has('title'),
            'has_video' => $request->hasFile('video'),
            'files' => array_keys($request->allFiles()),
        ]);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'video' => 'required|file|mimetypes:video/mp4,video/mpeg,video/quicktime,video/x-msvideo,video/webm|max:102400', // 100MB
            'thumbnailType' => 'nullable|in:auto,frame,custom',
            'thumbnailFrameTime' => 'nullable|numeric|min:0',
            'thumbnailImage' => 'nullable|file|image|mimetypes:image/jpeg,image/png,image/webp|max:5120', // 5MB
        ]);

        if ($validator->fails()) {
            Log::error('❌ [Upload] Ошибка валидации', [
                'errors' => $validator->errors()->toArray(),
            ]);
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        try {
            $file = $request->file('video');
            $filename = $file->getClientOriginalName();
            $publicId = \Illuminate\Support\Str::uuid() . '.mp4';

            // Попытка загрузить на Cloudinary, иначе локально
            $videoUrl = null;
            if ($this->cloudinary) {
                Log::info('📤 [Upload] Отправка на Cloudinary', [
                    'filename' => $filename,
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                ]);

                try {
                    $uploadResult = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
                        'resource_type' => 'video',
                        'folder' => 'videohub',
                        'format' => 'mp4',
                        'transformation' => [
                            ['quality' => 'auto'],
                            ['fetch_format' => 'auto'],
                        ],
                    ]);

                    $videoUrl = $uploadResult['secure_url'];
                    $publicId = $uploadResult['public_id'];

                    Log::info('✅ [Upload] Cloudinary успешно', [
                        'public_id' => $publicId,
                        'secure_url' => $videoUrl,
                    ]);
                } catch (\Exception $cloudinaryError) {
                    Log::warning('⚠️ [Upload] Cloudinary ошибка, падбэк на локальное хранилище', [
                        'error' => $cloudinaryError->getMessage(),
                    ]);
                    $videoUrl = null;
                }
            }

            // Если Cloudinary не удалась или не инициализирована, используем локальное хранилище
            if (!$videoUrl) {
                Log::info('💾 [Upload] Сохранение в локальное хранилище', [
                    'filename' => $filename,
                    'size' => $file->getSize(),
                ]);

                $file->storeAs('videos', $publicId, 'public');
                $videoUrl = route('video.file', ['filename' => $publicId]);

                Log::info('✅ [Upload] Локальное сохранение успешно', [
                    'url' => $videoUrl,
                    'public_id' => $publicId,
                ]);
            }

            // Обработка обложки (thumbnail)
            $thumbnailType = $request->input('thumbnailType', 'auto');
            $thumbnailUrl = null;
            $thumbnailFrameTime = null;

            switch ($thumbnailType) {
                case 'custom':
                    // Пользователь загрузил свою картинку
                    if ($request->hasFile('thumbnailImage')) {
                        $thumbnailFile = $request->file('thumbnailImage');

                        if ($this->cloudinary) {
                            // Загрузка в Cloudinary как image
                            try {
                                Log::info('🖼️ [Upload] Загрузка обложки в Cloudinary', [
                                    'filename' => $thumbnailFile->getClientOriginalName(),
                                    'size' => $thumbnailFile->getSize(),
                                ]);

                                $uploadResult = $this->cloudinary->uploadApi()->upload(
                                    $thumbnailFile->getRealPath(),
                                    [
                                        'folder' => 'videohub/thumbnails',
                                        'resource_type' => 'image',
                                        'transformation' => [
                                            ['width' => 1280, 'height' => 720, 'crop' => 'fill'],
                                            ['quality' => 'auto'],
                                            ['fetch_format' => 'auto']
                                        ]
                                    ]
                                );
                                $thumbnailUrl = $uploadResult['secure_url'];

                                Log::info('✅ [Upload] Обложка загружена в Cloudinary', [
                                    'url' => $thumbnailUrl,
                                ]);
                            } catch (\Exception $e) {
                                Log::warning('⚠️ [Upload] Ошибка загрузки обложки в Cloudinary, используем локальное хранилище', [
                                    'error' => $e->getMessage(),
                                ]);
                                $thumbnailUrl = null;
                            }
                        }

                        // Fallback на локальное хранилище
                        if (!$thumbnailUrl) {
                            $thumbnailFilename = \Illuminate\Support\Str::uuid() . '.jpg';
                            $thumbnailFile->storeAs('thumbnails', $thumbnailFilename, 'public');
                            $thumbnailUrl = route('thumbnail.file', ['filename' => $thumbnailFilename]);

                            Log::info('💾 [Upload] Обложка сохранена локально', [
                                'filename' => $thumbnailFilename,
                            ]);
                        }
                    }
                    break;

                case 'frame':
                    // Пользователь выбрал конкретный кадр
                    $thumbnailFrameTime = $request->input('thumbnailFrameTime', 0);
                    Log::info('🎬 [Upload] Выбран кадр из видео', [
                        'frameTime' => $thumbnailFrameTime,
                    ]);
                    break;

                case 'auto':
                default:
                    // Автоматическая обложка - accessor сделает всё сам
                    Log::info('🎨 [Upload] Будет использована автоматическая обложка', []);
                    break;
            }

            // Сохранение в БД
            $video = Video::create([
                'title' => $request->title,
                'description' => $request->description ?? '',
                'url' => $videoUrl,
                'publicId' => $publicId,
                'authorId' => $request->user()->id,
                'thumbnailType' => $thumbnailType,
                'thumbnailUrl' => $thumbnailUrl,
                'thumbnailFrameTime' => $thumbnailFrameTime,
            ]);

            Log::info('✅ [Upload] Видео сохранено в БД', [
                'video_id' => $video->id,
                'title' => $video->title,
            ]);

            $video->loadCount(['comments', 'likes']);
            $video->load('author:id,email,role');

            return response()->json([
                'message' => 'Видео успешно загружено',
                'video' => $video
            ], 201);

        } catch (\Exception $e) {
            Log::error('❌ [Upload] Ошибка при загрузке', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json(['error' => 'Ошибка при загрузке видео: ' . $e->getMessage()], 500);
        }
    }

    // 2. Список видео с фильтрами
    public function getVideos(Request $request)
    {
        $user = auth('sanctum')->user();
        $query = Video::query();

        // Фильтр по автору
        if ($request->authorId) {
            $query->where('authorId', $request->authorId);
        }

        // Фильтр блокировки
        if ($user && $user->role === 'ADMIN') {
            if ($request->has('isBlocked')) {
                $query->where('isBlocked', $request->isBlocked === 'true');
            }
        } else {
            $query->where('isBlocked', false);
        }

        // Поиск
        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'LIKE', "%$s%")
                    ->orWhere('description', 'LIKE', "%$s%");
            });
        }

        $videos = $query->with('author:id,email,role')
            ->withCount(['comments', 'likes'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->query('limit', 12));

        // Добавляем userLike для каждого видео
        $items = $videos->getCollection()->map(function ($video) use ($user) {
            $userLike = null;
            if ($user) {
                $like = Like::where('userId', $user->id)->where('videoId', $video->id)->first();
                $userLike = $like ? $like->isLike : null;
            }
            $video->userLike = $userLike;
            return $video;
        });

        return response()->json([
            'videos' => $items,
            'pagination' => [
                'page' => $videos->currentPage(),
                'limit' => $videos->perPage(),
                'total' => $videos->total(),
                'totalPages' => $videos->lastPage(),
            ]
        ]);
    }

    // 3. Получение одного видео
    public function getVideoById(Request $request, $id)
    {
        $video = Video::with(['author:id,email,role', 'comments.user:id,email,role'])
            ->withCount(['comments', 'likes'])
            ->find($id);

        if (!$video) return response()->json(['error' => 'Видео не найдено'], 404);

        $user = auth('sanctum')->user();
        if ($video->isBlocked && (!$user || $user->role !== 'ADMIN')) {
            return response()->json(['error' => 'Видео заблокировано'], 403);
        }

        $userLike = null;
        if ($user) {
            $like = Like::where('userId', $user->id)->where('videoId', $id)->first();
            $userLike = $like ? $like->isLike : null;
        }

        $video->userLike = $userLike;
        return response()->json(['video' => $video]);
    }

    // 4. Удаление видео
    public function deleteVideo(Request $request, $id)
    {
        $video = Video::findOrFail($id);
        $user = $request->user();

        if ($video->authorId !== $user->id && $user->role !== 'ADMIN') {
            return response()->json(['error' => 'Недостаточно прав'], 403);
        }

        try {
            // Удаление из Cloudinary или локального хранилища
            if ($this->cloudinary) {
                Log::info('🗑️ [Delete] Удаление из Cloudinary', ['public_id' => $video->publicId]);
                try {
                    $this->cloudinary->uploadApi()->destroy($video->publicId, ['resource_type' => 'video']);
                } catch (\Exception $cloudinaryError) {
                    Log::warning('⚠️ [Delete] Cloudinary ошибка, используем локальное хранилище', [
                        'error' => $cloudinaryError->getMessage(),
                    ]);
                    Storage::disk('public')->delete('videos/' . $video->publicId);
                }
            } else {
                Log::info('🗑️ [Delete] Удаление из локального хранилища', ['public_id' => $video->publicId]);
                Storage::disk('public')->delete('videos/' . $video->publicId);
            }

            $video->delete();

            Log::info('✅ [Delete] Видео удалено успешно', ['video_id' => $id]);
            return response()->json(['message' => 'Видео успешно удалено']);
        } catch (\Exception $e) {
            Log::error('❌ [Delete] Ошибка при удалении видео', [
                'video_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Ошибка при удалении видео'], 500);
        }
    }

    // 5. Блокировка (только ADMIN)
    public function toggleBlockVideo(Request $request, $id)
    {
        $video = Video::findOrFail($id);
        $video->update(['isBlocked' => !$video->isBlocked]);

        return response()->json([
            'message' => $video->isBlocked ? 'Видео заблокировано' : 'Видео разблокировано',
            'video' => $video->load('author:id,email,role')
        ]);
    }
}
