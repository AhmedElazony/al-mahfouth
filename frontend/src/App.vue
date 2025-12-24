<!-- filepath: frontend/src/App.vue -->
<template>
  <div 
    :class="[
      'min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300',
      'font-arabic'
    ]"
    dir="rtl"
  >
    <!-- Loading State -->
    <div v-if="initializing" class="min-h-screen flex items-center justify-center">
      <div class="text-center">
        <i class="pi pi-spinner pi-spin text-4xl text-primary-600 mb-4"></i>
        <p class="text-gray-600 dark:text-gray-400">{{ $t('common.loading') }}</p>
      </div>
    </div>

    <!-- App Content -->
    <router-view v-else />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useAuthStore } from '@/stores/auth'

const themeStore = useThemeStore()
const authStore = useAuthStore()
const initializing = ref(true)

onMounted(async () => {
  // Initialize theme
  themeStore.initTheme()
  
  // Initialize auth (fetch user if token exists)
  await authStore.initAuth()
  
  initializing.value = false
})
</script>