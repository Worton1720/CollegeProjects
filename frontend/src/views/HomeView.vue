<template>
    <div class="home-view">
        <div class="container">
            <!-- Sticky Search Bar -->
            <div class="search-bar">
                <div class="search-wrapper">
                    <svg
                        class="search-icon"
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Поиск видео..."
                        class="search-input"
                        @input="handleSearch"
                    />
                    <button
                        v-if="searchQuery"
                        @click="clearSearch"
                        class="clear-btn"
                    >
                        <svg
                            width="20"
                            height="20"
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

                <div class="results-info" v-if="!videoStore.loading">
                    <svg
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <polygon
                            points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"
                        />
                    </svg>
                    <span>{{ videoStore.videos.length }} видео</span>
                </div>
            </div>

            <!-- Loading State with Skeletons -->
            <div v-if="videoStore.loading" class="videos-grid">
                <div v-for="i in 12" :key="i" class="skeleton-card">
                    <div class="skeleton-thumbnail"></div>
                    <div class="skeleton-info">
                        <div class="skeleton-title"></div>
                        <div class="skeleton-meta"></div>
                    </div>
                </div>
            </div>

            <!-- Error State -->
            <div v-else-if="videoStore.error" class="error-state">
                <div class="error-icon">
                    <svg
                        width="64"
                        height="64"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#ff0000"
                        stroke-width="1.5"
                    >
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h2>Ошибка загрузки</h2>
                <p>{{ videoStore.error }}</p>
                <button @click="videoStore.fetchVideos()" class="btn-retry">
                    <svg
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <polyline points="23 4 23 10 17 10" />
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10" />
                    </svg>
                    Попробовать снова
                </button>
            </div>

            <!-- Empty State -->
            <div v-else-if="videoStore.videos.length === 0" class="empty-state">
                <div class="empty-animation">
                    <svg
                        width="120"
                        height="120"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1"
                    >
                        <rect
                            x="2"
                            y="7"
                            width="20"
                            height="15"
                            rx="2"
                            ry="2"
                        />
                        <polyline points="17 2 12 7 7 2" />
                    </svg>
                </div>
                <h2>Видео не найдены</h2>
                <p>Попробуйте изменить параметры поиска или очистите фильтры</p>
                <button @click="clearSearch" class="btn-clear">
                    <svg
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <polyline points="1 4 1 10 7 10" />
                        <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10" />
                    </svg>
                    Очистить поиск
                </button>
            </div>

            <!-- Videos Grid -->
            <div v-else>
                <div class="videos-grid">
                    <VideoCard
                        v-for="(video, index) in videoStore.videos"
                        :key="video.id"
                        :video="video"
                        :style="{ animationDelay: `${index * 0.03}s` }"
                        class="video-item"
                    />
                </div>

                <!-- Pagination -->
                <div
                    v-if="videoStore.pagination.totalPages > 1"
                    class="pagination"
                >
                    <button
                        @click="previousPage"
                        :disabled="videoStore.pagination.page === 1"
                        class="btn-page-nav"
                    >
                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <polyline points="15 18 9 12 15 6" />
                        </svg>
                    </button>

                    <div class="page-numbers">
                        <button
                            v-for="(page, index) in visiblePages()"
                            :key="index"
                            @click="page !== '...' && goToPage(page)"
                            :class="[
                                'page-btn',
                                {
                                    active: page === videoStore.pagination.page,
                                    dots: page === '...',
                                },
                            ]"
                            :disabled="page === '...'"
                        >
                            {{ page }}
                        </button>
                    </div>

                    <button
                        @click="nextPage"
                        :disabled="
                            videoStore.pagination.page >=
                            videoStore.pagination.totalPages
                        "
                        class="btn-page-nav"
                    >
                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useVideoStore } from "@/stores/videoStore";
import VideoCard from "@/components/VideoCard.vue";

const videoStore = useVideoStore();
const searchQuery = ref("");
let searchTimeout = null;

onMounted(() => {
    videoStore.fetchVideos();
});

const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        videoStore.setFilters({ search: searchQuery.value });
    }, 500);
};

const clearSearch = () => {
    searchQuery.value = "";
    videoStore.setFilters({ search: "" });
};

const goToPage = (page) => {
    videoStore.setPage(page);
    window.scrollTo({ top: 0, behavior: "smooth" });
};

const nextPage = () => {
    if (videoStore.pagination.page < videoStore.pagination.totalPages) {
        goToPage(videoStore.pagination.page + 1);
    }
};

const previousPage = () => {
    if (videoStore.pagination.page > 1) {
        goToPage(videoStore.pagination.page - 1);
    }
};

