<template>
  <div class="space-y-4">
    <!-- Header with Filters & Add Button -->
    <div class="flex flex-col gap-3">
      <!-- Filters Container -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- Date From -->
        <div class="relative">
          <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
            {{ $t('reports.dateFrom') }}
          </label>
          <div class="relative">
            <i class="pi pi-calendar absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
            <input
              v-model="dateFrom"
              type="date"
              class="input pr-10 w-full"
              :placeholder="$t('reports.dateFrom')"
            />
          </div>
        </div>

        <!-- Date To -->
        <div class="relative">
          <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
            {{ $t('reports.dateTo') }}
          </label>
          <div class="relative">
            <i class="pi pi-calendar absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
            <input
              v-model="dateTo"
              type="date"
              class="input pr-10 w-full"
              :placeholder="$t('reports.dateTo')"
            />
          </div>
        </div>

        <!-- Student Search -->
        <div class="relative">
          <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
            {{ $t('users.student') }}
          </label>
          <div class="relative">
            <i class="pi pi-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
            <input
              v-model="studentSearch"
              type="text"
              class="input pr-10 w-full"
              :placeholder="$t('reports.searchByStudent')"
            />
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col justify-end gap-2 md:flex-row">
          <button
            @click="clearAllFilters"
            class="btn-secondary whitespace-nowrap"
            :disabled="!hasActiveFilters"
          >
            <i class="pi pi-times mr-2"></i>
            <span>{{ $t('common.clear') }}</span>
          </button>

          <button 
            @click="openAddReportModal" 
            class="btn-primary whitespace-nowrap"
          >
            <i class="pi pi-plus mr-2"></i>
            <span>{{ $t('reports.addReport') }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-8 sm:py-12">
      <i class="pi pi-spinner pi-spin text-2xl sm:text-3xl text-primary-600"></i>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="card p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
      <p class="text-xs sm:text-sm text-red-600 dark:text-red-400">{{ error }}</p>
    </div>

    <!-- Reports List -->
    <div v-else-if="filteredReports.length > 0" class="space-y-3">
      <div
        v-for="report in paginatedReports"
        :key="report.id"
        class="card p-3 sm:p-4 hover:shadow-lg transition-shadow"
      >
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
          <div class="flex-1 min-w-0">
            <!-- Student Name & Date -->
            <div class="flex flex-wrap items-center gap-2 mb-2">
              <h4 class="font-medium text-gray-900 dark:text-white text-sm sm:text-base truncate">
                {{ report.student?.name }}
              </h4>
              <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                {{ formatDate(report.date) }}
              </span>
            </div>

            <!-- Badges -->
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-2 sm:mb-3">
              <span
                :class="getAttendanceBadgeClass(report.attendance_status.value)"
                class="px-2 py-0.5 sm:py-1 text-xs rounded-full font-medium"
              >
                {{ report.attendance_status.for_view }}
              </span>
              <span
                v-if="report.memorized_amount"
                class="px-2 py-0.5 sm:py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 text-xs rounded-full font-medium"
              >
                {{ report.memorized_amount.for_view }}
              </span>
              <span
                v-if="report.grade"
                :class="getGradeBadgeClass(report.grade.value)"
                class="px-2 py-0.5 sm:py-1 text-xs rounded-full font-medium"
              >
                {{ report.grade.for_view }}
              </span>
            </div>

            <!-- Notes -->
            <p v-if="report.notes" class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
              {{ report.notes }}
            </p>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0 self-start">
            <button
              @click="editReport(report)"
              class="p-1.5 sm:p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
              :title="$t('common.edit')"
            >
              <i class="pi pi-pencil text-xs sm:text-sm"></i>
            </button>
            <button
              @click="deleteReport(report)"
              class="p-1.5 sm:p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
              :title="$t('common.delete')"
            >
              <i class="pi pi-trash text-xs sm:text-sm"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div 
        v-if="totalPages > 1" 
        class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-gray-200 dark:border-gray-700"
      >
        <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 order-2 sm:order-1">
          {{ $t('common.showing') }} {{ startIndex + 1 }}-{{ endIndex }} {{ $t('common.of') }} {{ filteredReports.length }}
        </div>
        <div class="flex items-center gap-2 order-1 sm:order-2">
          <button
            @click="changePage(1)"
            :disabled="currentPage === 1"
            class="hidden sm:block btn-secondary p-2 text-xs"
            :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
          >
            <i class="pi pi-angle-double-right"></i>
          </button>
          <button
            @click="previousPage"
            :disabled="currentPage === 1"
            class="btn-secondary p-2"
            :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
          >
            <i class="pi pi-chevron-right"></i>
          </button>
          <span class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-600 dark:text-gray-400 font-medium">
            {{ currentPage }} / {{ totalPages }}
          </span>
          <button
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="btn-secondary p-2"
            :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
          >
            <i class="pi pi-chevron-left"></i>
          </button>
          <button
            @click="changePage(totalPages)"
            :disabled="currentPage === totalPages"
            class="hidden sm:block btn-secondary p-2 text-xs"
            :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
          >
            <i class="pi pi-angle-double-left"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-8 sm:py-12 text-gray-500 dark:text-gray-400">
      <i class="pi pi-file-edit text-3xl sm:text-4xl mb-3 sm:mb-4 block"></i>
      <p class="text-sm sm:text-base mb-2">
        {{ hasActiveFilters ? $t('reports.noReportsFound') : $t('reports.noReports') }}
      </p>
      <button @click="openAddReportModal" class="btn-primary mt-2 sm:mt-4 text-sm sm:text-base">
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
      :default-date="dateFrom || dateTo"
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
const dateFrom = ref<string>('')
const dateTo = ref<string>('')
const studentSearch = ref<string>('') // ✅ Changed from selectedStudentId to studentSearch
const currentPage = ref(1)
const itemsPerPage = ref(15)
const showReportModal = ref(false)
const selectedReport = ref<Report | null>(null)

// Computed
const dateFilter = computed(() => {
  const filters: any = {}
  
  if (dateFrom.value) {
    filters.date_from = dateFrom.value
  }
  
  if (dateTo.value) {
    filters.date_to = dateTo.value
  }
  
  return filters
})

const hasActiveFilters = computed(() => {
  return !!(dateFrom.value || dateTo.value || studentSearch.value)
})

// ✅ Client-side filtering by student name
const filteredReports = computed(() => {
  let result = reports.value

  if (studentSearch.value) {
    const query = studentSearch.value.toLowerCase()
    result = result.filter(report => {
      const studentName = report.student?.name?.toLowerCase() || ''
      return studentName.includes(query)
    })
  }

  return result
})

const totalPages = computed(() => Math.ceil(filteredReports.value.length / itemsPerPage.value))

const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage.value, filteredReports.value.length))

