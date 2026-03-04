import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import reportService, { type Report, type ReportFilters } from '@/services/reportService'

export const useReportsStore = defineStore('reports', () => {
	const reports = ref<Report[]>([])
	const loading = ref(false)
	const error = ref<string | null>(null)
	const currentPage = ref(1)
	const perPage = ref(15)
	const totalReports = ref(0)
	const totalPages = ref(0)
	const filters = ref<ReportFilters>({})

	const pagination = computed(() => ({
		currentPage: currentPage.value,
		perPage: perPage.value,
		total: totalReports.value,
		totalPages: totalPages.value
	}))

	async function fetchReports() {
		loading.value = true
		error.value = null

		try {
			const response = await reportService.getAllReports({
				...filters.value,
				page: currentPage.value,
				per_page: perPage.value
			})

			reports.value = response.data || []
			totalReports.value = response.pagination?.total || 0
			totalPages.value = response.pagination?.last_page || 0
			currentPage.value = response.pagination?.current_page || 1
		} catch (err: any) {
			console.error('Failed to fetch reports:', err)
			error.value = err.response?.data?.message || 'حدث خطأ في جلب التقارير'
		} finally {
			loading.value = false
		}
	}

	function setPage(page: number) {
		currentPage.value = page
		fetchReports()
	}

	function setFilters(newFilters: ReportFilters) {
		filters.value = { ...newFilters }
		currentPage.value = 1
		fetchReports()
	}

	function clearFilters() {
		filters.value = {}
		currentPage.value = 1
		fetchReports()
	}

	function removeReport(reportId: number) {
		reports.value = reports.value.filter(r => r.id !== reportId)
		totalReports.value--
	}

	function updateReportInList(report: Report) {
		const index = reports.value.findIndex(r => r.id === report.id)
		if (index !== -1) {
			reports.value[index] = report
		}
	}

	function addReport(report: Report) {
		reports.value.unshift(report)
		totalReports.value++
	}

	return {
		reports,
		loading,
		error,
		currentPage,
		perPage,
		totalReports,
		totalPages,
		pagination,
		filters,
		fetchReports,
		setPage,
		setFilters,
		clearFilters,
		removeReport,
		updateReportInList,
		addReport
	}
})