import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { Group, GroupFilters } from '@/types/models'
import groupService from '@/services/groupService'
import type { PaginationMeta } from '@/types'

export const useGroupsStore = defineStore('groups', () => {
  const groups = ref<Group[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const currentPageNum = ref(1)
  const currentPage = computed(() => currentPageNum.value)
  const perPage = ref(15)
  const totalGroups = computed(() => pagination.value?.total ?? 0)
  const totalPages = computed(() => pagination.value?.last_page ?? 1)
  const filters = ref<GroupFilters>({})
  
  const pagination = ref<PaginationMeta | null>(null)
  

  async function fetchGroups(newFilters?: GroupFilters) {
    loading.value = true
    error.value = null

    if (newFilters) {
      filters.value = { ...filters.value, ...newFilters }
    }

    try {
      const response = await groupService.getGroups({
        ...filters.value,
        page: currentPageNum.value,
        per_page: perPage.value
      })
      
      groups.value = response.data || []
      
      pagination.value = response.pagination 
    } catch (err: any) {
      error.value = err.response?.data?.message || 'حدث خطأ في جلب المجموعات'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function setPage(page: number) {
    if (page < 1 || page > totalPages.value) return
    currentPageNum.value = page
    await fetchGroups()
  }

  function setFilters(newFilters: GroupFilters) {
    currentPageNum.value = 1
    fetchGroups(newFilters)
  }

  function clearFilters() {
    filters.value = {}
    currentPageNum.value = 1
    fetchGroups()
  }

  function addGroup(group: Group) {
    groups.value.unshift(group)
    if (pagination.value) {
      pagination.value.total += 1
    }
  }

  function updateGroupInList(group: Group) {
    const index = groups.value.findIndex(g => g.id === group.id)
    if (index !== -1) {
      groups.value[index] = group
    }
  }

  async function deleteGroup(id: number) {
    await groupService.deleteGroup(id)
    groups.value = groups.value.filter(g => g.id !== id)
    if (pagination.value) {
      pagination.value.total -= 1
    }
  }

  return {
    groups,
    loading,
    error,
    currentPage,
    perPage,
    totalGroups,
    totalPages,
    pagination,
    filters,
    fetchGroups,
    setPage,
    setFilters,
    clearFilters,
    addGroup,
    updateGroupInList,
    deleteGroup
  }
})