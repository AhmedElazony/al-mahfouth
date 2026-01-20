<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $t('groups.title') }}</h1>
        <p class="text-gray-500 dark:text-gray-400">{{ $t('groups.subtitle') }}</p>
      </div>
      <button @click="openCreateModal" class="btn-primary flex items-center justify-center gap-2">
        <i class="pi pi-plus"></i>
        {{ $t('groups.addGroup') }}
      </button>
    </div>

    <!-- Filters -->
     <div class="card p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="flex-1 min-w-[200px]">
            <input
            v-model="searchQuery"
            type="text"
            :placeholder="$t('common.search')"
            class="input w-full"
            @input="debouncedSearch"
            />
          </div>
          <div>
             <select v-model="selectedTeacher" class="input" @change="applyFilters">
               <option value="" selected>{{ $t('groups.allTeachers') }}</option>
               <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                 {{ teacher.name }}
               </option>
             </select>
          </div>
          <select v-model="selectedStatus" class="input" @change="applyFilters">
            <option value="">{{ $t('groups.allStatuses') }}</option>
            <option value="active">{{ $t('common.active') }}</option>
            <option value="inactive">{{ $t('common.inactive') }}</option>
          </select>
          <button @click="clearFilters" class="btn-secondary">
            {{ $t('common.clearFilters') }}
          </button>
        </div>
      </div>


    <!-- Loading State -->
    <div v-if="groupsStore.loading" class="flex justify-center py-12">
      <i class="pi pi-spinner pi-spin text-4xl text-primary-600"></i>
    </div>

    <!-- Error State -->
    <div v-else-if="groupsStore.error" class="card p-6 text-center">
      <i class="pi pi-exclamation-circle text-4xl text-red-500 mb-4"></i>
      <p class="text-red-600 dark:text-red-400">{{ groupsStore.error }}</p>
      <button @click="groupsStore.fetchGroups()" class="btn-primary mt-4">
        {{ $t('common.retry') }}
      </button>
    </div>

    <!-- Desktop Table -->
    <div v-else class="hidden md:block card overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50 dark:bg-gray-800">
          <tr>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              #
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              {{ $t('groups.name') }}
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              {{ $t('groups.teacher') }}
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              {{ $t('groups.schedule') }}
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              {{ $t('groups.type') }}
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              {{ $t('common.status') }}
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              {{ $t('common.actions') }}
            </th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
          <template v-if="groupsStore.groups.length > 0">
            <tr v-for="group in groupsStore.groups" :key="group.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                {{ group.id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ group.name }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ getTeacherName(group) }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                <div class="flex flex-wrap gap-1">
                  <span 
                    v-for="(item, index) in parseSchedule(group.schedule)" 
                    :key="index"
                    class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-xs"
                  >
                    {{ getDayName(item.day) }} {{ formatTime(item.start_time) }} - {{ formatTime(item.end_time) }}
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="group.is_online ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'" class="px-2 py-1 text-xs rounded-full">
                  {{ group.is_online ? $t('groups.online') : $t('groups.offline') }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="group.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'" class="px-2 py-1 text-xs rounded-full">
                  {{ group.is_active ? $t('common.active') : $t('common.inactive') }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <div class="flex items-center gap-2">
                  <button
                    @click="viewGroupStudents(group)"
                    class="p-2 text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                    :title="$t('groups.students')"
                  >
                    <i class="pi pi-users"></i>
                  </button>
                  <button
                    @click="openEditModal(group)"
                    :disabled="loadingGroupId === group.id"
                    class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors disabled:opacity-50"
                    :title="$t('common.edit')"
                  >
                    <i :class="loadingGroupId === group.id ? 'pi pi-spinner pi-spin' : 'pi pi-pencil'"></i>
                  </button>
                  <button
                    @click="confirmDelete(group)"
                    class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                    :title="$t('common.delete')"
                  >
                    <i class="pi pi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </template>
          <template v-else>
            <tr>
              <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                <i class="pi pi-th-large text-4xl mb-4 block"></i>
                {{ $t('common.noData') }}
              </td>
            </tr>
          </template>
        </tbody>
      </table>

      <!-- Pagination -->
     <div v-if="groupsStore.groups.length > 0" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <div class="text-sm text-gray-500 dark:text-gray-400">
          {{ $t('common.showing') }} {{ groupsStore.pagination.from }} - {{ groupsStore.pagination.to }} 
          {{ $t('common.of') }} {{ groupsStore.totalGroups }}
        </div>
        <div class="flex items-center gap-2">
          <button
            @click="groupsStore.setPage(groupsStore.currentPage - 1)"
            :disabled="groupsStore.currentPage === 1"
            class="btn-secondary px-3 py-1 disabled:opacity-50"
          >
            <i class="pi pi-chevron-right"></i>
          </button>
          <span class="text-sm text-gray-700 dark:text-gray-300">
            {{ groupsStore.currentPage }} / {{ groupsStore.totalPages }}
          </span>
          <button
            @click="groupsStore.setPage(groupsStore.currentPage + 1)"
            :disabled="groupsStore.currentPage === groupsStore.totalPages"
            class="btn-secondary px-3 py-1 disabled:opacity-50"
          >
            <i class="pi pi-chevron-left"></i>
          </button>
        </div>
      </div> 
    </div>

    <!-- Mobile Cards -->
    <div v-if="!groupsStore.loading && !groupsStore.error" class="md:hidden space-y-4">
      <template v-if="groupsStore.groups.length > 0">
        <div 
          v-for="group in groupsStore.groups" 
          :key="group.id" 
          class="card p-4 space-y-3"
        >
          <div class="flex items-start justify-between">
            <div>
              <div class="font-medium text-gray-900 dark:text-white">{{ group.name }}</div>
              <div class="text-sm text-gray-500 dark:text-gray-400">{{ getTeacherName(group) }}</div>
            </div>
            <div class="flex items-center gap-1">
              <button
                @click="viewGroupStudents(group)"
                class="p-2 text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
              >
                <i class="pi pi-users"></i>
              </button>
              <button
                @click="openEditModal(group)"
                :disabled="loadingGroupId === group.id"
                class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg disabled:opacity-50"
              >
                <i :class="loadingGroupId === group.id ? 'pi pi-spinner pi-spin' : 'pi pi-pencil'"></i>
              </button>
              <button
                @click="confirmDelete(group)"
                class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg"
              >
                <i class="pi pi-trash"></i>
              </button>
            </div>
          </div>

          <div class="flex flex-wrap gap-2">
            <span :class="group.is_online ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'" class="px-2 py-0.5 text-xs rounded-full">
              {{ group.is_online ? $t('groups.online') : $t('groups.offline') }}
            </span>
            <span :class="group.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'" class="px-2 py-0.5 text-xs rounded-full">
              {{ group.is_active ? $t('common.active') : $t('common.inactive') }}
            </span>
          </div>

          <div class="flex flex-wrap gap-1 text-xs">
            <span 
              v-for="(item, index) in parseSchedule(group.schedule)" 
              :key="index"
              class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded"
            >
              {{ getDayName(item.day) }} {{ formatTime(item.start_time) }}
            </span>
          </div>
        </div>

        <!-- Mobile Pagination -->
       <div v-if="groupsStore.groups.length > 0" class="card p-4 flex flex-col gap-4">
          <div class="text-sm text-center text-gray-500 dark:text-gray-400">
            {{ $t('common.showing') }} {{ groupsStore.pagination.from }} - {{ groupsStore.pagination.to }} 
            {{ $t('common.of') }} {{ groupsStore.totalGroups }}
          </div>
          <div class="flex items-center justify-center gap-4">
            <button
              @click="groupsStore.setPage(groupsStore.currentPage - 1)"
              :disabled="groupsStore.currentPage === 1"
              class="btn-secondary px-4 py-2 disabled:opacity-50"
            >
              <i class="pi pi-chevron-right ml-1"></i>
            </button>
            <span class="text-sm text-gray-700 dark:text-gray-300">
              {{ groupsStore.currentPage }} / {{ groupsStore.totalPages }}
            </span>
            <button
              @click="groupsStore.setPage(groupsStore.currentPage + 1)"
              :disabled="groupsStore.currentPage === groupsStore.totalPages"
              class="btn-secondary px-4 py-2 disabled:opacity-50"
            >
              <i class="pi pi-chevron-left mr-1"></i>
            </button>
          </div>
        </div> 
      </template>
      <template v-else>
        <div class="card p-6 text-center text-gray-500 dark:text-gray-400">
          <i class="pi pi-th-large text-4xl mb-4 block"></i>
          {{ $t('common.noData') }}
        </div>
      </template>
    </div>

    <!-- Create/Edit Modal -->
    <GroupFormModal
      v-if="showModal"
      :group="selectedGroup"
      :is-editing="isEditing"
      @close="closeModal"
      @created="onGroupCreated"
      @updated="onGroupUpdated"
    />

    <!-- Students Modal -->
    <GroupStudentsModal
      v-if="showStudentsModal && selectedGroup"
      :group="selectedGroup"
      @close="showStudentsModal = false"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      v-if="showDeleteModal"
      :title="$t('groups.deleteGroup')"
      :message="$t('groups.deleteConfirmation', { name: groupToDelete?.name })"
      :confirm-text="$t('common.delete')"
      :cancel-text="$t('common.cancel')"
      variant="danger"
      @confirm="handleDelete"
      @cancel="showDeleteModal = false"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useGroupsStore } from '@/stores/groups'
import groupService from '@/services/groupService'
import type { Group, CreateGroupForm, UpdateGroupForm, ScheduleItem } from '@/types/models'
import GroupFormModal from '@/components/groups/GroupFormModal.vue'
import GroupStudentsModal from '@/components/groups/GroupStudentsModal.vue'
import ConfirmModal from '@/components/common/ConfirmModal.vue'
import { WeekDayLabels, getEnumLabel } from '@/constants'
import userService from '@/services/userService'

const groupsStore = useGroupsStore()

// Filters
const searchQuery = ref('')
const selectedStatus = ref<string>('')
const selectedTeacher = ref<string>('')

const teachers = ref<Array<{ id: number; name: string }>>([])

// Modal state
const showModal = ref(false)
const isEditing = ref(false)
const selectedGroup = ref<Group | null>(null)
const loadingGroupId = ref<number | null>(null)

// Students modal
const showStudentsModal = ref(false)

// Delete modal state
const showDeleteModal = ref(false)
const groupToDelete = ref<Group | null>(null)

/**
 * Parse schedule - handle both string and array formats
 */
function parseSchedule(schedule: any): ScheduleItem[] {
  if (!schedule) return []
  
  if (Array.isArray(schedule)) {
    return schedule
  }
  
  if (typeof schedule === 'string') {
    try {
      const parsed = JSON.parse(schedule)
      return Array.isArray(parsed) ? parsed : []
    } catch {
      return []
    }
  }
  
  return []
}

/**
 * Get day name in Arabic using constants
 */
function getDayName(day: string): string {
  if (!day) return ''
  const dayValue = typeof day === 'object' ? (day as any).value : day
  return getEnumLabel(WeekDayLabels, dayValue?.toLowerCase())
}

/**
 * Format time string (handle HH:mm:ss and HH:mm formats)
 */
function formatTime(time: string): string {
  if (!time) return ''
  
  if (typeof time === 'object') {
    time = (time as any).value || ''
  }
  
  const parts = time.split(':')
  if (parts.length >= 2) {
    const hours = parseInt(parts[0])
    const minutes = parts[1]
    const period = hours >= 12 ? 'م' : 'ص'
    const displayHours = hours > 12 ? hours - 12 : hours === 0 ? 12 : hours
    return `${displayHours}:${minutes} ${period}`
  }
  
  return time
}

/**
 * Get teacher name from group
 */
function getTeacherName(group: Group): string {
  if (!group.teacher) return '-'
  
  if (typeof group.teacher === 'object') {
    return group.teacher.name || (group.teacher as any).user?.name || '-'
  }
  
  return '-'
}

// Debounced search
let searchTimeout: ReturnType<typeof setTimeout>
function debouncedSearch() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 300)
}

