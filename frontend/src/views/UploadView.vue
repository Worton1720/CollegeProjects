<script setup>
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
import { videosAPI } from "@/api";
import ThumbnailSelector from "@/components/ThumbnailSelector.vue";

const router = useRouter();
const form = reactive({ title: "", description: "" });
const selectedFile = ref(null);
const uploading = ref(false);
const progress = ref(0);
const error = ref(null);
const success = ref(false);
const isDragging = ref(false);
const thumbnailConfig = ref({
    type: 'auto',
    frameTime: null,
    customImage: null
});

const handleFileSelect = (event) => {
    const file = event.target.files[0];
    validateAndSetFile(file);
};

const handleDrop = (event) => {
    event.preventDefault();
    isDragging.value = false;
    const file = event.dataTransfer.files[0];
    validateAndSetFile(file);
};

const handleDragOver = (event) => {
    event.preventDefault();
    isDragging.value = true;
};

const handleDragLeave = () => {
    isDragging.value = false;
};

const validateAndSetFile = (file) => {
    if (!file) return;
    
    if (!file.type.startsWith('video/')) {
        error.value = "Пожалуйста, выберите видео файл";
        return;
    }
    
    if (file.size > 100 * 1024 * 1024) {
        error.value = "Размер файла не должен превышать 100MB";
        return;
    }
    
    selectedFile.value = file;
    error.value = null;
};

const formatFileSize = (bytes) => {
    if (bytes < 1024) return bytes + " B";
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + " KB";
    return (bytes / (1024 * 1024)).toFixed(2) + " MB";
};

const removeFile = () => {
    selectedFile.value = null;
    error.value = null;
};

const handleUpload = async () => {
    if (!selectedFile.value) return;

    console.log("🎬 [Upload] Начало загрузки видео");
    console.log("📝 [Upload] Данные:", {
        title: form.title,
        description: form.description,
        file: {
            name: selectedFile.value.name,
            size: selectedFile.value.size,
            type: selectedFile.value.type,
        }
    });

    uploading.value = true;
    error.value = null;
    progress.value = 0;

    const formData = new FormData();
    formData.append("title", form.title);
    formData.append("description", form.description);
    formData.append("video", selectedFile.value);

    // Добавляем данные обложки
    let thumbnailType = thumbnailConfig.value.type;
    let thumbnailFrameTime = null;

    if (thumbnailType === 'frame') {
        thumbnailFrameTime = thumbnailConfig.value.frameTime ?? 0;
        // Если есть файл кадра (для локальных видео), отправляем как картинку
        if (thumbnailConfig.value.customImage) {
            formData.append("thumbnailImage", thumbnailConfig.value.customImage);
            // Для локальных видео отправляем как custom тип
            // (Cloudinary видео будут использовать frameTime для генерации кадра)
            if (selectedFile.value && !selectedFile.value.type.startsWith('cloudinary')) {
                thumbnailType = 'custom';
            }
        }
    } else if (thumbnailType === 'custom' && thumbnailConfig.value.customImage) {
        // Для режима custom отправляем файл изображения
        formData.append("thumbnailImage", thumbnailConfig.value.customImage);
    }

    formData.append("thumbnailType", thumbnailType);
    if (thumbnailFrameTime !== null && thumbnailFrameTime !== undefined) {
        formData.append("thumbnailFrameTime", thumbnailFrameTime);
    }

    // Логируем данные обложки
    console.log("🎨 [Upload] Данные обложки:", {
        originalType: thumbnailConfig.value.type,
        finalType: thumbnailType,
        frameTime: thumbnailFrameTime,
        isLocalVideo: selectedFile.value && !selectedFile.value.type.startsWith('cloudinary'),
        customImageSize: thumbnailConfig.value.customImage?.size || 'null',
        customImageName: thumbnailConfig.value.customImage?.name || 'null'
    });

    // Логируем содержимое FormData
    console.log("📋 [Upload] FormData содержит:");
    for (let [key, value] of formData.entries()) {
        if (value instanceof File) {
            console.log(`  - ${key}: File(name=${value.name}, size=${value.size} bytes, type=${value.type})`);
        } else {
            console.log(`  - ${key}:`, value);
        }
    }

    try {
        console.log("📤 [Upload] Отправка на сервер...");

        // Симуляция прогресса
        const interval = setInterval(() => {
            if (progress.value < 90) progress.value += 10;
        }, 500);

        const response = await videosAPI.upload(formData);
        console.log("✅ [Upload] Успешно загружено!", response.data);

        clearInterval(interval);
        progress.value = 100;
        success.value = true;

        setTimeout(() => {
            router.push("/profile");
        }, 2000);
    } catch (err) {
        console.error("❌ [Upload] Ошибка загрузки:", {
            message: err.message,
            response: err.response?.data,
            status: err.response?.status,
            config: {
                url: err.config?.url,
                method: err.config?.method,
            }
        });
        error.value = err.response?.data?.error || err.message || "Ошибка загрузки видео";
    } finally {
        uploading.value = false;
    }
};
</script>

