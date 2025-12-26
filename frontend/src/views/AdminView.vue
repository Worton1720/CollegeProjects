<script setup>
import { ref, onMounted } from "vue";
import { videosAPI } from "@/api";
import VideoCard from "@/components/VideoCard.vue";

const videos = ref([]);
const loading = ref(true);
const showBlocked = ref(false);
const searchQuery = ref("");
const pagination = ref({ page: 1, limit: 12, total: 0, totalPages: 0 });

// Modal & Toast states
const deleteModal = ref({ show: false, videoId: null, videoTitle: '' });
const toast = ref({ show: false, message: '', type: 'success' });

const fetchVideos = async () => {
    loading.value = true;
    try {
        const { data } = await videosAPI.getAll({
            page: pagination.value.page,
            limit: pagination.value.limit,
            isBlocked: showBlocked.value ? undefined : false,
            search: searchQuery.value,
        });
        videos.value = data.videos;
        pagination.value = data.pagination;
    } catch (error) {
        console.error(error);
        showToast('Ошибка загрузки видео', 'error');
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchVideos();
});

let searchTimeout;
const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        pagination.value.page = 1;
        fetchVideos();
    }, 500);
};

const handleFilterChange = () => {
    pagination.value.page = 1;
    fetchVideos();
};

const openDeleteModal = (video) => {
    deleteModal.value = {
        show: true,
        videoId: video.id,
        videoTitle: video.title
    };
};

const closeDeleteModal = () => {
    deleteModal.value = { show: false, videoId: null, videoTitle: '' };
};

const confirmDelete = async () => {
    try {
        await videosAPI.delete(deleteModal.value.videoId);
        closeDeleteModal();
        showToast('Видео успешно удалено', 'success');
        fetchVideos();
    } catch (error) {
        showToast('Ошибка при удалении видео', 'error');
    }
};

const handleToggleBlock = async (id) => {
    try {
        await videosAPI.toggleBlock(id);
        showToast('Статус видео изменен', 'success');
        fetchVideos();
    } catch (error) {
        showToast('Ошибка изменения статуса', 'error');
    }
};

const showToast = (message, type = 'success') => {
    toast.value = { show: true, message, type };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

const goToPage = (page) => {
    pagination.value.page = page;
    fetchVideos();
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const nextPage = () => {
    if (pagination.value.page < pagination.value.totalPages) {
        goToPage(pagination.value.page + 1);
    }
};

const previousPage = () => {
    if (pagination.value.page > 1) {
        goToPage(pagination.value.page - 1);
    }
};

// Generate visible page numbers
const visiblePages = () => {
    const current = pagination.value.page;
    const total = pagination.value.totalPages;
    const pages = [];
    
    if (total <= 7) {
        for (let i = 1; i <= total; i++) pages.push(i);
    } else {
        if (current <= 4) {
            for (let i = 1; i <= 5; i++) pages.push(i);
            pages.push('...');
            pages.push(total);
        } else if (current >= total - 3) {
            pages.push(1);
            pages.push('...');
            for (let i = total - 4; i <= total; i++) pages.push(i);
        } else {
            pages.push(1);
            pages.push('...');
            for (let i = current - 1; i <= current + 1; i++) pages.push(i);
            pages.push('...');
            pages.push(total);
        }
    }
    
    return pages;
};
</script>

<template>
    <div class="admin-view">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="header-text">
                    <h1>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="header-icon">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        Панель администратора
                    </h1>
                    <p>Управление видеоконтентом</p>
                </div>
            </div>
        </div>

        <!-- Sticky Filters -->
        <div class="filters-wrapper">
            <div class="filters-content">
                <div class="search-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="search-icon">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Поиск видео..."
                        @input="handleSearch"
                        class="search-input"
                    />
                    <button 
                        v-if="searchQuery" 
                        @click="searchQuery = ''; handleSearch()"
                        class="clear-search"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>

                <div class="filter-controls">
                    <button 
                        class="filter-chip"
                        :class="{ active: showBlocked }"
                        @click="showBlocked = !showBlocked; handleFilterChange()"
                    >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                        </svg>
                        <span>Заблокированные</span>
                        <span class="chip-indicator" v-if="showBlocked"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Loading Skeleton -->
            <div v-if="loading" class="videos-grid">
                <div v-for="i in 8" :key="i" class="skeleton-card">
                    <div class="skeleton-thumbnail"></div>
                    <div class="skeleton-info">
                        <div class="skeleton-title"></div>
                        <div class="skeleton-meta"></div>
                        <div class="skeleton-stats"></div>
                    </div>
                </div>
            </div>

            <!-- Videos Grid -->
            <div v-else-if="videos.length > 0" class="videos-grid">
                <VideoCard
                    v-for="(video, index) in videos"
                    :key="video.id"
                    :video="video"
                    :show-actions="true"
                    :style="{ animationDelay: `${index * 0.05}s` }"
                    class="video-item"
                    @delete="openDeleteModal(video)"
                    @toggleBlock="handleToggleBlock"
                />
            </div>

            <!-- Empty State -->
            <div v-else class="empty-state">
                <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <rect x="2" y="7" width="20" height="15" rx="2" ry="2"/>
                    <polyline points="17 2 12 7 7 2"/>
                </svg>
                <h3>Видео не найдены</h3>
                <p>Попробуйте изменить параметры поиска</p>
            </div>

            <!-- Pagination -->
            <div v-if="pagination.totalPages > 1" class="pagination">
                <button
                    @click="previousPage"
                    :disabled="pagination.page === 1"
                    class="btn-page-nav"
                >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                </button>

                <div class="page-numbers">
                    <button
                        v-for="(page, index) in visiblePages()"
                        :key="index"
                        @click="page !== '...' && goToPage(page)"
                        :class="['page-btn', { 
                            active: page === pagination.page,
                            dots: page === '...'
                        }]"
                        :disabled="page === '...'"
                    >
                        {{ page }}
                    </button>
                </div>

                <button
                    @click="nextPage"
                    :disabled="pagination.page >= pagination.totalPages"
                    class="btn-page-nav"
                >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Delete Modal -->
        <Transition name="modal">
            <div v-if="deleteModal.show" class="modal-overlay" @click="closeDeleteModal">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h3>Удалить видео?</h3>
                        <button @click="closeDeleteModal" class="modal-close">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Вы уверены, что хотите удалить видео <strong>"{{ deleteModal.videoTitle }}"</strong>?</p>
                        <p class="modal-warning">Это действие нельзя отменить.</p>
                    </div>
                    <div class="modal-footer">
                        <button @click="closeDeleteModal" class="btn-cancel">
                            Отмена
                        </button>
                        <button @click="confirmDelete" class="btn-confirm-delete">
                            Удалить
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Toast Notification -->
        <Transition name="toast">
            <div v-if="toast.show" :class="['toast', toast.type]">
                <svg v-if="toast.type === 'success'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>{{ toast.message }}</span>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.admin-view {
    font-family: "Roboto", "Arial", sans-serif;
    background: #0f0f0f;
    min-height: 100vh;
    color: #f1f1f1;
}

