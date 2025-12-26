import api from './api'
import type { User, CreateUserForm, UpdateUserForm, UserFilters } from '@/types/models'
import type { ApiResponse, PaginatedResponse } from '@/types/api'

export const userService = {
  /**
   * Get paginated list of users
   */
  async getUsers(filters: UserFilters = {}): Promise<PaginatedResponse<User>> {
    const params = new URLSearchParams()
    
    if (filters.search) params.append('search', filters.search)
    if (filters.role) params.append('role', filters.role)
    if (filters.gender) params.append('gender', filters.gender)
    if (filters.per_page) params.append('per_page', filters.per_page.toString())
    if (filters.page) params.append('page', filters.page.toString())

    const response = await api.get(`/users?${params.toString()}`)
    return response.data
  },

  /**
   * Get single user by ID
   */
  async getUser(id: number): Promise<ApiResponse<User>> {
    const response = await api.get(`/users/${id}`)
    return response.data
  },

  /**
   * Create new user
   */
  async createUser(data: CreateUserForm): Promise<ApiResponse<User>> {
    const response = await api.post('/users', data)
    return response.data
  },

  /**
   * Update existing user
   */
  async updateUser(id: number, data: UpdateUserForm): Promise<ApiResponse<User>> {
    const response = await api.put(`/users/${id}`, data)
    return response.data
  },

  /**
   * Delete user
   */
  async deleteUser(id: number): Promise<ApiResponse<null>> {
    const response = await api.delete(`/users/${id}`)
    return response.data
  }
}

export default userService