<!-- filepath: frontend/src/pages/users/UsersListPage.vue -->
<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $t('users.title') }}</h1>
        <p class="text-gray-500 dark:text-gray-400">{{ $t('users.subtitle') }}</p>
      </div>
      <button @click="openCreateModal" class="btn-primary flex items-center justify-center gap-2">
        <i class="pi pi-plus"></i>
        {{ $t('users.addUser') }}
      </button>
    </div>

    <!-- Filters -->
    <div class="card p-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Search -->
        <div>
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="$t('common.search')"
            class="input w-full"
            @input="debouncedSearch"
          />
        </div>

        <!-- Role Filter -->
        <div>
          <select v-model="selectedRole" class="input w-full" @change="applyFilters">
            <option value="">{{ $t('users.allRoles') }}</option>
            <option value="admin">{{ $t('roles.admin') }}</option>
            <option value="teacher">{{ $t('roles.teacher') }}</option>
            <option value="student">{{ $t('roles.student') }}</option>
          </select>
        </div>

        <!-- Gender Filter -->
        <div>
          <select v-model="selectedGender" class="input w-full" @change="applyFilters">
            <option value="">{{ $t('users.allGenders') }}</option>
            <option value="male">{{ $t('users.male') }}</option>
            <option value="female">{{ $t('users.female') }}</option>
          </select>
        </div>

        <!-- Clear Filters -->
        <div>
          <button @click="clearFilters" class="btn-secondary w-full">
            {{ $t('common.clearFilters') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="usersStore.loading && !usersStore.hasUsers" class="flex justify-center py-12">
      <i class="pi pi-spinner pi-spin text-4xl text-primary-600"></i>
    </div>

    <!-- Error State -->
    <div v-else-if="usersStore.error" class="card p-6 text-center">
      <i class="pi pi-exclamation-circle text-4xl text-red-500 mb-4"></i>
      <p class="text-red-600 dark:text-red-400">{{ usersStore.error }}</p>
      <button @click="usersStore.fetchUsers()" class="btn-primary mt-4">
        {{ $t('common.retry') }}
      </button>
    </div>

    <!-- Desktop Table (hidden on mobile) -->
    <div v-else class="hidden md:block card overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50 dark:bg-gray-800">
          <tr>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              #
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              {{ $t('users.name') }}
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              {{ $t('users.email') }}
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              {{ $t('users.phone') }}
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              {{ $t('users.role') }}
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              {{ $t('common.actions') }}
            </th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-for="user in usersStore.users" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
              {{ user.id }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                  <span class="text-primary-600 dark:text-primary-400 font-medium">
                    {{ user.name.charAt(0) }}
                  </span>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-900 dark:text-white">{{ user.name }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">@{{ user.username }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
              {{ user.email }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm" dir="ltr">
              <a 
                v-if="user.phone" 
                :href="`tel:${user.phone}`" 
                class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 hover:underline inline-flex items-center gap-1"
              >
                <i class="pi pi-phone text-xs"></i>
                {{ user.phone }}
              </a>
              <span v-else class="text-gray-400">-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getRoleBadgeClass(user.role.value)" class="px-2 py-1 text-xs rounded-full">
                {{ user.role.for_view }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <div class="flex items-center gap-2">
                <button
                  @click="openEditModal(user)"
                  :disabled="loadingUserId === user.id"
                  class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors disabled:opacity-50"
                  :title="$t('common.edit')"
                >
                  <i :class="loadingUserId === user.id ? 'pi pi-spinner pi-spin' : 'pi pi-pencil'"></i>
                </button>
                <button
                  @click="confirmDelete(user)"
                  class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                  :title="$t('common.delete')"
                >
                  <i class="pi pi-trash"></i>
                </button>
              </div>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-if="!usersStore.hasUsers">
            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
              <i class="pi pi-users text-4xl mb-4 block"></i>
              {{ $t('common.noData') }}
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Desktop Pagination -->
      <div v-if="usersStore.hasUsers" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <div class="text-sm text-gray-500 dark:text-gray-400">
          {{ $t('common.showing') }} {{ usersStore.pagination?.from }} - {{ usersStore.pagination?.to }} 
          {{ $t('common.of') }} {{ usersStore.totalUsers }}
        </div>
        <div class="flex items-center gap-2">
          <button
            @click="usersStore.setPage(usersStore.currentPage - 1)"
            :disabled="usersStore.currentPage === 1"
            class="btn-secondary px-3 py-1 disabled:opacity-50"
          >
            <i class="pi pi-chevron-right"></i>
          </button>
          <span class="text-sm text-gray-700 dark:text-gray-300">
            {{ usersStore.currentPage }} / {{ usersStore.totalPages }}
          </span>
          <button
            @click="usersStore.setPage(usersStore.currentPage + 1)"
            :disabled="usersStore.currentPage === usersStore.totalPages"
            class="btn-secondary px-3 py-1 disabled:opacity-50"
          >
            <i class="pi pi-chevron-left"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Cards (hidden on desktop) -->
    <div v-if="!usersStore.loading && !usersStore.error" class="md:hidden space-y-4">
      <!-- Empty State -->
      <div v-if="!usersStore.hasUsers" class="card p-6 text-center text-gray-500 dark:text-gray-400">
        <i class="pi pi-users text-4xl mb-4 block"></i>
        {{ $t('common.noData') }}
      </div>

      <!-- User Cards -->
      <div 
        v-for="user in usersStore.users" 
        :key="user.id" 
        class="card p-4 space-y-3"
      >
        <!-- Header: Avatar, Name, Actions -->
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
              <span class="text-primary-600 dark:text-primary-400 font-medium text-lg">
                {{ user.name.charAt(0) }}
              </span>
            </div>
            <div>
              <div class="font-medium text-gray-900 dark:text-white">{{ user.name }}</div>
              <div class="text-sm text-gray-500 dark:text-gray-400">@{{ user.username }}</div>
            </div>
          </div>
          <div class="flex items-center gap-1">
            <button
              @click="openEditModal(user)"
              :disabled="loadingUserId === user.id"
              class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors disabled:opacity-50"
            >
              <i :class="loadingUserId === user.id ? 'pi pi-spinner pi-spin' : 'pi pi-pencil'"></i>
            </button>
            <button
              @click="confirmDelete(user)"
              class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
            >
              <i class="pi pi-trash"></i>
            </button>
          </div>
        </div>

        <!-- Details -->
        <div class="grid grid-cols-2 gap-3 text-sm">
          <div>
            <span class="text-gray-500 dark:text-gray-400">{{ $t('users.role') }}:</span>
            <span :class="getRoleBadgeClass(user.role.value)" class="px-2 py-0.5 text-xs rounded-full mr-2">
              {{ user.role.for_view }}
            </span>
          </div>
          <div class="text-gray-500 dark:text-gray-400">
            #{{ user.id }}
          </div>
        </div>

        <!-- Contact Info -->
        <div class="space-y-2 text-sm border-t border-gray-200 dark:border-gray-700 pt-3">
          <div class="flex items-center gap-2">
            <i class="pi pi-envelope text-gray-400"></i>
            <a :href="`mailto:${user.email}`" class="text-primary-600 dark:text-primary-400 hover:underline truncate">
              {{ user.email }}
            </a>
          </div>
          <div class="flex items-center gap-2" dir="ltr">
            <i class="pi pi-phone text-gray-400"></i>
            <a 
              v-if="user.phone" 
              :href="`tel:${user.phone}`" 
              class="text-primary-600 dark:text-primary-400 hover:underline"
            >
              {{ user.phone }}
            </a>
            <span v-else class="text-gray-400">-</span>
          </div>
        </div>
      </div>

      <!-- Mobile Pagination -->
      <div v-if="usersStore.hasUsers" class="card p-4 flex flex-col gap-4">
        <div class="text-sm text-center text-gray-500 dark:text-gray-400">
          {{ $t('common.showing') }} {{ usersStore.pagination?.from }} - {{ usersStore.pagination?.to }} 
          {{ $t('common.of') }} {{ usersStore.totalUsers }}
        </div>
        <div class="flex items-center justify-center gap-4">
          <button
            @click="usersStore.setPage(usersStore.currentPage - 1)"
            :disabled="usersStore.currentPage === 1"
            class="btn-secondary px-4 py-2 disabled:opacity-50"
          >
            <i class="pi pi-chevron-right ml-1"></i>
            {{ $t('common.previous') }}
          </button>
          <span class="text-sm text-gray-700 dark:text-gray-300">
            {{ usersStore.currentPage }} / {{ usersStore.totalPages }}
          </span>
          <button
            @click="usersStore.setPage(usersStore.currentPage + 1)"
            :disabled="usersStore.currentPage === usersStore.totalPages"
            class="btn-secondary px-4 py-2 disabled:opacity-50"
          >
            {{ $t('common.next') }}
            <i class="pi pi-chevron-left mr-1"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <UserFormModal
      v-if="showModal"
      :user="selectedUserDetails"
      :is-editing="isEditing"
      @close="closeModal"
      @save="handleSave"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      v-if="showDeleteModal"
      :title="$t('users.deleteUser')"
      :message="$t('users.deleteConfirmation', { name: userToDelete?.name })"
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
import { useUsersStore } from '@/stores/users'
import userService from '@/services/userService'
import type { User, CreateUserForm, UpdateUserForm, UserRole } from '@/types/models'
import UserFormModal from '@/components/users/UserFormModal.vue'
import ConfirmModal from '@/components/common/ConfirmModal.vue'

const usersStore = useUsersStore()

// Filters
const searchQuery = ref('')
const selectedRole = ref<UserRole | ''>('')
const selectedGender = ref<'male' | 'female' | ''>('')

// Modal state
const showModal = ref(false)
const isEditing = ref(false)
const selectedUserDetails = ref<User | null>(null)
const loadingUserId = ref<number | null>(null)

// Delete modal state
const showDeleteModal = ref(false)
const userToDelete = ref<User | null>(null)

// Debounced search
let searchTimeout: ReturnType<typeof setTimeout>
function debouncedSearch() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 300)
}

function applyFilters() {
  usersStore.setFilters({
    search: searchQuery.value || undefined,
    role: selectedRole.value || undefined,
    gender: selectedGender.value || undefined
  })
}

function clearFilters() {
  searchQuery.value = ''
  selectedRole.value = ''
  selectedGender.value = ''
  usersStore.clearFilters()
}

function getRoleBadgeClass(role: UserRole): string {
  const classes: Record<UserRole, string> = {
    super_admin: 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
    admin: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    teacher: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    student: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
  }
  return classes[role]
}

// Modal actions
function openCreateModal() {
  selectedUserDetails.value = null
  isEditing.value = false
  showModal.value = true
}

async function openEditModal(user: User) {
  loadingUserId.value = user.id
  isEditing.value = true

  try {
    // Fetch full user details including profile data
    const response = await userService.getUser(user.id)
    selectedUserDetails.value = response.data
    showModal.value = true
  } catch (err) {
    console.error('Failed to fetch user details:', err)
    usersStore.error = 'حدث خطأ في جلب بيانات المستخدم'
  } finally {
    loadingUserId.value = null
  }
}

function closeModal() {
  showModal.value = false
  selectedUserDetails.value = null
  isEditing.value = false
}

async function handleSave(data: CreateUserForm | UpdateUserForm) {
  try {
    if (isEditing.value && selectedUserDetails.value) {
      await usersStore.updateUser(selectedUserDetails.value.id, data as UpdateUserForm)
    } else {
      await usersStore.createUser(data as CreateUserForm)
    }
    closeModal()
  } catch (err) {
    // Error handled in store
  }
}

// Delete actions
function confirmDelete(user: User) {
  userToDelete.value = user
  showDeleteModal.value = true
}

async function handleDelete() {
  if (!userToDelete.value) return
  
  try {
    await usersStore.deleteUser(userToDelete.value.id)
    showDeleteModal.value = false
    userToDelete.value = null
  } catch (err) {
    // Error handled in store
  }
}

onMounted(() => {
  usersStore.fetchUsers()
})
</script>