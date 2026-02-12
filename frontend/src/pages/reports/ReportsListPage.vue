<template>
	<div class="space-y-4 md:space-y-6">
		<!-- Header -->
		<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
			<div>
				<h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">
					{{ $t('reports.title') }}
				</h1>
				<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
					{{ $t('reports.subtitle') }}
				</p>
			</div>
			<button @click="openAddModal" class="btn-primary whitespace-nowrap">
				<i class="pi pi-plus mr-2"></i>
				{{ $t('reports.addReport') }}
			</button>
		</div>

		<!-- Filters -->
		<div class="card p-4">
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
				<!-- Group Filter -->
				<div>
					<label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
						{{ $t('reports.group') }}
					</label>
					<select v-model="filterGroupId" @change="applyFilters"
						class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:text-white text-sm">
						<option value="">{{ $t('reports.allGroups') }}</option>
						<option v-for="group in groups" :key="group.id" :value="group.id">
							{{ group.name }}
						</option>
					</select>
				</div>

				<!-- Student Search -->
				<div>
					<label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
						{{ $t('reports.student') }}
					</label>
					<input v-model="studentSearch" type="text" :placeholder="$t('reports.searchStudent')"
						class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:text-white text-sm" />
				</div>

				<!-- Date From -->
				<div>
					<label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
						{{ $t('reports.dateFrom') }}
					</label>
					<input v-model="filterDateFrom" type="date" @change="applyFilters"
						class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:text-white text-sm" />
				</div>

				<!-- Date To -->
				<div>
					<label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
						{{ $t('reports.dateTo') }}
					</label>
					<input v-model="filterDateTo" type="date" @change="applyFilters"
						class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:text-white text-sm" />
				</div>
			</div>

			<!-- Filter Actions -->
			<div class="flex justify-end mt-4">
				<button @click="clearAllFilters" class="btn-secondary text-sm" :disabled="!hasActiveFilters">
					<i class="pi pi-times mr-2"></i>
					{{ $t('common.clear') }}
				</button>
			</div>
		</div>

		<!-- Loading -->
		<div v-if="store.loading" class="flex justify-center py-12">
			<i class="pi pi-spinner pi-spin text-3xl text-primary-600"></i>
		</div>

		<!-- Error -->
		<div v-else-if="store.error"
			class="card p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
			<p class="text-sm text-red-600 dark:text-red-400">{{ store.error }}</p>
			<button @click="store.fetchReports()" class="btn-secondary mt-3 text-sm">
				<i class="pi pi-refresh mr-2"></i>
				{{ $t('common.retry') }}
			</button>
		</div>

		<!-- Reports Table -->
		<div v-else-if="filteredReports.length > 0" class="card overflow-hidden">
			<!-- Desktop Table -->
			<div class="hidden md:block overflow-x-auto">
				<table class="w-full text-sm">
					<thead class="bg-gray-50 dark:bg-gray-700/50">
						<tr>
							<th class="px-6 py-3 text-right font-medium text-gray-600 dark:text-gray-400">
								{{ $t('reports.student') }}
							</th>
							<th class="px-6 py-3 text-right font-medium text-gray-600 dark:text-gray-400">
								{{ $t('reports.group') }}
							</th>
							<th class="px-6 py-3 text-right font-medium text-gray-600 dark:text-gray-400">
								{{ $t('reports.date') }}
							</th>
							<th class="px-6 py-3 text-right font-medium text-gray-600 dark:text-gray-400">
								{{ $t('reports.attendance') }}
							</th>
							<th class="px-6 py-3 text-right font-medium text-gray-600 dark:text-gray-400">
								{{ $t('reports.memorizedAmount') }}
							</th>
							<!-- <th class="px-6 py-3 text-right font-medium text-gray-600 dark:text-gray-400">
								{{ $t('reports.revision') }}
							</th> -->
							<th class="px-6 py-3 text-center font-medium text-gray-600 dark:text-gray-400">
								{{ $t('common.actions') }}
							</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
						<tr v-for="report in filteredReports" :key="report.id"
							class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
							<td class="px-6 py-3 text-gray-900 dark:text-white font-medium">
								{{ report.student?.name || '-' }}
							</td>
							<td class="px-6 py-3 text-gray-600 dark:text-gray-400">
								{{ report.group?.name || '-' }}
							</td>
							<td class="px-6 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">
								{{ formatDate(report.date) }}
							</td>
							<td class="px-6 py-3">
								<span :class="getAttendanceBadgeClass(report.attendance_status.value)"
									class="px-2 py-1 rounded-full text-xs font-medium">
									{{ report.attendance_status.for_view }}
								</span>
							</td>
							<td class="px-6 py-3 text-gray-600 dark:text-gray-400 text-xs">
								<template v-if="report.memorized_amount">
									{{ report.memorized_amount.for_view }}
									<span v-if="report.grade" :class="getGradeBadgeClass(report.grade.value)"
										class="inline-block px-1.5 py-0.5 rounded text-xs mr-1">
										{{ report.grade.for_view }}
									</span>
								</template>
								<span v-else class="text-gray-400">-</span>
							</td>
							<td class="px-6 py-3">
								<div class="flex items-center justify-center gap-2">
									<button @click="editReport(report)"
										class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
										:title="$t('common.edit')">
										<i class="pi pi-pencil"></i>
									</button>
									<button @click="confirmDelete(report)"
										class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
										:title="$t('common.delete')">
										<i class="pi pi-trash"></i>
									</button>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- Mobile Cards -->
			<div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
				<div v-for="report in filteredReports" :key="report.id" class="p-4 space-y-2">
					<div class="flex items-start justify-between">
						<div>
							<h4 class="font-medium text-gray-900 dark:text-white text-sm">
								{{ report.student?.name }}
							</h4>
							<p class="text-xs text-gray-500 dark:text-gray-400">
								{{ report.group?.name }} · {{ formatDate(report.date) }}
							</p>
						</div>
						<span :class="getAttendanceBadgeClass(report.attendance_status.value)"
							class="px-2 py-1 rounded-full text-xs font-medium">
							{{ report.attendance_status.for_view }}
						</span>
					</div>

					<div v-if="report.memorized_amount" class="text-xs text-gray-600 dark:text-gray-400">
						<span class="font-medium">{{ $t('reports.memorizedAmount') }}:</span>
						{{ report.memorized_amount.for_view }}
						<span v-if="report.grade" :class="getGradeBadgeClass(report.grade.value)"
							class="inline-block px-1.5 py-0.5 rounded text-xs mr-1">
							{{ report.grade.for_view }}
						</span>
					</div>

					<div v-if="report.notes" class="text-xs text-gray-500 dark:text-gray-400 italic">
						{{ report.notes }}
					</div>

					<div class="flex items-center gap-3 pt-1">
						<button @click="editReport(report)"
							class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-xs">
							<i class="pi pi-pencil mr-1"></i>
							{{ $t('common.edit') }}
						</button>
						<button @click="confirmDelete(report)"
							class="text-red-600 hover:text-red-800 dark:text-red-400 text-xs">
							<i class="pi pi-trash mr-1"></i>
							{{ $t('common.delete') }}
						</button>
					</div>
				</div>
			</div>

			<!-- Pagination -->
			<div 
				class="flex flex-col sm:flex-row items-center justify-between gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
				<div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 order-2 sm:order-1">
					{{ $t('common.showing') }} {{ store.pagination.currentPage }} {{ $t('common.of') }} {{
						store.pagination.totalPages }}
					({{ store.pagination.total }} {{ $t('reports.totalReports') }})
				</div>
				<div class="flex items-center gap-2 order-1 sm:order-2">
					<button @click="store.setPage(1)" :disabled="store.currentPage === 1"
						class="btn-secondary p-2 text-xs"
						:class="{ 'opacity-50 cursor-not-allowed': store.currentPage === 1 }">
						<i class="pi pi-angle-double-right"></i>
					</button>
					<button @click="store.setPage(store.currentPage - 1)" :disabled="store.currentPage === 1"
						class="btn-secondary p-2" :class="{ 'opacity-50 cursor-not-allowed': store.currentPage === 1 }">
						<i class="pi pi-chevron-right"></i>
					</button>
					<span class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 font-medium">
						{{ store.currentPage }} / {{ store.totalPages }}
					</span>
					<button @click="store.setPage(store.currentPage + 1)"
						:disabled="store.currentPage === store.totalPages" class="btn-secondary p-2"
						:class="{ 'opacity-50 cursor-not-allowed': store.currentPage === store.totalPages }">
						<i class="pi pi-chevron-left"></i>
					</button>
					<button @click="store.setPage(store.totalPages)" :disabled="store.currentPage === store.totalPages"
						class="btn-secondary p-2 text-xs"
						:class="{ 'opacity-50 cursor-not-allowed': store.currentPage === store.totalPages }">
						<i class="pi pi-angle-double-left"></i>
					</button>
				</div>
			</div>
		</div>

		<!-- Empty State -->
		<div v-else class="card p-8 text-center">
			<i class="pi pi-file-edit text-4xl text-gray-400 mb-4 block"></i>
			<p class="text-gray-500 dark:text-gray-400 mb-2">
				{{ hasActiveFilters ? $t('reports.noReportsFound') : $t('reports.noReports') }}
			</p>
			<button @click="openAddModal" class="btn-primary mt-4">
				<i class="pi pi-plus mr-2"></i>
				{{ $t('reports.addFirstReport') }}
			</button>
		</div>

		<!-- Add/Edit Report Modal -->
		<ReportFormModal v-if="showReportModal" :group="selectedReport?.group" :students="students"
			:report="selectedReport" :default-date="filterDateFrom || filterDateTo" @close="closeReportModal"
			@saved="onReportSaved" />

		<!-- Delete Confirmation Modal -->
		<ConfirmModal v-if="showDeleteModal" :title="$t('reports.deleteReport')"
			:message="$t('reports.deleteConfirmation', { name: selectedReport?.student?.name || '' })"
			:confirm-text="deleting ? $t('common.deleting') : $t('common.delete')" :cancel-text="$t('common.cancel')"
			:loading="deleting" variant="danger" @confirm="handleDelete" @cancel="showDeleteModal = false" />
	</div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useReportsStore } from '@/stores/reports'
