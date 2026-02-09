<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-black/50" @click="$emit('close')"></div>

    <div class="relative min-h-screen flex items-center justify-center p-4">
      <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
          <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ group.name }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $t('students.groupDetails') }}</p>
          </div>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="pi pi-times text-xl"></i>
          </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
          <!-- Teacher Info -->
          <div class="card p-4 bg-gray-50 dark:bg-gray-700">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ $t('groups.teacher') }}</p>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                <span class="text-primary-600 dark:text-primary-400 font-semibold">
                  {{ group.teacher?.name?.charAt(0).toUpperCase() }}
                </span>
              </div>
              <div>
                <p class="font-medium text-gray-900 dark:text-white">{{ group.teacher?.name }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ group.teacher?.email }}</p>
              </div>
            </div>
          </div>

          <!-- My Status -->
          <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ $t('students.myStatus') }}</h3>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $t('students.studentStatus') }}</span>
                <span 
                  :class="getStatusBadgeClass(group.student_status)"
                  class="px-3 py-1 text-sm rounded-full"
                >
                  {{ getStatusLabel(group.student_status) }}
                </span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $t('students.memorizingAmount') }}</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ getMemorizingAmountLabel(group.memorizing_amount) }}
                </span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $t('groups.joinedAt') }}</span>
                <span class="text-sm text-gray-900 dark:text-white">
                  {{ formatDate(group.joined_at) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Schedule -->
          <div v-if="group.schedule?.length">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ $t('groups.schedule') }}</h3>
            <div class="space-y-2">
              <div 
                v-for="(item, idx) in group.schedule" 
                :key="idx"
                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
              >
                <span class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ getDayLabel(item.day) }}
                </span>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                  {{ formatTime(item.start_time) }} - {{ formatTime(item.end_time) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Group Info -->
          <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ $t('groups.groupInfo') }}</h3>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $t('groups.type') }}</span>
                <span 
                  :class="group.is_online 
                    ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' 
                    : 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'"
                  class="px-3 py-1 text-sm rounded-full"
                >
                  {{ group.is_online ? $t('groups.online') : $t('groups.offline') }}
                </span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $t('common.status') }}</span>
                <span 
                  :class="group.is_active 
                    ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' 
                    : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'"
                  class="px-3 py-1 text-sm rounded-full"
                >
                  {{ group.is_active ? $t('common.active') : $t('common.inactive') }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700">
          <button @click="$emit('close')" class="btn-secondary">
            {{ $t('common.close') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { 
  getEnumLabel, 
  WeekDayLabels, 
  StudentStatusLabels,
  MemorizingAmountLabels,
  StudentStatus 
} from '@/constants'

interface Props {
  group: any
}

defineProps<Props>()
defineEmits<{
  close: []
}>()

function getDayLabel(day: string): string {
  const dayValue = typeof day === 'object' ? (day as any).value : day
  return getEnumLabel(WeekDayLabels, dayValue?.toLowerCase())
}

function formatTime(time: string): string {
  if (!time) return ''
  if (typeof time === 'object') {
    time = (time as any).value || ''
  }
  const parts = time.split(':')
  if (parts.length >= 2) {
    const hours = parseInt(parts[0])
    const minutes = parts[1]
    const period = hours >= 12 ? 'م' : 'ص'
    const displayHours = hours > 12 ? hours - 12 : hours === 0 ? 12 : hours
    return `${displayHours}:${minutes} ${period}`
  }
  return time
}

function formatDate(date: string | null | undefined): string {
  if (!date) return '-'
  try {
    return new Date(date).toLocaleDateString('ar-EG', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch {
    return '-'
  }
}

function getStatusLabel(status: string | null | undefined): string {
  if (!status) return '-'
  const statusValue = typeof status === 'object' ? (status as any).value : status
  return getEnumLabel(StudentStatusLabels, statusValue)
}

function getMemorizingAmountLabel(amount: string | null | undefined): string {
  if (!amount) return '-'
  const amountValue = typeof amount === 'object' ? (amount as any).value : amount
  return getEnumLabel(MemorizingAmountLabels, amountValue)
}

function getStatusBadgeClass(status: string | null | undefined): string {
  const statusValue = typeof status === 'object' ? (status as any).value : status
  switch (statusValue) {
    case StudentStatus.COMMITTED:
      return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
    case StudentStatus.ABSENT:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
    case StudentStatus.UNCOMMITTED:
      return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
  }
}
</script>