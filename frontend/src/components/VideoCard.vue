<script setup>
import { computed } from "vue";
import { useAuthStore } from "@/stores/authStore";

const props = defineProps({
    video: {
        type: Object,
        required: true,
    },
    showActions: {
        type: Boolean,
        default: false,
    },
});

defineEmits(["delete", "toggleBlock"]);

const authStore = useAuthStore();

const canEdit = computed(() => {
    return authStore.user?.id === props.video.authorId || authStore.isAdmin;
});

const getThumbnail = (video) => {
    // Если сервер вернул готовый thumbnail URL
    if (video.thumbnail) {
        return video.thumbnail;
    }

    // Fallback на старую логику для видео без обложки
    if (!video.url) return '';
    return video.url.replace(
        "/video/upload/",
        "/video/upload/w_400,h_225,c_fill,q_auto/"
    );
};

const handleImageError = (e) => {
    e.target.src =
        'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="225"%3E%3Crect width="400" height="225" fill="%23181818"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" fill="%23717171" font-size="16" dy=".3em"%3EНет превью%3C/text%3E%3C/svg%3E';
};

const formatDate = (dateString) => {
    if (!dateString) return "Неизвестно";

    // Парсим ISO дату
    const dateStr = dateString.replace('Z', '+00:00');
    const d = new Date(dateStr);

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
    <div class="video-card">
        <router-link :to="`/video/${video.id}`" class="video-thumbnail">
            <img
                :src="getThumbnail(video)"
                :alt="video.title"
                @error="handleImageError"
                loading="lazy"
            />
            <div class="video-overlay"></div>
            <div v-if="video.isBlocked" class="blocked-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                </svg>
                ЗАБЛОКИРОВАНО
            </div>
        </router-link>

        <div class="video-info">
            <router-link :to="`/video/${video.id}`" class="video-title">
                {{ video.title }}
            </router-link>

            <div class="video-meta">
                <span class="author">{{ video.author.email.split("@")[0] }}</span>
            </div>

            <div class="video-stats">
                <div class="stat-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
                    </svg>
                    <span>{{ formatCount(video.likes_count) }}</span>
                </div>
                <span class="stat-divider">•</span>
                <div class="stat-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                    </svg>
                    <span>{{ formatCount(video.comments_count) }}</span>
                </div>
                <span class="stat-divider">•</span>
                <span class="date">{{ formatDate(video.createdAt) }}</span>
            </div>

            <div v-if="showActions" class="video-actions">
                <button
                    v-if="canEdit"
                    @click.prevent="$emit('delete', video)"
                    class="action-btn delete-btn"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                    <span>Удалить</span>
                </button>
                <button
                    v-if="authStore.isAdmin"
                    @click.prevent="$emit('toggleBlock', video.id)"
                    class="action-btn block-btn"
                >
                    <svg v-if="video.isBlocked" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                        <line x1="1" y1="1" x2="23" y2="23"/>
                    </svg>
                    <span>{{ video.isBlocked ? "Показать" : "Скрыть" }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.video-card {
    background: #212121;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    cursor: pointer;
}

.video-card:hover {
    background: #2d2d2d;
}

.video-thumbnail {
    position: relative;
    display: block;
    aspect-ratio: 16 / 9;
    background-color: #181818;
    overflow: hidden;
}

.video-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.video-card:hover .video-thumbnail img {
    transform: scale(1.05);
}

.video-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 50%);
    opacity: 0;
    transition: opacity 0.2s;
}

.video-thumbnail:hover .video-overlay {
    opacity: 1;
}

.blocked-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(204, 0, 0, 0.95);
    color: white;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 4px;
    backdrop-filter: blur(4px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.video-info {
    padding: 12px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.video-title {
    font-size: 14px;
    font-weight: 500;
    color: #f1f1f1;
    text-decoration: none;
    line-height: 1.4;
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.2s;
}

.video-title:hover {
    color: #fff;
}

.video-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #aaaaaa;
    margin-bottom: 8px;
}

.author {
    font-weight: 500;
}

.video-stats {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #aaaaaa;
    margin-top: auto;
    padding-bottom: 8px;
    flex-wrap: wrap;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 4px;
}

.stat-divider {
    color: #717171;
}

.date {
    color: #aaaaaa;
}

.video-actions {
    display: flex;
    gap: 8px;
    padding-top: 12px;
    border-top: 1px solid #3d3d3d;
}

.action-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 12px;
    font-size: 13px;
    font-weight: 500;
    border-radius: 18px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    background: #3d3d3d;
    color: #f1f1f1;
}

.action-btn:hover {
    background: #4d4d4d;
}

.delete-btn {
    color: #ff6b6b;
}

.delete-btn:hover {
    background: rgba(255, 0, 0, 0.1);
    color: #ff4444;
}

.block-btn:hover {
    background: rgba(255, 255, 255, 0.1);
}

/* Responsive */
@media (max-width: 768px) {
    .video-info {
        padding: 10px;
    }

    .video-title {
        font-size: 13px;
    }

    .video-stats {
        font-size: 11px;
    }

    .action-btn {
        font-size: 12px;
        padding: 6px 10px;
    }
}
</style>