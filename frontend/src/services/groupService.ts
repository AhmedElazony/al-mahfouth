import api from './api'
import type { Group, CreateGroupForm, UpdateGroupForm, GroupFilters } from '@/types/models'
import type { ApiResponse, PaginatedResponse } from '@/types/api'

// API response structure for group student
export interface GroupStudentResponse {
  id: number
  student: {
    id: number
    name: string
  }
  student_status: {
    for_view: string | null
    value: string | null
  }
  memorizing_amount: {
    for_view: string
    value: string
  }
  joined_at: string
}

export interface UpdateStudentProfilePayload {
  student_id: number
  educational_stage?: string
  begin_memorizing_at?: string
  memorizing_completed_at?: string
  tajweed_recitation_level?: string
  tajweed_learning_status?: string
  tajweed_notes?: string
}

export const groupService = {
  /**
   * Get paginated list of groups
   */
  async getGroups(filters: GroupFilters = {}): Promise<PaginatedResponse<Group>> {
    const params = new URLSearchParams()

    if (filters.search) params.append('q', filters.search)
    if (filters.teacher_id) params.append('teacher_id', filters.teacher_id.toString())
    if (filters.is_active !== undefined) params.append('is_active', filters.is_active ? '1' : '0')
    if (filters.per_page) params.append('per_page', filters.per_page.toString())
    if (filters.page) params.append('page', filters.page.toString())

    const response = await api.get(`/groups?${params.toString()}`)
    return response.data
  },

  /**
   * Get single group by ID
   */
  async getGroup(id: number): Promise<ApiResponse<Group>> {
    const response = await api.get(`/groups/${id}`)
    return response.data
  },

  /**
   * Create new group
   */
  async createGroup(data: CreateGroupForm): Promise<ApiResponse<Group>> {
    const response = await api.post('/groups', data)
    return response.data
  },

  /**
   * Update existing group
   */
  async updateGroup(id: number, data: UpdateGroupForm): Promise<ApiResponse<Group>> {
    const response = await api.put(`/groups/${id}`, data)
    return response.data
  },

  /**
   * Delete group
   */
  async deleteGroup(id: number): Promise<ApiResponse<null>> {
    const response = await api.delete(`/groups/${id}`)
    return response.data
  },

  /**
   * Get students in a group
   */
  async getGroupStudents(groupId: number): Promise<{ data: GroupStudentResponse[] }> {
    const response = await api.get(`/groups/${groupId}/students`)
    return response.data
  },

  /**
   * Get a specific student in a group
   */
  async getGroupStudent(groupId: number, studentId: number): Promise<ApiResponse<any>> {
    const response = await api.get(`/groups/${groupId}/students/${studentId}`)
    return response.data
  },

  /**
   * Assign student to group
   */
  async assignStudent(groupId: number, data: {
    student_id: number
    memorizing_amount: string
    student_status?: string
  }): Promise<{ data: GroupStudentResponse[] }> {
    const response = await api.post(`/groups/${groupId}/students`, data)
    return response.data
  },

  /**
   * Update student assignment in group
   */
  async updateStudent(groupId: number, studentId: number, data: {
    memorizing_amount?: string
    student_status?: string
  }): Promise<{ data: GroupStudentResponse }> {
    const response = await api.put(`/groups/${groupId}/students/${studentId}`, data)
    return response.data
  },

  /**
   * Update student profile within a group
   */
  async updateStudentProfile(groupId: number, data: UpdateStudentProfilePayload): Promise<ApiResponse<any>> {
    const response = await api.put(`/groups/${groupId}/students/profile`, data)
    return response.data
  },

  /**
   * Remove student from group
   */
  async removeStudent(groupId: number, studentId: number): Promise<ApiResponse<null>> {
    const response = await api.delete(`/groups/${groupId}/students/${studentId}`)
    return response.data
  },

  /**
   * Get student's groups
   */
  async getStudentGroups(): Promise<ApiResponse<any[]>> {
    const response = await api.get('/student/groups')
    return response.data
  }
}

export default groupService