import type { Report } from '@/services/reportService'
import reportService from '@/services/reportService'
import groupService from '@/services/groupService'
import type { User, Group } from '@/types/models'
import ConfirmModal from '@/components/common/ConfirmModal.vue'
import ReportFormModal from '@/components/reports/ReportFormModal.vue'
import userService from '@/services/userService'

const store = useReportsStore()

// Filters
const groups = ref<Group[]>([])
const filterGroupId = ref<number | string>('')
const filterDateFrom = ref('')
const filterDateTo = ref('')
const studentSearch = ref('')
const students = ref<User[]>([])

// Modal state
const showReportModal = ref(false)
const showDeleteModal = ref(false)
const selectedReport = ref<Report | null>(null)
const deleting = ref(false)

const hasActiveFilters = computed(() => {
	return !!(filterGroupId.value || filterDateFrom.value || filterDateTo.value || studentSearch.value)
})

// Client-side student name filtering
const filteredReports = computed(() => {
	if (!studentSearch.value) return store.reports

	const query = studentSearch.value.toLowerCase()
	return store.reports.filter(report => {
		const studentName = report.student?.name?.toLowerCase() || ''
		return studentName.includes(query)
	})
})

function applyFilters() {
	const filters: any = {}
	if (filterGroupId.value) filters.group_id = filterGroupId.value
	if (filterDateFrom.value) filters.date_from = filterDateFrom.value
	if (filterDateTo.value) filters.date_to = filterDateTo.value
	store.setFilters(filters)
}

