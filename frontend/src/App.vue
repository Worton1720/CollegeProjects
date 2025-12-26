<template>
  <div id="app">
    <Navbar />
    <main class="main-content">
      <router-view />
    </main>
    <Footer />
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';

const authStore = useAuthStore();

onMounted(() => {
  authStore.initializeAuth();
});
</script>

<style scoped>
#app {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.main-content {
  flex: 1;
  padding: 0rem 0;
}
</style>