<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
    videoFile: {
        type: File,
        required: true
    },
    modelValue: {
        type: Object,
        default: () => ({
            type: 'auto',
            frameTime: null,
            customImage: null
        })
    }
});

const emit = defineEmits(['update:modelValue']);

const thumbnailType = ref('auto');
const frameTime = ref(null);
const customImage = ref(null);
const videoPreviewUrl = ref(null);
const framePreviewUrl = ref(null);
const videoDuration = ref(0);
const customImageInput = ref(null);

// Создать object URL для preview видео
watch(() => props.videoFile, (newFile) => {
    if (newFile) {
        videoPreviewUrl.value = URL.createObjectURL(newFile);

        // Получить длительность видео
        const video = document.createElement('video');
        video.src = videoPreviewUrl.value;
        video.onloadedmetadata = () => {
            videoDuration.value = video.duration;
            frameTime.value = Math.floor(video.duration / 2); // Середина по умолчанию
        };
    }
}, { immediate: true });

// Генерация preview кадра локально (через canvas)
const updateFramePreview = () => {
    if (!videoPreviewUrl.value) return;

    const video = document.createElement('video');
    video.src = videoPreviewUrl.value;
    video.currentTime = frameTime.value;

    video.onseeked = () => {
        const canvas = document.createElement('canvas');
        canvas.width = 640;
        canvas.height = 360;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        framePreviewUrl.value = canvas.toDataURL('image/jpeg', 0.85);

        // Для локальных видео, конвертируем canvas в файл
        if (!props.videoFile.type.includes('cloudinary')) {
            canvas.toBlob((blob) => {
                if (blob) {
                    const file = new File(
                        [blob],
                        `frame-${(frameTime.value ?? 0).toFixed(1)}s.jpg`,
                        { type: 'image/jpeg' }
                    );
                    customImage.value = file;
                }
            }, 'image/jpeg', 0.85);
        }
    };
};

watch([thumbnailType, frameTime, customImage], () => {
    emit('update:modelValue', {
        type: thumbnailType.value,
        frameTime: frameTime.value,
        customImage: customImage.value
    });
}, { immediate: true });

watch(frameTime, () => {
    if (thumbnailType.value === 'frame') {
        updateFramePreview();
    }
});

const handleCustomImageSelect = (event) => {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        customImage.value = file;
    }
};

const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

const customImageUrl = computed(() => {
    return customImage.value ? URL.createObjectURL(customImage.value) : null;
});
</script>

<template>
    <div class="thumbnail-selector">
        <label class="section-label">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
            </svg>
            Обложка видео
        </label>

        <!-- Выбор типа обложки -->
        <div class="thumbnail-type-selector">
            <button
                type="button"
                @click="thumbnailType = 'auto'"
                class="type-btn"
                :class="{ active: thumbnailType === 'auto' }"
            >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="1.5"/>
                    <path d="M12 1v6m0 6v6m5.2-13.2l-4.2 4.2m0 6l-4.2 4.2m13.2-5.2h-6m-6 0H1m13.2 5.2l-4.2-4.2m0-6l-4.2-4.2"/>
                </svg>
                <div>
                    <div class="type-title">Автоматически</div>
                    <div class="type-desc">Лучший кадр из видео</div>
                </div>
            </button>

            <button
                type="button"
                @click="thumbnailType = 'frame'; updateFramePreview()"
                class="type-btn"
                :class="{ active: thumbnailType === 'frame' }"
            >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="7" width="20" height="15" rx="2" ry="2"/>
                    <polyline points="17 2 12 7 7 2"/>
                </svg>
                <div>
                    <div class="type-title">Выбрать кадр</div>
                    <div class="type-desc">Укажите время в видео</div>
                </div>
            </button>

            <button
                type="button"
                @click="thumbnailType = 'custom'"
                class="type-btn"
                :class="{ active: thumbnailType === 'custom' }"
            >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                <div>
                    <div class="type-title">Загрузить картинку</div>
                    <div class="type-desc">Своя обложка</div>
                </div>
            </button>
        </div>

        <!-- Preview зона -->
        <div class="thumbnail-preview-zone">
            <!-- Auto preview -->
            <div v-if="thumbnailType === 'auto'" class="preview-content">
                <video
                    v-if="videoPreviewUrl"
                    :src="videoPreviewUrl"
                    class="preview-video"
                    muted
                />
                <div class="preview-label">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="1.5"/>
                        <path d="M12 1v6m0 6v6"/>
                    </svg>
                    Будет выбран лучший кадр автоматически
                </div>
            </div>

            <!-- Frame selector preview -->
            <div v-else-if="thumbnailType === 'frame'" class="preview-content">
                <img
                    v-if="framePreviewUrl"
                    :src="framePreviewUrl"
                    class="preview-image"
                    alt="Frame preview"
                />
                <div v-else class="preview-placeholder">
                    Выберите время для preview
                </div>
                <div class="frame-time-selector">
                    <label class="time-label">
                        Время кадра: <strong>{{ formatTime(frameTime ?? 0) }}</strong>
                    </label>
                    <input
                        type="range"
                        v-model.number="frameTime"
                        :min="0"
                        :max="videoDuration"
                        :step="0.1"
                        class="time-slider"
                    />
                    <div class="time-range">
                        <span>0:00</span>
                        <span>{{ formatTime(videoDuration) }}</span>
                    </div>
                </div>
            </div>

            <!-- Custom image upload -->
            <div v-else-if="thumbnailType === 'custom'" class="preview-content">
                <div
                    v-if="!customImage"
                    class="custom-upload-zone"
                    @click="customImageInput?.click()"
                >
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                    <p>Нажмите для выбора изображения</p>
                    <span class="upload-hint">JPG, PNG, WebP (макс. 5MB)</span>
                </div>
                <div v-else class="custom-preview">
                    <img
                        :src="customImageUrl"
                        class="preview-image"
                        alt="Custom thumbnail"
                    />
                    <button
                        type="button"
                        @click="customImage = null"
                        class="btn-remove-custom"
                    >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                        Удалить
                    </button>
                </div>
                <input
                    ref="customImageInput"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    @change="handleCustomImageSelect"
                    hidden
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
.thumbnail-selector {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.section-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #e0e0e0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.thumbnail-type-selector {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.type-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border: 2px solid #333;
    border-radius: 8px;
    background: #242424;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #a0a0a0;
}