<template>
    <div class="upload-view">
        <div class="container">
            <div class="upload-card">
                <!-- Header -->
                <div class="upload-header">
                    <div class="header-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                    </div>
                    <h1>Загрузить видео</h1>
                    <p class="subtitle">
                        Поделитесь своим контентом с сообществом
                    </p>
                </div>

                <!-- Status Banners -->
                <Transition name="slide-down">
                    <div v-if="error" class="status-banner error">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <span>{{ error }}</span>
                        <button @click="error = null" class="close-banner">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                </Transition>

                <Transition name="slide-down">
                    <div v-if="success" class="status-banner success">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Видео успешно загружено! Перенаправление...</span>
                    </div>
                </Transition>

                <!-- Upload Form -->
                <form @submit.prevent="handleUpload" class="upload-form">
                    <!-- File Drop Zone -->
                    <div class="form-section">
                        <label class="section-label">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                            Файл видео
                            <span class="required">*</span>
                        </label>
                        
                        <div
                            class="file-drop-zone"
                            :class="{ 
                                'has-file': selectedFile,
                                'is-dragging': isDragging,
                                'is-disabled': uploading
                            }"
                            @click="!uploading && $refs.fileInput.click()"
                            @drop="handleDrop"
                            @dragover="handleDragOver"
                            @dragleave="handleDragLeave"
                        >
                            <input
                                ref="fileInput"
                                type="file"
                                accept="video/mp4,video/mpeg,video/quicktime,video/webm"
                                @change="handleFileSelect"
                                :disabled="uploading"
                                hidden
                            />

                            <div v-if="!selectedFile" class="drop-zone-content">
                                <div class="upload-icon">
                                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="17 8 12 3 7 8"/>
                                        <line x1="12" y1="3" x2="12" y2="15"/>
                                    </svg>
                                </div>
                                <h3>Перетащите видео сюда</h3>
                                <p>или нажмите для выбора файла</p>
                                <div class="file-info">
                                    <span>MP4, WebM, MOV или QuickTime</span>
                                    <span class="file-limit">Макс. 100 MB</span>
                                </div>
                            </div>

                            <div v-else class="selected-file-display">
                                <div class="file-preview">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polygon points="23 7 16 12 23 17 23 7"/>
                                        <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                                    </svg>
                                </div>
                                <div class="file-details">
                                    <div class="file-name">{{ selectedFile.name }}</div>
                                    <div class="file-meta">
                                        <span class="file-size">{{ formatFileSize(selectedFile.size) }}</span>
                                        <span class="file-type">{{ selectedFile.type.split('/')[1].toUpperCase() }}</span>
                                    </div>
                                </div>
                                <button
                                    v-if="!uploading"
                                    type="button"
                                    class="btn-remove-file"
                                    @click.stop="removeFile"
                                    title="Удалить файл"
                                >
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="18" y1="6" x2="6" y2="18"/>
                                        <line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Thumbnail Selector -->
                    <ThumbnailSelector
                        v-if="selectedFile"
                        :videoFile="selectedFile"
                        v-model="thumbnailConfig"
                    />

                    <!-- Video Details -->
                    <div class="form-section">
                        <label class="section-label">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            Детали
                        </label>

                        <div class="form-group">
                            <label class="form-label">
                                Название
                                <span class="required">*</span>
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="form-input"
                                placeholder="Добавьте название, которое описывает ваше видео"
                                required
                                :disabled="uploading"
                                maxlength="100"
                            />
                            <div class="char-counter">{{ form.title.length }}/100</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Описание</label>
                            <textarea
                                v-model="form.description"
                                class="form-input"
                                rows="4"
                                placeholder="Расскажите зрителям о своём видео"
                                :disabled="uploading"
                                maxlength="500"
                            ></textarea>
                            <div class="char-counter">{{ form.description.length }}/500</div>
                        </div>
                    </div>

                    <!-- Upload Progress -->
                    <Transition name="fade">
                        <div v-if="uploading" class="upload-progress">
                            <div class="progress-header">
                                <div class="progress-info">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="rotating">
                                        <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                                    </svg>
                                    <span>Загрузка видео...</span>
                                </div>
                                <span class="progress-percent">{{ progress }}%</span>
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar" :style="{ width: progress + '%' }"></div>
                            </div>
                        </div>
                    </Transition>

                    <!-- Actions -->
                    <div class="form-actions">
                        <router-link to="/profile" class="btn-secondary" :class="{ disabled: uploading }">
                            Отмена
                        </router-link>
                        <button
                            type="submit"
                            class="btn-primary"
                            :disabled="uploading || !selectedFile || !form.title"
                        >
                            <svg v-if="!uploading" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                            <span>{{ uploading ? 'Загрузка...' : 'Опубликовать' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.upload-view {
    background: #0f0f0f;
    min-height: 100vh;
    padding: 40px 20px 80px;
}

.container {
    max-width: 900px;
    margin: 0 auto;
}

.upload-card {
    background: #212121;
    border: 1px solid #3d3d3d;
    border-radius: 16px;
    padding: 48px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.3);
}

/* Header */
.upload-header {
    text-align: center;
    margin-bottom: 40px;
}

.header-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
    border-radius: 50%;
    margin-bottom: 24px;
    color: white;
}

