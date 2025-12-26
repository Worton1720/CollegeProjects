import { defineStore } from 'pinia';
import { videosAPI } from '@/api';

export const useVideoStore = defineStore('video', {
  state: () => ({
    videos: [],
    currentVideo: null,
    pagination: {
      page: 1,
      limit: 12,
      total: 0,
      totalPages: 0,
    },
    filters: {
      search: '',
      authorId: null,
      isBlocked: null,
    },
    loading: false,
    error: null,
    uploadProgress: 0,
  }),

  actions: {
    async fetchVideos(params = {}) {
      this.loading = true;
      this.error = null;
      try {
        const queryParams = {
          page: this.pagination.page,
          limit: this.pagination.limit,
          ...this.filters,
          ...params,
        };

        const { data } = await videosAPI.getAll(queryParams);
        this.videos = data.videos;
        this.pagination = data.pagination;
      } catch (error) {
        this.error = error.response?.data?.error || 'Ошибка загрузки видео';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchVideoById(id) {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await videosAPI.getById(id);
        this.currentVideo = data.video;
        return data.video;
      } catch (error) {
        this.error = error.response?.data?.error || 'Ошибка загрузки видео';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async uploadVideo(formData, onProgress) {
      this.loading = true;
      this.error = null;
      this.uploadProgress = 0;
      
      try {
        const { data } = await videosAPI.upload(formData);
        
        // Добавляем новое видео в начало списка
        this.videos.unshift(data.video);
        this.uploadProgress = 100;
        
        return data.video;
      } catch (error) {
        this.error = error.response?.data?.error || 'Ошибка загрузки видео';
        throw error;
      } finally {
        this.loading = false;
        setTimeout(() => {
          this.uploadProgress = 0;
        }, 1000);
      }
    },

    async deleteVideo(id) {
      this.loading = true;
      this.error = null;
      try {
        await videosAPI.delete(id);
        this.videos = this.videos.filter(v => v.id !== id);
        
        if (this.currentVideo?.id === id) {
          this.currentVideo = null;
        }
      } catch (error) {
        this.error = error.response?.data?.error || 'Ошибка удаления видео';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async toggleBlock(id) {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await videosAPI.toggleBlock(id);
        
        // Обновляем видео в списке
        const index = this.videos.findIndex(v => v.id === id);
        if (index !== -1) {
          this.videos[index] = { ...this.videos[index], ...data.video };
        }
        
        // Обновляем текущее видео
        if (this.currentVideo?.id === id) {
          this.currentVideo = { ...this.currentVideo, ...data.video };
        }
        
        return data.video;
      } catch (error) {
        this.error = error.response?.data?.error || 'Ошибка изменения статуса видео';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    setFilters(filters) {
      this.filters = { ...this.filters, ...filters };
      this.pagination.page = 1;
      this.fetchVideos();
    },

    setPage(page) {
      this.pagination.page = page;
      this.fetchVideos();
    },

    clearCurrentVideo() {
      this.currentVideo = null;
    },
  },
});