const visiblePages = () => {
    const current = videoStore.pagination.page;
    const total = videoStore.pagination.totalPages;
    const pages = [];

    if (total <= 7) {
        for (let i = 1; i <= total; i++) pages.push(i);
    } else {
        if (current <= 4) {
            for (let i = 1; i <= 5; i++) pages.push(i);
            pages.push("...");
            pages.push(total);
        } else if (current >= total - 3) {
            pages.push(1);
            pages.push("...");
            for (let i = total - 4; i <= total; i++) pages.push(i);
        } else {
            pages.push(1);
            pages.push("...");
            for (let i = current - 1; i <= current + 1; i++) pages.push(i);
            pages.push("...");
            pages.push(total);
        }
    }

    return pages;
};
</script>

<style scoped>
.home-view {
    background: #0f0f0f;
    min-height: 100vh;
    padding-bottom: 60px;
    color: #f1f1f1;
}

.container {
    max-width: 1800px;
    margin: 0 auto;
    padding: 0 24px;
}

/* Search Bar */
.search-bar {
    position: sticky;
    top: 56px;
    background: #0f0f0f;
    padding: 16px 0;
    margin-bottom: 24px;
    z-index: 50;
    border-bottom: 1px solid #3d3d3d;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    backdrop-filter: blur(10px);
}

.search-wrapper {
    position: relative;
    flex: 1;
    max-width: 600px;
}

.search-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #aaaaaa;
    pointer-events: none;
}

.search-input {
    width: 100%;
    padding: 12px 48px 12px 48px;
    background: #121212;
    border: 1px solid #3d3d3d;
    border-radius: 40px;
    font-size: 14px;
    color: #f1f1f1;
    outline: none;
    transition: all 0.2s;
}

.search-input::placeholder {
    color: #717171;
}

.search-input:focus {
    border-color: #065fd4;
    background: #212121;
}

.clear-btn {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #aaaaaa;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s;
}

.clear-btn:hover {
    background: #3d3d3d;
    color: #f1f1f1;
}

.results-info {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #aaaaaa;
    white-space: nowrap;
}

.results-info svg {
    color: #065fd4;
}

/* Videos Grid */
.videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
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

/* Skeleton Loading */
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

/* Error State */
.error-state {
    text-align: center;
    padding: 80px 20px;
}

.error-icon {
    margin-bottom: 24px;
    opacity: 0.5;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%,
    100% {
        opacity: 0.5;
    }
    50% {
        opacity: 0.8;
    }
}

.error-state h2 {
    font-size: 24px;
    color: #f1f1f1;
    margin-bottom: 12px;
}

.error-state p {
    font-size: 14px;
    color: #aaaaaa;
    margin-bottom: 24px;
}

.btn-retry {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: #065fd4;
    border: none;
    border-radius: 18px;
    color: white;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-retry:hover {
    background: #0c7ceb;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 20px;
}

.empty-animation {
    margin-bottom: 24px;
    opacity: 0.3;
}

.empty-animation svg {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.empty-state h2 {
    font-size: 24px;
    color: #f1f1f1;
    margin-bottom: 12px;
}

.empty-state p {
    font-size: 14px;
    color: #aaaaaa;
    margin-bottom: 24px;
}

.btn-clear {
    display: inline-flex;
    align-items: center;
    gap: 8px;
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

.btn-clear:hover {
    background: #565656;
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    padding: 32px 0;
}

.btn-page-nav {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #272727;
    border: 1px solid #3d3d3d;
    border-radius: 50%;
    color: #f1f1f1;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-page-nav:hover:not(:disabled) {
    background: #3d3d3d;
    border-color: #565656;
}

.btn-page-nav:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.page-numbers {
    display: flex;
    gap: 8px;
}

.page-btn {
    min-width: 40px;
    height: 40px;
    padding: 0 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #272727;
    border: 1px solid #3d3d3d;
    border-radius: 8px;
    color: #f1f1f1;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
}

.page-btn:hover:not(:disabled):not(.dots) {
    background: #3d3d3d;
}

.page-btn.active {
    background: #ff0000;
    border-color: #ff0000;
    font-weight: 500;
}

.page-btn.dots {
    cursor: default;
    border: none;
    background: transparent;
}

/* Responsive */
@media (max-width: 768px) {
    .container {
        padding: 0 16px;
    }

    .search-bar {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }

    .search-wrapper {
        max-width: none;
    }

    .results-info {
        justify-content: center;
    }

    .videos-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .page-numbers {
        flex-wrap: wrap;
    }
}
</style>
