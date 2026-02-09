<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $t('community.title') }}</h1>
      <p class="text-gray-500 dark:text-gray-400">{{ $t('community.subtitle') }}</p>
    </div>

    <!-- Coming Soon -->
    <div class="card p-12 text-center">
      <div class="max-w-md mx-auto space-y-4">
        <div class="w-20 h-20 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mx-auto">
          <i class="pi pi-comments text-4xl text-primary-600 dark:text-primary-400"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $t('common.comingSoon') }}</h2>
        <p class="text-gray-500 dark:text-gray-400">
          {{ $t('community.comingSoon') }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
</script>