function applyFilters() {
  groupsStore.setFilters({
    search: searchQuery.value || undefined,
    teacher_id: selectedTeacher.value || undefined,
    is_active: selectedStatus.value === 'active' ? true : selectedStatus.value === 'inactive' ? false : undefined
  })
}

function clearFilters() {
  searchQuery.value = ''
  selectedStatus.value = ''
  selectedTeacher.value = ''
  groupsStore.clearFilters()
}

// Modal actions
function openCreateModal() {
  selectedGroup.value = null
  isEditing.value = false
  showModal.value = true
}

async function openEditModal(group: Group) {
  loadingGroupId.value = group.id
  isEditing.value = true

  try {
    const response = await groupService.getGroup(group.id)
    selectedGroup.value = response.data
    showModal.value = true
  } catch (err) {
    console.error('Failed to fetch group details:', err)
    groupsStore.error = 'حدث خطأ في جلب بيانات المجموعة'
  } finally {
    loadingGroupId.value = null
  }
}

function closeModal() {
  showModal.value = false
  selectedGroup.value = null
  isEditing.value = false
}

function onGroupCreated(group: Group) {
  groupsStore.addGroup(group)
  closeModal()
}

function onGroupUpdated(group: Group) {
  groupsStore.updateGroupInList(group)
  closeModal()
}

function viewGroupStudents(group: Group) {
  selectedGroup.value = group
  showStudentsModal.value = true
}

// Delete actions
function confirmDelete(group: Group) {
  groupToDelete.value = group
  showDeleteModal.value = true
}

async function handleDelete() {
  if (!groupToDelete.value) return
  
  try {
    await groupsStore.deleteGroup(groupToDelete.value.id)
    showDeleteModal.value = false
    groupToDelete.value = null
  } catch (err) {
    // Error handled in store
  }
}

async function fetchTeachers() {
  try {
    const response = await userService.getUsers({ role: 'teacher' })
    teachers.value = response.data || []
  } catch (err) {
    console.error('Failed to fetch teachers:', err)
  }
}

onMounted(() => {
  groupsStore.fetchGroups()
  fetchTeachers()
})
</script>