.type-btn:hover {
    border-color: #555;
    background: #2a2a2a;
}

.type-btn.active {
    border-color: #4a9eff;
    background: #1a2a3a;
    color: #e0e0e0;
}

.type-btn svg {
    flex-shrink: 0;
}

.type-title {
    font-weight: 500;
    font-size: 13px;
    color: inherit;
}

.type-desc {
    font-size: 12px;
    color: #717171;
    margin-top: 2px;
}

.type-btn.active .type-desc {
    color: #a0a0a0;
}

.thumbnail-preview-zone {
    background: #1a1a1a;
    border: 1px solid #333;
    border-radius: 8px;
    padding: 20px;
    min-height: 280px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.preview-content {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
}

.preview-video,
.preview-image,
.custom-preview {
    width: 100%;
    max-width: 400px;
    aspect-ratio: 16 / 9;
    border-radius: 6px;
    background: #0a0a0a;
    object-fit: cover;
}

.preview-image {
    border: 1px solid #333;
}

.preview-placeholder {
    width: 100%;
    max-width: 400px;
    aspect-ratio: 16 / 9;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #0a0a0a;
    border: 2px dashed #333;
    border-radius: 6px;
    color: #717171;
    font-size: 14px;
}

.preview-label {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #717171;
    font-size: 13px;
}

.frame-time-selector {
    width: 100%;
    max-width: 400px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.time-label {
    font-size: 13px;
    color: #a0a0a0;
    font-weight: 500;
}

.time-slider {
    width: 100%;
    height: 6px;
    border-radius: 3px;
    background: #333;
    outline: none;
    -webkit-appearance: none;
    appearance: none;
}

.time-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #4a9eff;
    cursor: pointer;
    transition: background 0.2s ease;
}

.time-slider::-webkit-slider-thumb:hover {
    background: #5aafff;
}

.time-slider::-moz-range-thumb {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #4a9eff;
    cursor: pointer;
    border: none;
    transition: background 0.2s ease;
}

.time-slider::-moz-range-thumb:hover {
    background: #5aafff;
}

.time-range {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #717171;
}

.custom-upload-zone {
    width: 100%;
    max-width: 400px;
    padding: 40px 20px;
    border: 2px dashed #333;
    border-radius: 6px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #717171;
}

.custom-upload-zone:hover {
    border-color: #4a9eff;
    background: #242424;
    color: #a0a0a0;
}

.custom-upload-zone svg {
    color: inherit;
}

.custom-upload-zone p {
    font-size: 14px;
    margin: 0;
    font-weight: 500;
}

.upload-hint {
    font-size: 12px;
    color: inherit;
    opacity: 0.7;
}

.custom-preview {
    position: relative;
    border: 1px solid #333;
}

.btn-remove-custom {
    position: absolute;
    top: 8px;
    right: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: rgba(0, 0, 0, 0.8);
    border: 1px solid #555;
    border-radius: 4px;
    color: #a0a0a0;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s ease;
}

.btn-remove-custom:hover {
    background: rgba(0, 0, 0, 0.95);
    border-color: #ff6b6b;
    color: #ff6b6b;
}

.btn-remove-custom svg {
    width: 14px;
    height: 14px;
}

@media (max-width: 768px) {
    .thumbnail-type-selector {
        grid-template-columns: 1fr;
    }

    .type-btn {
        padding: 10px 12px;
    }

    .type-title {
        font-size: 12px;
    }

    .type-desc {
        font-size: 11px;
    }
}
</style>