function clearAllFilters() {
	filterGroupId.value = ''
	filterDateFrom.value = ''
	filterDateTo.value = ''
	studentSearch.value = ''
	store.clearFilters()
}

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
		attended: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
		absent: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
		excused: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400'
	}[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
}

function getGradeBadgeClass(grade: string): string {
	return {
		excellent: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
		very_good: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
		good: 'bg-teal-100 text-teal-800 dark:bg-teal-900/20 dark:text-teal-400',
		bad: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
	}[grade] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
}

// Modal actions
function openAddModal() {
	selectedReport.value = null
	showReportModal.value = true
	fetchStudents()
}

function editReport(report: Report) {
	selectedReport.value = report
	showReportModal.value = true
}

function closeReportModal() {
	showReportModal.value = false
	selectedReport.value = null
}

function onReportSaved() {
	closeReportModal()
	store.fetchReports()
}

function confirmDelete(report: Report) {
	selectedReport.value = report
	showDeleteModal.value = true
}

async function handleDelete() {
	if (!selectedReport.value) return

	deleting.value = true

	try {
		await reportService.deleteStandaloneReport(selectedReport.value.id)
		store.removeReport(selectedReport.value.id)
		showDeleteModal.value = false
		selectedReport.value = null
	} catch (err: any) {
		console.error('Failed to delete report:', err)
	} finally {
		deleting.value = false
	}
}

async function fetchGroups() {
	try {
		const response = await groupService.getGroups({ per_page: 100 })
		groups.value = response.data || []
	} catch (err) {
		console.error('Failed to fetch groups:', err)
	}
}

async function fetchStudents() {
	try {
		const response = await userService.getUsers({ role: 'student', per_page: 100 })
		students.value = response.data || []
	} catch (err) {
		console.error('Failed to fetch students:', err)
	}
}

// Watch student search for debounce effect
let searchTimeout: ReturnType<typeof setTimeout>
watch(studentSearch, () => {
	clearTimeout(searchTimeout)
	searchTimeout = setTimeout(() => {
		// Client-side filtering, no need to refetch
	}, 300)
})

onMounted(() => {
	store.fetchReports()
	fetchGroups()
})
</script>