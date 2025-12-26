import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User, CreateUserForm, UpdateUserForm, UserFilters } from '@/types/models'
import type { PaginationMeta } from '@/types/api'
import userService from '@/services/userService'

export const useUsersStore = defineStore('users', () => {
  // State
  const users = ref<User[]>([])
  const currentUser = ref<User | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const pagination = ref<PaginationMeta | null>(null)
  const filters = ref<UserFilters>({
    per_page: 15,
    page: 1
  })

  // Getters
  const hasUsers = computed(() => users.value.length > 0)
  const totalPages = computed(() => pagination.value?.last_page ?? 1)
  const currentPage = computed(() => pagination.value?.current_page ?? 1)
  const totalUsers = computed(() => pagination.value?.total ?? 0)

  // Filter users by role
  const teachers = computed(() => users.value.filter(u => u.role.value === 'teacher'))
  const students = computed(() => users.value.filter(u => u.role.value === 'student'))
  const admins = computed(() => users.value.filter(u => u.role.value === 'admin' || u.role.value === 'super_admin'))

  // Actions
  async function fetchUsers(newFilters?: UserFilters) {
    loading.value = true
    error.value = null

    if (newFilters) {
      filters.value = { ...filters.value, ...newFilters }
    }

    try {
      const response = await userService.getUsers(filters.value)
      users.value = response.data
      pagination.value = response.meta
    } catch (err: any) {
      error.value = err.response?.data?.message || 'حدث خطأ في جلب المستخدمين'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchUser(id: number) {
    loading.value = true
    error.value = null

    try {
      const response = await userService.getUser(id)
      currentUser.value = response.data
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'حدث خطأ في جلب بيانات المستخدم'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function createUser(data: CreateUserForm) {
    loading.value = true
    error.value = null

    try {
      const response = await userService.createUser(data)
      // Add to list if we're on the first page
      if (currentPage.value === 1) {
        users.value.unshift(response.data)
      }
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'حدث خطأ في إنشاء المستخدم'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function updateUser(id: number, data: UpdateUserForm) {
    loading.value = true
    error.value = null

    try {
      const response = await userService.updateUser(id, data)
      // Update in list
      const index = users.value.findIndex(u => u.id === id)
      if (index !== -1) {
        users.value[index] = response.data
      }
      // Update current user if it's the same
      if (currentUser.value?.id === id) {
        currentUser.value = response.data
      }
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'حدث خطأ في تحديث المستخدم'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function deleteUser(id: number) {
    loading.value = true
    error.value = null

    try {
      await userService.deleteUser(id)
      // Remove from list
      users.value = users.value.filter(u => u.id !== id)
      // Clear current user if it's the same
      if (currentUser.value?.id === id) {
        currentUser.value = null
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'حدث خطأ في حذف المستخدم'
      throw err
    } finally {
      loading.value = false
    }
  }

  function setPage(page: number) {
    filters.value.page = page
    fetchUsers()
  }

  function setFilters(newFilters: UserFilters) {
    filters.value = { ...filters.value, ...newFilters, page: 1 }
    fetchUsers()
  }

  function clearFilters() {
    filters.value = { per_page: 15, page: 1 }
    fetchUsers()
  }

  function clearError() {
    error.value = null
  }

  return {
    // State
    users,
    currentUser,
    loading,
    error,
    pagination,
    filters,

    // Getters
    hasUsers,
    totalPages,
    currentPage,
    totalUsers,
    teachers,
    students,
    admins,

    // Actions
    fetchUsers,
    fetchUser,
    createUser,
    updateUser,
    deleteUser,
    setPage,
    setFilters,
    clearFilters,
    clearError
  }
})