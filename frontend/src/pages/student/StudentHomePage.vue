<template>
  <div class="space-y-6">
    <!-- Welcome Section -->
    <div class="card p-6">
      <div class="flex items-center gap-4">
        <div class="w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
          <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
            {{ authStore.user?.name?.charAt(0).toUpperCase() }}
          </span>
        </div>
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $t('students.welcome') }}, {{ authStore.user?.name }}
          </h1>
          <p class="text-gray-500 dark:text-gray-400">
            {{ $t('students.homeSubtitle') }}
          </p>
        </div>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Educational Stage -->
      <div class="card p-6">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
            <i class="pi pi-graduation-cap text-blue-600 dark:text-blue-400 text-xl"></i>
          </div>
          <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $t('users.educationalStage') }}</p>
            <p class="text-base font-bold text-gray-900 dark:text-white">
              {{ authStore.user?.student?.educational_stage?.for_view || '-' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Memorization Start Date -->
      <div class="card p-6">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
            <i class="pi pi-calendar text-green-600 dark:text-green-400 text-xl"></i>
          </div>
          <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $t('users.beginMemorizingAt') }}</p>
            <p class="text-base font-bold text-gray-900 dark:text-white">
              {{ formatDate(authStore.user?.student?.begin_memorizing_at) }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Student Information -->
    <div class="card p-4">
      <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
        {{ $t('users.studentInfo') }}
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-sm">
        <div class="flex items-center justify-between py-1.5">
          <span class="text-gray-600 dark:text-gray-400">{{ $t('users.name') }}</span>
          <span class="font-medium text-gray-900 dark:text-white">{{ authStore.user?.name }}</span>
        </div>
        <div class="flex items-center justify-between py-1.5">
          <span class="text-gray-600 dark:text-gray-400">{{ $t('users.educationalStage') }}</span>
          <span class="font-medium text-gray-900 dark:text-white">
            {{ authStore.user?.student?.educational_stage?.for_view || '-' }}
          </span>
        </div>
        <div class="flex items-center justify-between py-1.5">
          <span class="text-gray-600 dark:text-gray-400">{{ $t('users.username') }}</span>
          <span class="font-medium text-gray-900 dark:text-white">{{ authStore.user?.username }}</span>
        </div>
        <div class="flex items-center justify-between py-1.5">
          <span class="text-gray-600 dark:text-gray-400">{{ $t('users.beginMemorizingAt') }}</span>
          <span class="font-medium text-gray-900 dark:text-white">
            {{ formatDate(authStore.user?.student?.begin_memorizing_at) }}
          </span>
        </div>
        <div class="flex items-center justify-between py-1.5">
          <span class="text-gray-600 dark:text-gray-400">{{ $t('users.email') }}</span>
          <span class="font-medium text-gray-900 dark:text-white">{{ authStore.user?.email }}</span>
        </div>
        <div class="flex items-center justify-between py-1.5">
          <span class="text-gray-600 dark:text-gray-400">{{ $t('users.memorizingCompletedAt') }}</span>
          <span class="font-medium text-gray-900 dark:text-white">
            {{ formatDate(authStore.user?.student?.memorizing_completed_at) }}
          </span>
        </div>
        <div class="flex items-center justify-between py-1.5">
          <span class="text-gray-600 dark:text-gray-400">{{ $t('users.phone') }}</span>
          <span class="font-medium text-gray-900 dark:text-white">{{ authStore.user?.phone || '-' }}</span>
        </div>
      </div>
    </div>

    <!-- Tajweed Information -->
    <div v-if="authStore.user?.student?.tajweed" class="card p-4">
      <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
        {{ $t('users.tajweedInfo') }}
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-sm">
        <div class="flex items-center justify-between py-1.5">
          <span class="text-gray-600 dark:text-gray-400">{{ $t('users.recitationLevel') }}</span>
          <span class="font-medium text-gray-900 dark:text-white">
            {{ authStore.user?.student?.tajweed?.recitation_level?.for_view || '-' }}
          </span>
        </div>
        <div class="flex items-center justify-between py-1.5">
          <span class="text-gray-600 dark:text-gray-400">{{ $t('users.tajweedLearningStatus') }}</span>
          <span class="font-medium text-gray-900 dark:text-white">
            {{ authStore.user?.student?.tajweed?.learning_status?.for_view || '-' }}
          </span>
        </div>
        <div v-if="authStore.user?.student?.tajweed?.notes" class="col-span-full pt-2">
          <p class="text-gray-600 dark:text-gray-400 mb-1.5">{{ $t('users.tajweedNotes') }}</p>
          <p class="text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 p-2.5 rounded-lg">
            {{ authStore.user?.student?.tajweed?.notes }}
          </p>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="card p-4">
      <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">
        {{ $t('students.quickActions') }}
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <router-link 
          to="/student/groups"
          class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors group"
        >
          <i class="pi pi-users text-xl text-gray-600 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400"></i>
          <div>
            <p class="font-medium text-sm text-gray-900 dark:text-white">{{ $t('students.viewGroups') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $t('students.viewGroupsDesc') }}</p>
          </div>
        </router-link>

        <router-link 
          to="/community"
          class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors group"
        >
          <i class="pi pi-comments text-xl text-gray-600 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400"></i>
          <div>
            <p class="font-medium text-sm text-gray-900 dark:text-white">{{ $t('community.title') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $t('community.subtitle') }}</p>
          </div>
        </router-link>

        <router-link 
          to="/student/settings"
          class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors group"
        >
          <i class="pi pi-cog text-xl text-gray-600 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400"></i>
          <div>
            <p class="font-medium text-sm text-gray-900 dark:text-white">{{ $t('nav.settings') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $t('students.settingsDesc') }}</p>
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

function formatDate(dateString: string | null | undefined): string {
  if (!dateString) return '-'
  try {
    return new Date(dateString).toLocaleDateString('ar-EG', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch {
    return '-'
  }
}
</script>