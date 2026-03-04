<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $t('students.myGroups') }}</h1>
      <p class="text-gray-500 dark:text-gray-400">{{ $t('students.myGroupsSubtitle') }}</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <i class="pi pi-spinner pi-spin text-3xl text-primary-600"></i>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="card p-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
      <p class="text-red-600 dark:text-red-400">{{ error }}</p>
    </div>

    <!-- Groups List -->
    <div v-else-if="groups.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div 
        v-for="item in groups" 
        :key="item.group.id"
        class="card p-6 hover:shadow-lg transition-shadow cursor-pointer"
        @click="viewGroup(item)"
      >
        <!-- Group Header -->
        <div class="flex items-start justify-between mb-4">
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
              {{ item.group.name }}
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              <i class="pi pi-user text-xs mr-1"></i>
              {{ item.group.teacher?.name || '-' }}
            </p>
          </div>
          <span 
            :class="item.group.is_active 
              ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' 
              : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'"
            class="px-2 py-1 text-xs rounded-full"
          >
            {{ item.group.is_active ? $t('common.active') : $t('common.inactive') }}
          </span>
        </div>

        <!-- Student Status in Group -->
        <div class="space-y-2 mb-4">
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-600 dark:text-gray-400">{{ $t('students.myStatus') }}:</span>
            <span 
              :class="getStatusBadgeClass(item.student_status)"
              class="px-2 py-1 text-xs rounded-full"
            >
              {{ getStatusLabel(item.student_status) }}
            </span>
          </div>
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-600 dark:text-gray-400">{{ $t('students.memorization.amount') }}:</span>
            <span class="font-medium text-gray-900 dark:text-white">
              {{ getMemorizingAmountLabel(item.memorizing_amount) }}
            </span>
          </div>
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-600 dark:text-gray-400">{{ $t('groups.joinedAt') }}:</span>
            <span class="text-gray-900 dark:text-white">
              {{ formatDate(item.joined_at) }}
            </span>
          </div>
        </div>

        <!-- Schedule -->
        <div v-if="item.group.schedule?.length" class="pt-3 border-t border-gray-200 dark:border-gray-700">
          <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">{{ $t('groups.schedule') }}:</p>
          <div class="flex flex-wrap gap-1">
            <span 
              v-for="(scheduleItem, idx) in item.group.schedule.slice(0, 2)" 
              :key="idx"
              class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs"
            >
              {{ getDayLabel(scheduleItem.day) }} {{ formatTime(scheduleItem.start_time) }}
            </span>
            <span v-if="item.group.schedule.length > 2" class="px-2 py-1 text-xs text-gray-400">
              +{{ item.group.schedule.length - 2 }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="card p-12 text-center">
      <i class="pi pi-users text-4xl text-gray-300 dark:text-gray-600 mb-4 block"></i>
      <p class="text-gray-500 dark:text-gray-400">{{ $t('students.noGroups') }}</p>
    </div>

    <!-- Group Detail Modal -->
    <StudentGroupDetailModal 
      v-if="selectedGroup" 
      :group="selectedGroup"
      @close="selectedGroup = null"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import groupService from '@/services/groupService'
import StudentGroupDetailModal from '@/components/students/StudentGroupDetailModal.vue'
import { 
  getEnumLabel, 
  WeekDayLabels, 
  StudentStatusLabels,
  MemorizingAmountLabels,
  StudentStatus 
} from '@/constants'

const loading = ref(true)
const error = ref<string | null>(null)
const groups = ref<any[]>([])
const selectedGroup = ref<any | null>(null)

async function fetchGroups() {
  loading.value = true
  error.value = null

  try {
    const response = await groupService.getStudentGroups()
    groups.value = response.data || []
  } catch (err: any) {
    error.value = err.response?.data?.message || 'حدث خطأ في جلب المجموعات'
  } finally {
    loading.value = false
  }
}

function viewGroup(item: any) {
  selectedGroup.value = item
}

function getDayLabel(day: string | { value: string; for_view: string } | null | undefined): string {
  if (!day) return '-'
  if (typeof day === 'object' && day !== null) {
    return day.for_view
  }
  const dayValue = typeof day === 'string' ? day : ''
  return getEnumLabel(WeekDayLabels, dayValue?.toLowerCase())
}

function formatTime(time: string | { value: string; for_view: string } | null | undefined): string {
  if (!time) return ''
  
  // If it's already formatted from backend
  if (typeof time === 'object' && time !== null) {
    return time.for_view
  }
  
  // Otherwise format it
  const timeValue = typeof time === 'string' ? time : ''
  const parts = timeValue.split(':')
  if (parts.length >= 2) {
    const hours = parseInt(parts[0] || '0')
    const minutes = parts[1]
    const period = hours >= 12 ? 'م' : 'ص'
    const displayHours = hours > 12 ? hours - 12 : hours === 0 ? 12 : hours
    return `${displayHours}:${minutes} ${period}`
  }
  return timeValue
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
  // Handle null/undefined status
  if (!status) {
    return 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
  }
  
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

onMounted(() => {
  fetchGroups()
})
</script>