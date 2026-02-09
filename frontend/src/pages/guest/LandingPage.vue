<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-50 via-white to-primary-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Navigation -->
    <nav class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo -->
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
              <i class="pi pi-book text-white text-xl"></i>
            </div>
            <span class="text-xl font-bold text-gray-900 dark:text-white">{{ $t('app.name') }}</span>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-3">
            <!-- Theme Toggle -->
            <button
              @click="themeStore.toggleTheme"
              class="p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors"
            >
              <i :class="themeStore.isDark ? 'pi pi-sun' : 'pi pi-moon'" class="text-lg"></i>
            </button>

            <!-- Dashboard Button (for authenticated users) -->
            <router-link
              v-if="authStore.isAuthenticated"
              to="/dashboard"
              class="btn-primary px-6 py-2"
            >
              <i class="pi pi-th-large mr-2"></i>
              {{ $t('landing.dashboard') }}
            </router-link>

            <!-- Login Button (for guests) -->
            <router-link
              v-else
              to="/auth/login"
              class="btn-primary px-6 py-2"
            >
              <i class="pi pi-sign-in mr-2"></i>
              {{ $t('landing.login') }}
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
      <div class="text-center space-y-8">
        <!-- Main Heading -->
        <div class="space-y-4">
          <h1 class="text-4xl md:text-6xl font-bold text-gray-900 dark:text-white">
            {{ $t('landing.title') }}
          </h1>
          <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
            {{ $t('landing.subtitle') }}
          </p>
        </div>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-8">
          <!-- Dashboard Button (for authenticated users) -->
          <router-link
            v-if="authStore.isAuthenticated"
            to="/dashboard"
            class="btn-primary px-8 py-4 text-lg w-full sm:w-auto"
          >
            <i class="pi pi-th-large mr-2"></i>
            {{ $t('landing.goToDashboard') }}
          </router-link>

          <!-- Login Button (for guests) -->
          <router-link
            v-else
            to="/auth/login"
            class="btn-primary px-8 py-4 text-lg w-full sm:w-auto"
          >
            <i class="pi pi-sign-in mr-2"></i>
            {{ $t('landing.getStarted') }}
          </router-link>
        </div>

        <!-- Hero Image/Illustration -->
        <div class="pt-16 relative">
          <div class="relative mx-auto max-w-5xl">
            <!-- Decorative circles -->
            <div class="absolute top-0 -left-4 w-72 h-72 bg-primary-200 dark:bg-primary-900 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute top-0 -right-4 w-72 h-72 bg-purple-200 dark:bg-purple-900 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-200 dark:bg-pink-900 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
            
            <!-- Main hero card -->
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 border border-gray-200 dark:border-gray-700">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="text-center p-6 bg-primary-50 dark:bg-primary-900/20 rounded-xl">
                  <div class="w-16 h-16 bg-primary-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="pi pi-users text-white text-2xl"></i>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    {{ $t('landing.feature1Title') }}
                  </h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $t('landing.feature1Desc') }}
                  </p>
                </div>

                <!-- Feature 2 -->
                <div class="text-center p-6 bg-purple-50 dark:bg-purple-900/20 rounded-xl">
                  <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="pi pi-file-edit text-white text-2xl"></i>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    {{ $t('landing.feature2Title') }}
                  </h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $t('landing.feature2Desc') }}
                  </p>
                </div>

                <!-- Feature 3 -->
                <div class="text-center p-6 bg-pink-50 dark:bg-pink-900/20 rounded-xl">
                  <div class="w-16 h-16 bg-pink-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="pi pi-chart-line text-white text-2xl"></i>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    {{ $t('landing.feature3Title') }}
                  </h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $t('landing.feature3Desc') }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 mt-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center space-y-4">
          <div class="flex items-center justify-center gap-3">
            <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
              <i class="pi pi-book text-white"></i>
            </div>
            <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $t('app.name') }}</span>
          </div>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ $t('landing.footer') }}
          </p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { useThemeStore } from '@/stores/theme'
import { useAuthStore } from '@/stores/auth'

const themeStore = useThemeStore()
const authStore = useAuthStore()
</script>

<style scoped>
@keyframes blob {
  0% {
    transform: translate(0px, 0px) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
  100% {
    transform: translate(0px, 0px) scale(1);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}
</style>