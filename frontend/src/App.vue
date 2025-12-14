<template>
  <div 
    :class="[
      'min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300',
      'font-arabic'
    ]"
    dir="rtl"
  >
    <router-view />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useThemeStore } from '@/stores/theme'

const themeStore = useThemeStore()

onMounted(() => {
  themeStore.initTheme()
})
</script>