/* Header */
.header {
    background: #212121;
    border-bottom: 1px solid #3d3d3d;
    padding: 20px 0;
    position: sticky;
    top: 0;
    z-index: 100;
}

.header-content {
    max-width: 1800px;
    margin: 0 auto;
    padding: 0 24px;
}

.header-text h1 {
    font-size: 24px;
    font-weight: 500;
    color: #f1f1f1;
    margin: 0 0 4px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-icon {
    color: #ff0000;
}

.header-text p {
    font-size: 14px;
    color: #aaaaaa;
    margin: 0;
}

/* Sticky Filters */
.filters-wrapper {
    position: sticky;
    top: 65px;
    background: #0f0f0f;
    border-bottom: 1px solid #3d3d3d;
    padding: 16px 0;
    z-index: 90;
    backdrop-filter: blur(10px);
}

.filters-content {
    max-width: 1800px;
    margin: 0 auto;
    padding: 0 24px;
    display: flex;
    gap: 16px;
    align-items: center;
    flex-wrap: wrap;
}

.search-box {
    position: relative;
    flex: 1;
    min-width: 300px;
    max-width: 500px;
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
    padding: 10px 40px 10px 48px;
    font-size: 14px;
    border: 1px solid #3d3d3d;
    border-radius: 40px;
    outline: none;
    transition: all 0.2s;
    background: #121212;
    color: #f1f1f1;
}

.search-input::placeholder {
    color: #717171;
}

.search-input:focus {
    border-color: #065fd4;
    background: #212121;
}

.clear-search {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #aaaaaa;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s;
}

.clear-search:hover {
    background: #3d3d3d;
    color: #f1f1f1;
}

.filter-controls {
    display: flex;
    gap: 12px;
}

.filter-chip {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #272727;
    border: 1px solid #3d3d3d;
    border-radius: 8px;
    color: #f1f1f1;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
}

.filter-chip:hover {
    background: #3d3d3d;
}

.filter-chip.active {
    background: #065fd4;
    border-color: #065fd4;
}

.chip-indicator {
    position: absolute;
    top: -4px;
    right: -4px;
    width: 8px;
    height: 8px;
    background: #ff0000;
    border-radius: 50%;
    border: 2px solid #0f0f0f;
}

/* Content */
.content {
    max-width: 1800px;
    margin: 0 auto;
    padding: 24px;
}

/* Videos Grid */
.videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.video-item {
    animation: fadeInUp 0.5s ease-out backwards;
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
    margin-bottom: 12px;
    animation: shimmer 1.5s infinite;
}

.skeleton-stats {
    height: 12px;
    width: 40%;
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
    color: #717171;
}

.empty-state svg {
    margin-bottom: 24px;
    opacity: 0.3;
}

.empty-state h3 {
    font-size: 20px;
    color: #f1f1f1;
    margin-bottom: 8px;
}

.empty-state p {
    font-size: 14px;
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
    align-items: center;
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

/* Toast */
.toast {
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    background: #212121;
    color: #f1f1f1;
    padding: 12px 24px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    z-index: 2000;
    border: 1px solid #3d3d3d;
}

.toast.success {
    border-left: 4px solid #00c853;
}

.toast.error {
    border-left: 4px solid #ff0000;
}

.toast svg {
    flex-shrink: 0;
}

.toast.success svg {
    color: #00c853;
}

.toast.error svg {
    color: #ff0000;
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

.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translate(-50%, 20px);
}

/* Responsive */
@media (max-width: 768px) {
    .header-content,
    .filters-content,
    .content {
        padding: 0 16px;
    }

    .filters-content {
        flex-direction: column;
        align-items: stretch;
    }

    .search-box {
        max-width: none;
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