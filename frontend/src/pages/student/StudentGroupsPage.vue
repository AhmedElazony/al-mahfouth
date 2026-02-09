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
        v-for="group in groups" 
        :key="group.id"
        class="card p-6 hover:shadow-lg transition-shadow cursor-pointer"
        @click="viewGroup(group)"
      >
        <!-- Group Header -->
        <div class="flex items-start justify-between mb-4">
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
              {{ group.name }}
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              <i class="pi pi-user text-xs mr-1"></i>
              {{ group.teacher?.name || '-' }}
            </p>
          </div>
          <span 
            :class="group.is_active 
              ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' 
              : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'"
            class="px-2 py-1 text-xs rounded-full"
          >
            {{ group.is_active ? $t('common.active') : $t('common.inactive') }}
          </span>
        </div>

        <!-- Student Status in Group -->
        <div class="space-y-2 mb-4">
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-600 dark:text-gray-400">{{ $t('students.studentStatus') }}:</span>
            <span 
              :class="getStatusBadgeClass(group.student_status)"
              class="px-2 py-1 text-xs rounded-full"
            >
              {{ getStatusLabel(group.student_status) }}
            </span>
          </div>
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-600 dark:text-gray-400">{{ $t('students.memorizingAmount') }}:</span>
            <span class="font-medium text-gray-900 dark:text-white">
              {{ getMemorizingAmountLabel(group.memorizing_amount) }}
            </span>
          </div>
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-600 dark:text-gray-400">{{ $t('groups.joinedAt') }}:</span>
            <span class="text-gray-900 dark:text-white">
              {{ formatDate(group.joined_at) }}
            </span>
          </div>
        </div>

        <!-- Schedule -->
        <div v-if="group.schedule?.length" class="pt-3 border-t border-gray-200 dark:border-gray-700">
          <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">{{ $t('groups.schedule') }}:</p>
          <div class="flex flex-wrap gap-1">
            <span 
              v-for="(item, idx) in group.schedule.slice(0, 2)" 
              :key="idx"
              class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs"
            >
              {{ getDayLabel(item.day) }} {{ formatTime(item.start_time) }}
            </span>
            <span v-if="group.schedule.length > 2" class="px-2 py-1 text-xs text-gray-400">
              +{{ group.schedule.length - 2 }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="card p-12 text-center">
      <i class="pi pi-users text-4xl text-gray-300 dark:text-gray-600 mb-4 block"></i>
      <p class="text-gray-500 dark:text-gray-400">{{ $t('student.noGroups') }}</p>
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
import { useRouter } from 'vue-router'
import type { Group } from '@/types/models'
import groupService from '@/services/groupService'
import StudentGroupDetailModal from '@/components/students/StudentGroupDetailModal.vue'
import { 
  getEnumLabel, 
  WeekDayLabels, 
  StudentStatusLabels,
  MemorizingAmountLabels,
  StudentStatus 
} from '@/constants'

const router = useRouter()
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

function viewGroup(group: any) {
  selectedGroup.value = group
}

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

onMounted(() => {
  fetchGroups()
})
</script>