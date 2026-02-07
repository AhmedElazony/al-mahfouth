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
  date: string // Format: dd-mm-yyyy
  attendance_status: string
  memorized_amount?: string
  grade?: string
  notes?: string
}

export interface UpdateReportPayload {
  student_id?: number
  date?: string
  attendance_status?: string
  memorized_amount?: string
  grade?: string
  notes?: string
}

export interface ReportFilters {
  per_page?: number
  student_id?: number
  date_from?: string
  date_to?: string
}

class ReportService {
  /**
   * Get paginated reports for a group
   */
  async getReports(groupId: number, filters?: ReportFilters): Promise<PaginatedResponse<Report>> {
    const { data } = await api.get(`/groups/${groupId}/reports`, { params: filters })
    return data
  }

  /**
   * Get single report
   */
  async getReport(groupId: number, reportId: number): Promise<ApiResponse<Report>> {
    const { data } = await api.get(`/groups/${groupId}/reports/${reportId}`)
    return data
  }

  /**
   * Create new report
   */
  async createReport(groupId: number, payload: CreateReportPayload): Promise<ApiResponse<Report>> {
    const { data } = await api.post(`/groups/${groupId}/reports`, payload)
    return data
  }

  /**
   * Update existing report
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
   * Delete report
   */
  async deleteReport(groupId: number, reportId: number): Promise<ApiResponse<null>> {
    const { data } = await api.delete(`/groups/${groupId}/reports/${reportId}`)
    return data
  }
}

export default new ReportService()