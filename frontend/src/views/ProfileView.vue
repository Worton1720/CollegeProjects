<script setup>
import { ref, computed, onMounted } from "vue";
import { useAuthStore } from "@/stores/authStore";
import { videosAPI } from "@/api";
import VideoCard from "@/components/VideoCard.vue";

const authStore = useAuthStore();
const videos = ref([]);
const loading = ref(true);
const deleteModal = ref({ show: false, videoId: null, videoTitle: "" });

const roleLabel = computed(() => {
    const roles = {
        ADMIN: "Администратор",
        USER: "Пользователь",
        GUEST: "Гость",
    };
    return roles[authStore.user?.role] || authStore.user?.role;
});

const roleIcon = computed(() => {
    return authStore.user?.role === "ADMIN" ? "shield" : "user";
});

const totalStats = computed(() => {
    return {
        likes: videos.value.reduce((sum, v) => sum + (v._count?.likes || 0), 0),
        comments: videos.value.reduce(
            (sum, v) => sum + (v._count?.comments || 0),
            0
        ),
    };
});

onMounted(async () => {
    try {
        if (!authStore.user?.id) {
            loading.value = false;
            return;
        }
        const { data } = await videosAPI.getAll({
            authorId: authStore.user.id,
            limit: 100,
        });
        videos.value = data.videos;
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
});

const openDeleteModal = (video) => {
    deleteModal.value = {
        show: true,
        videoId: video.id,
        videoTitle: video.title,
    };
};

const closeDeleteModal = () => {
    deleteModal.value = { show: false, videoId: null, videoTitle: "" };
};

const confirmDelete = async () => {
    try {
        await videosAPI.delete(deleteModal.value.videoId);
        videos.value = videos.value.filter(
            (v) => v.id !== deleteModal.value.videoId
        );
        closeDeleteModal();
    } catch (error) {
        alert("Ошибка удаления");
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("ru-RU", {
        day: "numeric",
        month: "long",
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
    <div class="profile-view">
        <div v-if="authStore.user" class="container">
            <!-- Profile Header Card -->
            <div class="profile-header-card">
                <div class="profile-banner"></div>
                <div class="profile-content">
                    <div class="profile-avatar-section">
                        <div class="profile-avatar">
                            {{ authStore.user?.email?.[0]?.toUpperCase() ?? "U" }}
                        </div>
                        <div class="profile-main-info">
                            <h1>{{ authStore.user?.email?.split("@")?.[0] ?? "User" }}</h1>
                            <div class="profile-stats-row">
                                <div
                                    class="stat-badge"
                                    :class="authStore.user?.role.toLowerCase()"
                                >
                                    <svg
                                        v-if="roleIcon === 'shield'"
                                        width="14"
                                        height="14"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"
                                        />
                                    </svg>
                                    <svg
                                        v-else
                                        width="14"
                                        height="14"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"
                                        />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                    {{ roleLabel }}
                                </div>
                                <span class="profile-email">{{
                                    authStore.user?.email
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <polygon points="23 7 16 12 23 17 23 7" />
                                <rect
                                    x="1"
                                    y="5"
                                    width="15"
                                    height="14"
                                    rx="2"
                                    ry="2"
                                />
                            </svg>
                            <div class="stat-content">
                                <div class="stat-value">
                                    {{ videos.length }}
                                </div>
                                <div class="stat-label">Видео</div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"
                                />
                            </svg>
                            <div class="stat-content">
                                <div class="stat-value">
                                    {{ formatCount(totalStats.likes) }}
                                </div>
                                <div class="stat-label">Лайков</div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"
                                />
                            </svg>
                            <div class="stat-content">
                                <div class="stat-value">
                                    {{ formatCount(totalStats.comments) }}
                                </div>
                                <div class="stat-label">Комментариев</div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <rect
                                    x="3"
                                    y="4"
                                    width="18"
                                    height="18"
                                    rx="2"
                                    ry="2"
                                />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            <div class="stat-content">
                                <div class="stat-value">
                                    {{ formatDate(authStore.user?.createdAt) }}
                                </div>
                                <div class="stat-label">Дата регистрации</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Videos Section -->
            <div class="videos-section">
                <div class="section-header">
                    <h2>
                        <svg
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <polygon points="23 7 16 12 23 17 23 7" />
                            <rect
                                x="1"
                                y="5"
                                width="15"
                                height="14"
                                rx="2"
                                ry="2"
                            />
                        </svg>
                        Мои видео
                    </h2>
                    <router-link to="/upload" class="btn-upload">
                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Загрузить видео
                    </router-link>
                </div>

                <!-- Loading -->
                <div v-if="loading" class="videos-grid">
                    <div v-for="i in 6" :key="i" class="skeleton-card">
                        <div class="skeleton-thumbnail"></div>
                        <div class="skeleton-info">
                            <div class="skeleton-title"></div>
                            <div class="skeleton-meta"></div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else-if="videos.length === 0" class="empty-state">
                    <div class="empty-icon">
                        <svg
                            width="80"
                            height="80"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                        >
                            <polygon points="23 7 16 12 23 17 23 7" />
                            <rect
                                x="1"
                                y="5"
                                width="15"
                                height="14"
                                rx="2"
                                ry="2"
                            />
                        </svg>
                    </div>
                    <h3>У вас пока нет видео</h3>
                    <p>
                        Загрузите своё первое видео и начните делиться контентом
                    </p>
                    <router-link to="/upload" class="btn-start">
                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"
                            />
                            <polyline points="17 8 12 3 7 8" />
                            <line x1="12" y1="3" x2="12" y2="15" />
                        </svg>
                        Загрузить видео
                    </router-link>
                </div>

                <!-- Videos Grid -->
                <div v-else class="videos-grid">
                    <VideoCard
                        v-for="(video, index) in videos"
                        :key="video.id"
                        :video="video"
                        :show-actions="true"
                        :style="{ animationDelay: `${index * 0.03}s` }"
                        class="video-item"
                        @delete="openDeleteModal(video)"
                    />
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <Transition name="modal">
            <div
                v-if="deleteModal.show"
                class="modal-overlay"
                @click="closeDeleteModal"
            >
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h3>Удалить видео?</h3>
                        <button @click="closeDeleteModal" class="modal-close">
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>
                            Вы уверены, что хотите удалить видео
                            <strong>"{{ deleteModal.videoTitle }}"</strong>?
                        </p>
                        <p class="modal-warning">
                            Это действие нельзя отменить.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button @click="closeDeleteModal" class="btn-cancel">
                            Отмена
                        </button>
                        <button
                            @click="confirmDelete"
                            class="btn-confirm-delete"
                        >
                            Удалить
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.profile-view {
    background: #0f0f0f;
    min-height: 100vh;
    padding: 24px 0 80px;
}

.container {
    max-width: 1800px;
    margin: 0 auto;
    padding: 0 24px;
}

/* Profile Header Card */
.profile-header-card {
    background: #212121;
    border: 1px solid #3d3d3d;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 32px;
}

.profile-banner {
    height: 120px;
    background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
}

.profile-content {
    padding: 0 32px 32px;
}

.profile-avatar-section {
    display: flex;
    align-items: flex-end;
    gap: 24px;
    margin-top: -60px;
    margin-bottom: 24px;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    background: #3d3d3d;
    border: 4px solid #212121;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: 600;
    color: #f1f1f1;
    flex-shrink: 0;
}

.profile-main-info {
    padding-bottom: 8px;
}

.profile-main-info h1 {
    font-size: 28px;
    font-weight: 600;
    color: #f1f1f1;
    margin-bottom: 8px;
    letter-spacing: -0.5px;
}

.profile-stats-row {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: #3d3d3d;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 600;
    color: #f1f1f1;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-badge.admin {
    background: #ff0000;
}

.profile-email {
    font-size: 14px;
    color: #aaaaaa;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.stat-card {
    background: #1a1a1a;
    border: 1px solid #3d3d3d;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.2s;
}

.stat-card:hover {
    background: #242424;
    border-color: #565656;
}

.stat-card svg {
    color: #065fd4;
    flex-shrink: 0;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 24px;
    font-weight: 600;
    color: #f1f1f1;
    margin-bottom: 4px;
    line-height: 1;
}

.stat-label {
    font-size: 13px;
    color: #aaaaaa;
}

/* Videos Section */
.videos-section {
    margin-top: 32px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.section-header h2 {
    font-size: 22px;
    font-weight: 600;
    color: #f1f1f1;
    display: flex;
    align-items: center;
    gap: 12px;
}

.btn-upload,
.btn-start {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #ff0000;
    border: none;
    border-radius: 18px;
    color: white;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-upload:hover,
.btn-start:hover {
    background: #cc0000;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
}

/* Videos Grid */
.videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 24px;
}

.video-item {
    animation: fadeInUp 0.4s ease-out backwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Skeleton */
.skeleton-card {
    background: #212121;
    border-radius: 12px;
    overflow: hidden;
}

.skeleton-thumbnail {
    aspect-ratio: 16 / 9;
    background: linear-gradient(90deg, #2a2a2a 25%, #333 50%, #2a2a2a 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

.skeleton-info {
    padding: 12px;
}

.skeleton-title {
    height: 16px;
    background: linear-gradient(90deg, #2a2a2a 25%, #333 50%, #2a2a2a 75%);
    background-size: 200% 100%;
    border-radius: 4px;
    margin-bottom: 8px;
    animation: shimmer 1.5s infinite;
}

.skeleton-meta {
    height: 12px;
    width: 60%;
    background: linear-gradient(90deg, #2a2a2a 25%, #333 50%, #2a2a2a 75%);
    background-size: 200% 100%;
    border-radius: 4px;
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: #212121;
    border: 1px dashed #3d3d3d;
    border-radius: 16px;
}

.empty-icon {
    margin-bottom: 24px;
    opacity: 0.3;
}

.empty-state h3 {
    font-size: 24px;
    color: #f1f1f1;
    margin-bottom: 12px;
}

.empty-state p {
    font-size: 14px;
    color: #aaaaaa;
    margin-bottom: 24px;
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
    margin: 0 0 12px 0;
    color: #f1f1f1;
    line-height: 1.5;
}

.modal-warning {
    color: #aaaaaa;
    font-size: 14px;
}

.modal-footer {
    display: flex;
    gap: 12px;
    padding: 16px 24px;
    border-top: 1px solid #3d3d3d;
    justify-content: flex-end;
}

.btn-cancel {
    padding: 10px 20px;
    background: #3d3d3d;
    border: none;
    border-radius: 18px;
    color: #f1f1f1;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel:hover {
    background: #565656;
}

.btn-confirm-delete {
    padding: 10px 20px;
    background: #cc0000;
    border: none;
    border-radius: 18px;
    color: white;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-confirm-delete:hover {
    background: #ff0000;
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

/* Responsive */
@media (max-width: 768px) {
    .profile-content {
        padding: 0 20px 20px;
    }

    .profile-avatar-section {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .profile-avatar {
        margin-top: -60px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .section-header {
        flex-direction: column;
        gap: 16px;
        align-items: stretch;
    }

    .btn-upload {
        width: 100%;
        justify-content: center;
    }

    .videos-grid {
        grid-template-columns: 1fr;
    }
}
</style>
