<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/authStore";
import { videosAPI, commentsAPI, likesAPI } from "@/api";

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const video = ref(null);
const commentText = ref("");
const userLike = ref(null);
const likesCount = ref(0);
const dislikesCount = ref(0);
const showDeleteModal = ref(false);
const isCommenting = ref(false);

const canEdit = computed(() => {
    return authStore.user?.id === video.value?.authorId || authStore.isAdmin;
});

const likesPercentage = computed(() => {
    const total = likesCount.value + dislikesCount.value;
    if (total === 0) return 50;
    return Math.round((likesCount.value / total) * 100);
});

onMounted(async () => {
    try {
        const { data } = await videosAPI.getById(route.params.id);
        video.value = data.video;
        userLike.value = data.video.userLike;
        const stats = await likesAPI.getStats(route.params.id);
        likesCount.value = stats.data.stats.likes;
        dislikesCount.value = stats.data.stats.dislikes;
    } catch (error) {
        console.error(error);
        router.push("/");
    }
});

const handleLike = async (isLike) => {
    if (!authStore.isUser) {
        router.push("/login");
        return;
    }
    try {
        const { data } = await likesAPI.toggle({
            videoId: video.value.id,
            isLike,
        });
        likesCount.value = data.stats.likes;
        dislikesCount.value = data.stats.dislikes;
        userLike.value = data.like ? data.like.isLike : null;
    } catch (error) {
        console.error(error);
    }
};

const handleAddComment = async () => {
    if (!commentText.value.trim()) return;
    
    isCommenting.value = true;
    try {
        await commentsAPI.create({
            text: commentText.value,
            videoId: video.value.id,
        });
        const { data } = await videosAPI.getById(route.params.id);
        video.value = data.video;
        commentText.value = "";
    } catch (error) {
        console.error(error);
    } finally {
        isCommenting.value = false;
    }
};

const openDeleteModal = () => {
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
};

const confirmDelete = async () => {
    try {
        await videosAPI.delete(video.value.id);
        router.push("/");
    } catch (error) {
        alert("Ошибка удаления");
    }
};

const formatDate = (dateString) => {
    if (!dateString) return "Неизвестно";

    // Парсим ISO дату с заменой последнего Z на +00:00 для совместимости
    const dateStr = dateString.replace('Z', '+00:00');
    const d = new Date(dateStr);

    // Проверка на валидность даты
    if (isNaN(d.getTime())) {
        return "Неизвестно";
    }

    const now = new Date();
    const diff = now - d;
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));

    if (days === 0) return "Сегодня";
    if (days === 1) return "Вчера";
    if (days < 7) return `${days} дней назад`;
    if (days < 30) return `${Math.floor(days / 7)} нед. назад`;
    if (days < 365) return `${Math.floor(days / 30)} мес. назад`;

    return d.toLocaleDateString("ru-RU", {
        day: "numeric",
        month: "short",
        year: "numeric",
    });
};

const formatCount = (count) => {
    if (count >= 1000000) return `${(count / 1000000).toFixed(1)}M`;
    if (count >= 1000) return `${(count / 1000).toFixed(1)}K`;
    return count;
};
</script>

