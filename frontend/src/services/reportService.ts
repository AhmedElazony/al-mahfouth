import api from './api'
import type { ApiResponse, PaginatedResponse } from '@/types/api'

export interface Report {
	id: number
	date: string
	attendance_status: {
		for_view: string
		value: string
	}
	memorized_amount?: {
		for_view: string
		value: string
	} | null
	grade?: {
		for_view: string
		value: string
	} | null
	//   memorization_from: string | null
	//   memorization_to: string | null
	//   memorization_grade: string | null
	//   revision_from: string | null
	//   revision_to: string | null
	//   revision_grade: string | null
	notes?: string | null
	created_at: string
	student?: {
		id: number
		name: string
	}
	group?: {
		id: number
		name: string
	}
	created_by?: {
		id: number
		name: string
	}
}

export interface CreateReportPayload {
	student_id: number
	group_id: number
	date: string
	attendance_status: string
	memorized_amount?: string
	grade?: string
	//   memorization_from?: string
	//   memorization_to?: string
	//   memorization_grade?: string
	//   revision_from?: string
	//   revision_to?: string
	//   revision_grade?: string
	notes?: string
}

export interface UpdateReportPayload {
	student_id?: number
	date?: string
	attendance_status?: string
	memorized_amount?: string
	grade?: string
	//   memorization_from?: string
	//   memorization_to?: string
	//   memorization_grade?: string
	//   revision_from?: string
	//   revision_to?: string
	//   revision_grade?: string
	notes?: string
}

export interface ReportFilters {
	per_page?: number
	page?: number
	student_id?: number
	group_id?: number
	date_from?: string
	date_to?: string
}

class ReportService {
	// ========== Group-scoped endpoints ==========

	/**
	 * Get paginated reports for a group
	 */
	async getReports(groupId: number, filters?: ReportFilters): Promise<PaginatedResponse<Report>> {
		const { data } = await api.get(`/groups/${groupId}/reports`, { params: filters })
		return data
	}

	/**
	 * Get single report (group-scoped)
	 */
	async getReport(groupId: number, reportId: number): Promise<ApiResponse<Report>> {
		const { data } = await api.get(`/groups/${groupId}/reports/${reportId}`)
		return data
	}

	/**
	 * Create new report (group-scoped)
	 */
	async createReport(groupId: number, payload: CreateReportPayload): Promise<ApiResponse<Report>> {
		const { data } = await api.post(`/groups/${groupId}/reports`, payload)
		return data
	}

	/**
	 * Update existing report (group-scoped)
	 */
	async updateReport(
		groupId: number,
		reportId: number,
		payload: UpdateReportPayload
	): Promise<ApiResponse<Report>> {
		const { data } = await api.put(`/groups/${groupId}/reports/${reportId}`, payload)
		return data
	}

	/**
	 * Delete report (group-scoped)
	 */
	async deleteReport(groupId: number, reportId: number): Promise<ApiResponse<null>> {
		const { data } = await api.delete(`/groups/${groupId}/reports/${reportId}`)
		return data
	}

	// ========== Standalone endpoints (admin) ==========

	/**
	 * Get all reports (standalone, admin only)
	 */
	async getAllReports(filters?: ReportFilters): Promise<PaginatedResponse<Report>> {
		const { data } = await api.get('/reports', { params: filters })
		return data
	}

	/**
	 * Get single report (standalone, admin only)
	 */
	async getReportById(reportId: number): Promise<ApiResponse<Report>> {
		const { data } = await api.get(`/reports/${reportId}`)
		return data
	}

	/**
	 * Create report (standalone, admin only)
	 */
	async createStandaloneReport(payload: CreateReportPayload): Promise<ApiResponse<Report>> {
		const { data } = await api.post('/reports', payload)
		return data
	}

	/**
	 * Update report (standalone, admin only)
	 */
	async updateStandaloneReport(reportId: number, payload: UpdateReportPayload): Promise<ApiResponse<Report>> {
		const { data } = await api.put(`/reports/${reportId}`, payload)
		return data
	}

	/**
	 * Delete report (standalone, admin only)
	 */
	async deleteStandaloneReport(reportId: number): Promise<ApiResponse<null>> {
		const { data } = await api.delete(`/reports/${reportId}`)
		return data
	}
}

export default new ReportService()