const paginatedReports = computed(() => {
  return filteredReports.value.slice(startIndex.value, endIndex.value)
})

function formatDate(dateStr: string): string {
  try {
    const [day, month, year] = dateStr.split('-')
    const date = new Date(`${year}-${month}-${day}`)
    return date.toLocaleDateString('ar-EG', {
      year: 'numeric',
      month: 'short',
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
    // ✅ Only fetch with date filters, student search is client-side
    const response = await reportService.getReports(props.group.id, {
      ...dateFilter.value,
      per_page: 1000 // Fetch all reports for client-side filtering
    } as any)

    reports.value = response.data || []
  } catch (err: any) {
    console.error('Failed to fetch reports:', err)
    error.value = err.response?.data?.message || 'حدث خطأ في جلب التقارير'
  } finally {
    loading.value = false
  }
}

function changePage(page: number) {
  currentPage.value = page
}

function nextPage() {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

function previousPage() {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

function clearAllFilters() {
  dateFrom.value = ''
  dateTo.value = ''
  studentSearch.value = '' // ✅ Clear student search
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
watch([dateFrom, dateTo], () => {
  currentPage.value = 1
  fetchReports()
})

// ✅ Reset pagination when student search changes
watch(studentSearch, () => {
  currentPage.value = 1
})

onMounted(() => {
  fetchReports()
})
</script>