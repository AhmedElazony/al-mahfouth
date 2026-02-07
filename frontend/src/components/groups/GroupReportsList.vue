<template>
  <div class="space-y-4">
    <!-- Header with Date Filter & Add Button -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
      <div class="flex items-center gap-3 w-full sm:w-auto">
        <div class="relative flex-1 sm:flex-none">
          <i class="pi pi-calendar absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
          <input
            v-model="selectedDate"
            type="date"
            class="input pr-10 w-full"
          />
        </div>
        <button
          @click="clearDateFilter"
          class="btn-secondary whitespace-nowrap"
          :disabled="!selectedDate"
        >
          <i class="pi pi-times mr-2"></i>
          <span class="hidden sm:inline">{{ $t('common.clear') }}</span>
        </button>
      </div>
      <button @click="openAddReportModal" class="btn-primary w-full sm:w-auto">
        <i class="pi pi-plus mr-2"></i>
        {{ $t('reports.addReport') }}
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <i class="pi pi-spinner pi-spin text-3xl text-primary-600"></i>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="card p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
      <p class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
    </div>

    <!-- Reports List -->
    <div v-else-if="reports.length > 0" class="space-y-3">
      <div
        v-for="report in reports"
        :key="report.id"
        class="card p-4 hover:shadow-lg transition-shadow"
      >
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
          <div class="flex-1 min-w-0">
            <!-- Student Name & Date -->
            <div class="flex flex-wrap items-center gap-2 mb-2">
              <h4 class="font-medium text-gray-900 dark:text-white text-base">
                {{ getStudentName(report.student?.id) }}
              </h4>
              <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ formatDate(report.date) }}
              </span>
            </div>

            <!-- Badges -->
            <div class="flex flex-wrap items-center gap-2 mb-3">
              <span
                :class="getAttendanceBadgeClass(report.attendance_status.value)"
                class="px-2 py-1 text-xs rounded-full font-medium"
              >
                {{ report.attendance_status.for_view }}
              </span>
              <span
                v-if="report.memorized_amount"
                class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 text-xs rounded-full font-medium"
              >
                {{ report.memorized_amount.for_view }}
              </span>
              <span
                v-if="report.grade"
                :class="getGradeBadgeClass(report.grade.value)"
                class="px-2 py-1 text-xs rounded-full font-medium"
              >
                {{ report.grade.for_view }}
              </span>
            </div>

            <!-- Notes -->
            <p v-if="report.notes" class="text-sm text-gray-600 dark:text-gray-400">
              {{ report.notes }}
            </p>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-2 flex-shrink-0">
            <button
              @click="editReport(report)"
              class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
              :title="$t('common.edit')"
            >
              <i class="pi pi-pencil text-sm"></i>
            </button>
            <button
              @click="deleteReport(report)"
              class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
              :title="$t('common.delete')"
            >
              <i class="pi pi-trash text-sm"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="pagination && pagination.last_page > 1" class="flex justify-center gap-2 pt-4">
        <button
          @click="changePage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="btn-secondary"
        >
          <i class="pi pi-chevron-right"></i>
        </button>
        <span class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
          {{ pagination.current_page }} / {{ pagination.last_page }}
        </span>
        <button
          @click="changePage(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="btn-secondary"
        >
          <i class="pi pi-chevron-left"></i>
        </button>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12 text-gray-500 dark:text-gray-400">
      <i class="pi pi-file-edit text-4xl mb-4 block"></i>
      <p class="mb-2">{{ $t('reports.noReports') }}</p>
      <button @click="openAddReportModal" class="btn-primary mt-4">
        <i class="pi pi-plus mr-2"></i>
        {{ $t('reports.addFirstReport') }}
      </button>
    </div>

    <!-- Add/Edit Report Modal -->
    <ReportFormModal
      v-if="showReportModal"
      :group="group"
      :students="students"
      :report="selectedReport"
      :default-date="selectedDate"
      @close="closeReportModal"
      @saved="onReportSaved"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, watch, computed, onMounted } from 'vue'
import type { Group } from '@/types/models'
import type { GroupStudentResponse } from '@/services/groupService'
import reportService, { type Report } from '@/services/reportService'
import type { PaginationMeta } from '@/types/api'
import ReportFormModal from './ReportFormModal.vue'
import { AttendanceStatus, Grade } from '@/constants/enums'

interface Props {
  group: Group
  students: GroupStudentResponse[]
}

const props = defineProps<Props>()

// State
const loading = ref(false)
const error = ref<string | null>(null)
const reports = ref<Report[]>([])
const pagination = ref<PaginationMeta | null>(null)
const selectedDate = ref<string>(new Date().toISOString().split('T')[0])
const currentPage = ref(1)
const showReportModal = ref(false)
const selectedReport = ref<Report | null>(null)

// Computed
const dateFilter = computed(() => {
  if (!selectedDate.value) return {}
  return {
    date_from: selectedDate.value,
    date_to: selectedDate.value
  }
})

// Methods
function getStudentName(studentId: number): string {
  const student = props.students.find(s => {
    const id = s.student?.id || s.student_id
    return id === studentId
  })
  return student?.student?.name || student?.user?.name || '-'
}

function formatDate(dateStr: string): string {
  try {
    const [day, month, year] = dateStr.split('-')
    const date = new Date(`${year}-${month}-${day}`)
    return date.toLocaleDateString('ar-EG', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch {
    return dateStr
  }
}

function getAttendanceBadgeClass(status: string): string {
  return {
    [AttendanceStatus.ATTENDED]: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    [AttendanceStatus.ABSENT]: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    [AttendanceStatus.EXCUSED]: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
  }[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
}

function getGradeBadgeClass(grade: string): string {
  return {
    [Grade.EXCELLENT]: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    [Grade.VERY_GOOD]: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    [Grade.GOOD]: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    [Grade.BAD]: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
  }[grade] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
}

async function fetchReports() {
  loading.value = true
  error.value = null

  try {
    const response = await reportService.getReports(props.group.id, {
      ...dateFilter.value,
      per_page: 15,
      page: currentPage.value
    } as any)

    reports.value = response.data || []
    pagination.value = response.pagination
  } catch (err: any) {
    console.error('Failed to fetch reports:', err)
    error.value = err.response?.data?.message || 'حدث خطأ في جلب التقارير'
  } finally {
    loading.value = false
  }
}

function changePage(page: number) {
  currentPage.value = page
  fetchReports()
}

function clearDateFilter() {
  selectedDate.value = ''
}

function openAddReportModal() {
  selectedReport.value = null
  showReportModal.value = true
}

function editReport(report: Report) {
  selectedReport.value = report
  showReportModal.value = true
}

async function deleteReport(report: Report) {
  if (!confirm('هل أنت متأكد من حذف هذا التقرير؟')) return

  try {
    await reportService.deleteReport(props.group.id, report.id)
    await fetchReports()
  } catch (err: any) {
    console.error('Failed to delete report:', err)
    error.value = err.response?.data?.message || 'حدث خطأ في حذف التقرير'
  }
}

function closeReportModal() {
  showReportModal.value = false
  selectedReport.value = null
}

function onReportSaved() {
  closeReportModal()
  fetchReports()
}

// Watchers
watch(selectedDate, () => {
  currentPage.value = 1
  fetchReports()
})

onMounted(() => {
  fetchReports()
})
</script>