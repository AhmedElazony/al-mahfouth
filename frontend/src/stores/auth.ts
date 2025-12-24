import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'
import type { User, UserRole } from '@/types/models'
import api from '@/services/api'
import router from '@/router'

// Helper functions for localStorage
function getStoredToken(): string | null {
  return localStorage.getItem('token')
}

function getStoredUser(): User | null {
  const userData = localStorage.getItem('user')
  if (userData) {
    try {
      return JSON.parse(userData)
    } catch {
      return null
    }
  }
  return null
}

function setStoredAuth(token: string, user: User): void {
  localStorage.setItem('token', token)
  localStorage.setItem('user', JSON.stringify(user))
}

function clearStoredAuth(): void {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
}

export const useAuthStore = defineStore('auth', () => {
  // Initialize from localStorage
  const user = ref<User | null>(getStoredUser())
  const token = ref<string | null>(getStoredToken())
  const loading = ref(false)
  const error = ref<string | null>(null)

  // Watch for changes and persist to localStorage
  watch(
    () => user.value,
    (newUser) => {
      if (newUser) {
        localStorage.setItem('user', JSON.stringify(newUser))
      } else {
        localStorage.removeItem('user')
      }
    },
    { deep: true }
  )

  watch(
    () => token.value,
    (newToken) => {
      if (newToken) {
        localStorage.setItem('token', newToken)
      } else {
        localStorage.removeItem('token')
      }
    }
  )

  const isAuthenticated = computed(() => !!token.value)
  const userRole = computed<UserRole | null>(() => user.value?.role.value ?? null)
  const userRoleLabel = computed(() => user.value?.role.for_view ?? '')
  const userName = computed(() => user.value?.name ?? '')
  const userGender = computed(() => user.value?.gender.value ?? null)
  const userGenderLabel = computed(() => user.value?.gender.for_view ?? '')

  // Check if user has specific role
  function hasRole(role: UserRole): boolean {
    return userRole.value === role
  }

  // Check if user has any of the specified roles
  function hasAnyRole(roles: UserRole[]): boolean {
    return userRole.value !== null && roles.includes(userRole.value)
  }

  // Check if user is admin (super_admin or admin)
  const isAdmin = computed(() => hasAnyRole(['super_admin', 'admin']))

  // Check if user is super admin
  const isSuperAdmin = computed(() => hasRole('super_admin'))

  // Check if user is teacher
  const isTeacher = computed(() => hasRole('teacher'))

  // Check if user is student
  const isStudent = computed(() => hasRole('student'))

  async function login(credentials: { username_or_email: string; password: string }) {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.post('/auth/login', credentials)
      const { token: newToken, user: newUser } = response.data.data
      
      // Update state (watchers will persist to localStorage)
      token.value = newToken
      user.value = newUser
      
      // Redirect to dashboard
      router.push({ name: 'dashboard' })
      
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'حدث خطأ في تسجيل الدخول'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    loading.value = true
    
    try {
      if (token.value) {
        await api.post('/auth/logout')
      }
    } catch (err) {
      console.error('Logout API error:', err)
    } finally {
      clearAuth()
      loading.value = false
      router.push({ name: 'login' })
    }
  }

  function clearAuth() {
    token.value = null
    user.value = null
    clearStoredAuth()
  }

  async function initAuth() {
    if (token.value && !user.value) {
      // Token exists but user data is missing, fetch from API
      getStoredUser()
    }
  }

  return {
    // State
    user,
    token,
    loading,
    error,
    
    // Getters
    isAuthenticated,
    userRole,
    userRoleLabel,
    userName,
    userGender,
    userGenderLabel,
    isAdmin,
    isSuperAdmin,
    isTeacher,
    isStudent,
    
    // Actions
    login,
    logout,
    initAuth,
    hasRole,
    hasAnyRole,
    clearAuth
  }
})