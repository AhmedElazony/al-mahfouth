import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User } from '@/types/models'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('token'))

  const isAuthenticated = computed(() => !!token.value)
  const userRole = computed(() => user.value?.role)

  async function login(credentials: { email: string; password: string }) {
    const response = await api.post('/auth/login', credentials)
    token.value = response.data.data.token
    user.value = response.data.data.user
    localStorage.setItem('token', token.value!)
    return response.data
  }

  async function logout() {
    try {
      await api.post('/auth/logout')
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
    }
  }

  async function fetchUser() {
    if (!token.value) return
    try {
      const response = await api.get('/auth/me')
      user.value = response.data.data
    } catch {
      logout()
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    userRole,
    login,
    logout,
    fetchUser
  }
})