.upload-header h1 {
    font-size: 32px;
    font-weight: 600;
    color: #f1f1f1;
    margin-bottom: 8px;
    letter-spacing: -0.5px;
}

.subtitle {
    font-size: 15px;
    color: #aaaaaa;
}

/* Status Banners */
.status-banner {
    padding: 14px 20px;
    border-radius: 8px;
    font-size: 14px;
    margin-bottom: 32px;
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
}

.status-banner svg {
    flex-shrink: 0;
}

.status-banner.error {
    background: rgba(204, 0, 0, 0.1);
    border: 1px solid rgba(255, 0, 0, 0.3);
    color: #ff6b6b;
}

.status-banner.success {
    background: rgba(0, 200, 83, 0.1);
    border: 1px solid rgba(0, 200, 83, 0.3);
    color: #00c853;
}

.close-banner {
    margin-left: auto;
    background: none;
    border: none;
    color: currentColor;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.close-banner:hover {
    opacity: 1;
}

/* Form */
.upload-form {
    display: flex;
    flex-direction: column;
    gap: 32px;
}

.form-section {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.section-label {
    font-size: 16px;
    font-weight: 600;
    color: #f1f1f1;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-label svg {
    color: #aaaaaa;
}

.required {
    color: #ff6b6b;
}

/* File Drop Zone */
.file-drop-zone {
    border: 2px dashed #3d3d3d;
    border-radius: 12px;
    padding: 48px 32px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    background: #1a1a1a;
}

.file-drop-zone:hover:not(.is-disabled) {
    border-color: #565656;
    background: #242424;
}

.file-drop-zone.is-dragging {
    border-color: #065fd4;
    background: rgba(6, 95, 212, 0.05);
}

.file-drop-zone.has-file {
    border-style: solid;
    border-color: #3d3d3d;
    background: #242424;
    padding: 24px;
}

.file-drop-zone.is-disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.drop-zone-content {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.upload-icon {
    margin-bottom: 16px;
    color: #717171;
}

.drop-zone-content h3 {
    font-size: 18px;
    font-weight: 500;
    color: #f1f1f1;
    margin-bottom: 8px;
}

.drop-zone-content p {
    font-size: 14px;
    color: #aaaaaa;
    margin-bottom: 16px;
}

.file-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 13px;
    color: #717171;
}

.file-limit {
    font-weight: 500;
}

/* Selected File */
.selected-file-display {
    display: flex;
    align-items: center;
    gap: 20px;
}

.file-preview {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    background: #2d2d2d;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #065fd4;
}

.file-details {
    flex: 1;
    text-align: left;
}

.file-name {
    font-size: 15px;
    font-weight: 500;
    color: #f1f1f1;
    margin-bottom: 8px;
    word-break: break-word;
}

.file-meta {
    display: flex;
    gap: 12px;
    font-size: 13px;
    color: #aaaaaa;
}

.file-type {
    padding: 2px 8px;
    background: #3d3d3d;
    border-radius: 4px;
    font-weight: 500;
}

.btn-remove-file {
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    background: #3d3d3d;
    border: none;
    border-radius: 50%;
    color: #aaaaaa;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-remove-file:hover {
    background: rgba(255, 0, 0, 0.2);
    color: #ff6b6b;
}

/* Form Inputs */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-label {
    font-size: 14px;
    font-weight: 500;
    color: #f1f1f1;
}

.form-input {
    padding: 12px 16px;
    background: #121212;
    border: 1px solid #3d3d3d;
    border-radius: 8px;
    font-size: 14px;
    color: #f1f1f1;
    transition: all 0.2s;
    font-family: inherit;
}

.form-input::placeholder {
    color: #717171;
}

.form-input:focus {
    outline: none;
    border-color: #065fd4;
    background: #1a1a1a;
}

.form-input:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

textarea.form-input {
    resize: vertical;
    min-height: 100px;
}

.char-counter {
    font-size: 12px;
    color: #717171;
    text-align: right;
}

/* Upload Progress */
.upload-progress {
    background: #1a1a1a;
    border: 1px solid #3d3d3d;
    border-radius: 12px;
    padding: 20px;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.progress-info {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    color: #f1f1f1;
    font-weight: 500;
}

.rotating {
    animation: rotate 1s linear infinite;
    color: #065fd4;
}

@keyframes rotate {
    to {
        transform: rotate(360deg);
    }
}

.progress-percent {
    font-size: 14px;
    font-weight: 600;
    color: #065fd4;
}

.progress-bar-container {
    height: 8px;
    background: #2d2d2d;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #065fd4 0%, #0c7ceb 100%);
    transition: width 0.3s ease;
    border-radius: 4px;
}

/* Actions */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding-top: 8px;
}

.btn-secondary,
.btn-primary {
    padding: 12px 24px;
    border-radius: 24px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-secondary {
    background: #3d3d3d;
    border: none;
    color: #f1f1f1;
}

.btn-secondary:hover:not(.disabled) {
    background: #565656;
}

.btn-secondary.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

.btn-primary {
    background: #ff0000;
    border: none;
    color: white;
}

.btn-primary:hover:not(:disabled) {
    background: #cc0000;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
}

.btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

/* Transitions */
.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.3s ease;
}

.slide-down-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}

.slide-down-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

.fade-enter-active,
.fade-leave-active {
    transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .upload-card {
        padding: 32px 24px;
    }

    .file-drop-zone {
        padding: 32px 20px;
    }

    .file-drop-zone.has-file {
        padding: 20px;
    }

    .selected-file-display {
        flex-direction: column;
        text-align: center;
    }

    .file-details {
        text-align: center;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .btn-secondary,
    .btn-primary {
        width: 100%;
        justify-content: center;
    }
}
</style>