<template>
    <div v-if="video" class="video-detail-view">
        <!-- Video Player -->
        <div class="player-section">
            <div class="player-wrapper">
                <video
                    :src="video.url"
                    controls
                    class="main-video-player"
                    controlsList="nodownload"
                ></video>
            </div>
        </div>

        <!-- Content -->
        <div class="container">
            <div class="content-layout">
                <!-- Video Info -->
                <div class="video-main-section">
                    <div class="video-title-section">
                        <h1 class="video-title">{{ video.title }}</h1>
                    </div>

                    <div class="video-meta-row">
                        <!-- Author Info -->
                        <div class="author-card">
                            <div class="author-avatar">
                                {{ video.author?.email?.[0]?.toUpperCase() ?? "U" }}
                            </div>
                            <div class="author-info">
                                <div class="author-name">{{ video.author?.email?.split("@")?.[0] ?? "Пользователь" }}</div>
                                <div class="author-meta">{{ formatDate(video.createdAt) }}</div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="video-actions-bar">
                            <!-- Like/Dislike -->
                            <div class="action-group like-dislike-group">
                                <button
                                    @click="handleLike(true)"
                                    class="action-btn"
                                    :class="{ active: userLike === true }"
                                    title="Нравится"
                                >
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                                    </svg>
                                    <span>{{ formatCount(likesCount) }}</span>
                                </button>
                                <div class="action-divider"></div>
                                <button
                                    @click="handleLike(false)"
                                    class="action-btn"
                                    :class="{ active: userLike === false }"
                                    title="Не нравится"
                                >
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zm7-13h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-3"></path>
                                    </svg>
                                    <span>{{ formatCount(dislikesCount) }}</span>
                                </button>
                            </div>

                            <!-- Delete Button -->
                            <button
                                v-if="canEdit"
                                @click="openDeleteModal"
                                class="action-btn delete-action"
                                title="Удалить видео"
                            >
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                                <span>Удалить</span>
                            </button>
                        </div>
                    </div>

                    <!-- Like Bar Visualization -->
                    <div class="like-bar-container" v-if="likesCount + dislikesCount > 0">
                        <div class="like-bar">
                            <div class="like-bar-fill" :style="{ width: likesPercentage + '%' }"></div>
                        </div>
                        <div class="like-bar-labels">
                            <span>{{ likesPercentage }}% нравится</span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="description-section" v-if="video.description">
                        <div class="description-content">
                            <p>{{ video.description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="comments-section">
                    <div class="comments-header">
                        <h2>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                            </svg>
                            Комментарии
                            <span class="comment-count">{{ video.comments_count }}</span>
                        </h2>
                    </div>

                    <!-- Add Comment -->
                    <div v-if="authStore.isUser && authStore.user" class="add-comment-block">
                        <div class="comment-avatar">
                            {{ authStore.user?.email?.[0]?.toUpperCase() ?? "U" }}
                        </div>
                        <form @submit.prevent="handleAddComment" class="comment-form">
                            <textarea
                                v-model="commentText"
                                placeholder="Добавьте комментарий..."
                                class="comment-input"
                                :disabled="isCommenting"
                            ></textarea>
                            <Transition name="fade">
                                <div v-if="commentText.trim()" class="comment-actions">
                                    <button
                                        type="button"
                                        @click="commentText = ''"
                                        class="btn-comment-cancel"
                                        :disabled="isCommenting"
                                    >
                                        Отмена
                                    </button>
                                    <button
                                        type="submit"
                                        class="btn-comment-submit"
                                        :disabled="isCommenting"
                                    >
                                        <span v-if="!isCommenting">Отправить</span>
                                        <div v-else class="mini-spinner"></div>
                                    </button>
                                </div>
                            </Transition>
                        </form>
                    </div>

                    <!-- Login Prompt -->
                    <div v-else class="login-prompt">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="16" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                        <p>
                            <router-link to="/login">Войдите в аккаунт</router-link>, чтобы оставлять комментарии
                        </p>
                    </div>

                    <!-- Comments List -->
                    <div class="comments-list">
                        <div
                            v-for="comment in video.comments"
                            :key="comment.id"
                            class="comment-item"
                        >
                            <div class="comment-avatar">
                                {{ comment.user.email[0].toUpperCase() }}
                            </div>
                            <div class="comment-body">
                                <div class="comment-header">
                                    <span class="comment-author">{{ comment.user.email.split("@")[0] }}</span>
                                    <span class="comment-date">{{ formatDate(comment.createdAt) }}</span>
                                </div>
                                <p class="comment-text">{{ comment.text }}</p>
                            </div>
                        </div>

                        <div v-if="video.comments.length === 0 && !authStore.isUser" class="empty-comments">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                            </svg>
                            <p>Пока нет комментариев</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <Transition name="modal">
            <div v-if="showDeleteModal" class="modal-overlay" @click="closeDeleteModal">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h3>Удалить видео навсегда?</h3>
                        <button @click="closeDeleteModal" class="modal-close">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Видео <strong>"{{ video.title }}"</strong> будет удалено без возможности восстановления.</p>
                    </div>
                    <div class="modal-footer">
                        <button @click="closeDeleteModal" class="btn-modal-cancel">
                            Отмена
                        </button>
                        <button @click="confirmDelete" class="btn-modal-delete">
                            Удалить навсегда
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>

    <!-- Loading State -->
    <div v-else class="loading-full">
        <div class="spinner-large"></div>
        <p>Загрузка видео...</p>
    </div>
</template>

<style scoped>
.video-detail-view {
    background: #0f0f0f;
    min-height: 100vh;
    padding-bottom: 60px;
}

/* Player Section */
.player-section {
    background: #000;
    width: 100%;
}

.player-wrapper {
    max-width: 1800px;
    margin: 0 auto;
    aspect-ratio: 16 / 9;
    max-height: calc(100vh - 56px);
}

.main-video-player {
    width: 100%;
    height: 100%;
    display: block;
}

/* Content Layout */
.container {
    max-width: 1800px;
    margin: 0 auto;
    padding: 0 24px;
}

.content-layout {
    max-width: 1280px;
    margin: 0 auto;
}

/* Video Main Section */
.video-main-section {
    padding: 20px 0;
}

.video-title-section {
    margin-bottom: 12px;
}

.video-title {
    font-size: 20px;
    font-weight: 600;
    color: #f1f1f1;
    line-height: 1.4;
}

.video-meta-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}

/* Author Card */
.author-card {
    display: flex;
    align-items: center;
    gap: 12px;
}

.author-avatar,
.comment-avatar {
    width: 40px;
    height: 40px;
    background: #3d3d3d;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: #f1f1f1;
    font-size: 16px;
    flex-shrink: 0;
}

.author-info {
    display: flex;
    flex-direction: column;
}

.author-name {
    font-size: 14px;
    font-weight: 500;
    color: #f1f1f1;
}

.author-meta {
    font-size: 12px;
    color: #aaaaaa;
}

/* Actions Bar */
.video-actions-bar {
    display: flex;
    align-items: center;
    gap: 8px;
}

.action-group {
    display: flex;
    align-items: center;
    background: #272727;
    border-radius: 18px;
    padding: 0 4px;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 16px;
    background: transparent;
    border: none;
    color: #f1f1f1;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    border-radius: 18px;
    transition: all 0.2s;
}

.action-btn:hover {
    background: #3d3d3d;
}

.action-btn.active {
    color: #065fd4;
}

.action-divider {
    width: 1px;
    height: 24px;
    background: #3d3d3d;
}

.delete-action {
    background: #272727;
    border-radius: 18px;
    padding: 10px 16px;
}

.delete-action:hover {
    background: rgba(255, 0, 0, 0.1);
    color: #ff6b6b;
}

/* Like Bar */
.like-bar-container {
    margin-bottom: 16px;
}

.like-bar {
    height: 3px;
    background: #3d3d3d;
    border-radius: 2px;
    overflow: hidden;
    margin-bottom: 8px;
}

.like-bar-fill {
    height: 100%;
    background: #f1f1f1;
    transition: width 0.3s ease;
}

.like-bar-labels {
    font-size: 12px;
    color: #aaaaaa;
}

/* Description */
.description-section {
    background: #272727;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 24px;
}

.description-content p {
    font-size: 14px;
    line-height: 1.6;
    color: #f1f1f1;
    white-space: pre-wrap;
    margin: 0;
}

/* Comments Section */
.comments-section {
    margin-top: 32px;
}

.comments-header h2 {
    font-size: 20px;
    font-weight: 600;
    color: #f1f1f1;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.comment-count {
    font-size: 16px;
    font-weight: 400;
    color: #aaaaaa;
}

/* Add Comment */
.add-comment-block {
    display: flex;
    gap: 16px;
    margin-bottom: 32px;
}

.comment-form {
    flex: 1;
}

.comment-input {
    width: 100%;
    background: transparent;
    border: none;
    border-bottom: 1px solid #3d3d3d;
    padding: 8px 0;
    font-size: 14px;
    color: #f1f1f1;
    resize: vertical;
    min-height: 24px;
    max-height: 200px;
    font-family: inherit;
    transition: border-color 0.2s;
}

.comment-input::placeholder {
    color: #717171;
}

.comment-input:focus {
    outline: none;
    border-bottom-color: #f1f1f1;
}

.comment-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 12px;
}

