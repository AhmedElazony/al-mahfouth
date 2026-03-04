<template>
  <div class="fixed inset-0 z-50 overflow-y-auto" @click="$emit('cancel')">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50" style="z-index: 1;"></div>

    <!-- Modal -->
    <div class="relative min-h-screen flex items-center justify-center p-4" style="z-index: 2;">
      <div @click.stop class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6">
        <!-- Icon -->
        <div class="flex justify-center mb-4">
          <div :class="iconContainerClass" class="w-16 h-16 rounded-full flex items-center justify-center">
            <i :class="[iconClass, 'text-2xl']"></i>
          </div>
        </div>

        <!-- Title -->
        <h3 class="text-xl font-bold text-center text-gray-900 dark:text-white mb-2">
          {{ title }}
        </h3>

        <!-- Message -->
        <p class="text-center text-gray-500 dark:text-gray-400 mb-6">
          {{ message }}
        </p>

        <!-- Actions -->
        <div class="flex items-center justify-center gap-4">
          <button @click="$emit('cancel')" class="btn-secondary px-6">
            {{ cancelText }}
          </button>
          <button @click="$emit('confirm')" :class="confirmButtonClass" class="px-6">
            {{ confirmText }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  title: string
  message: string
  confirmText?: string
  cancelText?: string
  variant?: 'danger' | 'warning' | 'info'
}

const props = withDefaults(defineProps<Props>(), {
  confirmText: 'تأكيد',
  cancelText: 'إلغاء',
  variant: 'danger'
})

defineEmits<{
  confirm: []
  cancel: []
}>()

const iconContainerClass = computed(() => {
  const classes = {
    danger: 'bg-red-100 dark:bg-red-900/20',
    warning: 'bg-yellow-100 dark:bg-yellow-900/20',
    info: 'bg-blue-100 dark:bg-blue-900/20'
  }
  return classes[props.variant]
})

const iconClass = computed(() => {
  const classes = {
    danger: 'pi pi-exclamation-triangle text-red-600 dark:text-red-400',
    warning: 'pi pi-exclamation-circle text-yellow-600 dark:text-yellow-400',
    info: 'pi pi-info-circle text-blue-600 dark:text-blue-400'
  }
  return classes[props.variant]
})

const confirmButtonClass = computed(() => {
  const classes = {
    danger: 'btn-danger',
    warning: 'btn-warning',
    info: 'btn-primary'
  }
  return classes[props.variant]
})
</script>