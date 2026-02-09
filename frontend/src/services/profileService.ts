import api from './api'
import type { ApiResponse } from '@/types/api'
import type { User } from '@/types/models'

export interface UpdateProfileForm {
  name?: string
  username?: string
  phone?: string
  password?: string
  password_confirmation?: string
}

class ProfileService {
  /**
   * Update authenticated user's profile
   */
  async updateProfile(data: UpdateProfileForm): Promise<ApiResponse<User>> {
    const response = await api.put('/user/profile', data)
    return response.data
  }
}

export default new ProfileService()