.btn-comment-cancel,
.btn-comment-submit {
    padding: 10px 16px;
    border: none;
    border-radius: 18px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-comment-cancel {
    background: transparent;
    color: #f1f1f1;
}

.btn-comment-cancel:hover:not(:disabled) {
    background: #3d3d3d;
}

.btn-comment-submit {
    background: #065fd4;
    color: white;
    min-width: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-comment-submit:hover:not(:disabled) {
    background: #0c7ceb;
}

.btn-comment-submit:disabled,
.btn-comment-cancel:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.mini-spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Login Prompt */
.login-prompt {
    background: #272727;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 32px;
    display: flex;
    align-items: center;
    gap: 16px;
    color: #aaaaaa;
}

.login-prompt svg {
    flex-shrink: 0;
}

.login-prompt p {
    margin: 0;
    font-size: 14px;
}

.login-prompt a {
    color: #3ea6ff;
    text-decoration: none;
    font-weight: 500;
}

.login-prompt a:hover {
    text-decoration: underline;
}

/* Comments List */
.comments-list {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.comment-item {
    display: flex;
    gap: 16px;
}

.comment-body {
    flex: 1;
}

.comment-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}

.comment-author {
    font-size: 13px;
    font-weight: 500;
    color: #f1f1f1;
}

.comment-date {
    font-size: 12px;
    color: #aaaaaa;
}

.comment-text {
    font-size: 14px;
    color: #f1f1f1;
    line-height: 1.5;
    margin: 0;
    white-space: pre-wrap;
}

/* Empty Comments */
.empty-comments {
    text-align: center;
    padding: 60px 20px;
    color: #717171;
}

.empty-comments svg {
    margin-bottom: 16px;
    opacity: 0.5;
}

.empty-comments p {
    margin: 0;
    font-size: 14px;
}

/* Modal */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.85);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 20px;
    backdrop-filter: blur(4px);
}

.modal-content {
    background: #212121;
    border-radius: 12px;
    max-width: 500px;
    width: 100%;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    border: 1px solid #3d3d3d;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #3d3d3d;
}

.modal-header h3 {
    font-size: 18px;
    font-weight: 500;
    margin: 0;
    color: #f1f1f1;
}

.modal-close {
    background: none;
    border: none;
    color: #aaaaaa;
    cursor: pointer;
    padding: 4px;
    display: flex;
    border-radius: 50%;
    transition: all 0.2s;
}

.modal-close:hover {
    background: #3d3d3d;
    color: #f1f1f1;
}

.modal-body {
    padding: 24px;
}

.modal-body p {
    margin: 0;
    color: #f1f1f1;
    line-height: 1.5;
    font-size: 14px;
}

.modal-footer {
    display: flex;
    gap: 12px;
    padding: 16px 24px;
    border-top: 1px solid #3d3d3d;
    justify-content: flex-end;
}

.btn-modal-cancel,
.btn-modal-delete {
    padding: 10px 20px;
    border: none;
    border-radius: 18px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-modal-cancel {
    background: #3d3d3d;
    color: #f1f1f1;
}

.btn-modal-cancel:hover {
    background: #565656;
}

.btn-modal-delete {
    background: #cc0000;
    color: white;
}

.btn-modal-delete:hover {
    background: #ff0000;
}

/* Loading */
.loading-full {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    background: #0f0f0f;
    color: #aaaaaa;
}

.spinner-large {
    width: 48px;
    height: 48px;
    border: 4px solid #3d3d3d;
    border-top-color: #f1f1f1;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin-bottom: 16px;
}

/* Transitions */
.modal-enter-active,
.modal-leave-active {
    transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-from .modal-content,
.modal-leave-to .modal-content {
    transform: scale(0.9);
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
    .video-meta-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .video-actions-bar {
        width: 100%;
        flex-wrap: wrap;
    }

    .add-comment-block {
        flex-direction: column;
        gap: 12px;
    }

    .comment-item {
        gap: 12px;
    }
}
</style>