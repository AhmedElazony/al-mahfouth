<template>
  <div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $t('settings.title') }}</h1>
      <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $t('settings.subtitle') }}</p>
    </div>

    <!-- Profile Section -->
    <div class="card p-6">
      <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
          <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
            {{ authStore.user?.name?.charAt(0).toUpperCase() }}
          </span>
        </div>
        <div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ authStore.user?.name }}</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">@{{ authStore.user?.username }}</p>
          <p class="text-xs text-gray-400 dark:text-gray-500">{{ authStore.userRoleLabel }}</p>
        </div>
      </div>

      <!-- Success Message -->
      <div v-if="successMessage" class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
        <div class="flex items-center gap-2">
          <i class="pi pi-check-circle text-green-600 dark:text-green-400"></i>
          <p class="text-sm text-green-700 dark:text-green-300">{{ successMessage }}</p>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
        <div class="flex items-center gap-2">
          <i class="pi pi-exclamation-circle text-red-600 dark:text-red-400"></i>
          <p class="text-sm text-red-700 dark:text-red-300">{{ error }}</p>
        </div>
      </div>

      <!-- Profile Form -->
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <!-- Name -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $t('users.name') }}
          </label>
          <input
            v-model="form.name"
            type="text"
            class="input w-full"
            :placeholder="$t('users.name')"
          />
        </div>

        <!-- Username -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $t('users.username') }}
          </label>
          <input
            v-model="form.username"
            type="text"
            class="input w-full"
            dir="ltr"
            :placeholder="$t('users.username')"
          />
          <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            {{ $t('users.usernameHint') }}
          </p>
        </div>

        <!-- Phone -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $t('users.phone') }}
          </label>
          <input
            v-model="form.phone"
            type="tel"
            class="input w-full"
            dir="ltr"
            :placeholder="$t('users.phone')"
          />
        </div>

        <!-- Change Password Section -->
        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
          <button
            type="button"
            @click="showPasswordFields = !showPasswordFields"
            class="flex items-center gap-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300"
          >
            <i :class="showPasswordFields ? 'pi pi-chevron-down' : 'pi pi-chevron-left'"></i>
            {{ $t('settings.changePassword') }}
          </button>

          <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2"
          >
            <div v-if="showPasswordFields" class="mt-4 space-y-4">
              <!-- New Password -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $t('settings.newPassword') }}
                </label>
                <div class="relative">
                  <input
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
                    class="input w-full pl-10"
                    dir="ltr"
                    :placeholder="$t('settings.newPassword')"
                  />
                  <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                  >
                    <i :class="showPassword ? 'pi pi-eye-slash' : 'pi pi-eye'"></i>
                  </button>
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  {{ $t('settings.passwordHint') }}
                </p>
              </div>

              <!-- Confirm Password -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $t('settings.confirmPassword') }}
                </label>
                <div class="relative">
                  <input
                    v-model="form.password_confirmation"
                    :type="showPasswordConfirm ? 'text' : 'password'"
                    class="input w-full pl-10"
                    dir="ltr"
                    :placeholder="$t('settings.confirmPassword')"
                  />
                  <button
                    type="button"
                    @click="showPasswordConfirm = !showPasswordConfirm"
                    class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                  >
                    <i :class="showPasswordConfirm ? 'pi pi-eye-slash' : 'pi pi-eye'"></i>
                  </button>
                </div>
              </div>
            </div>
          </transition>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3 pt-4">
          <button
            type="submit"
            :disabled="loading || !hasChanges"
            class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <i v-if="loading" class="pi pi-spinner pi-spin mr-2"></i>
            <i v-else class="pi pi-check mr-2"></i>
            {{ $t('common.save') }}
          </button>
          <button
            type="button"
            @click="resetForm"
            :disabled="loading || !hasChanges"
            class="btn-secondary disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <i class="pi pi-times mr-2"></i>
            {{ $t('common.cancel') }}
          </button>
        </div>
      </form>
    </div>

    <!-- Account Information (Read-only) -->
    <div class="card p-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
        {{ $t('settings.accountInfo') }}
      </h3>
      <div class="space-y-3">
        <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
          <span class="text-sm text-gray-600 dark:text-gray-400">{{ $t('users.email') }}</span>
          <span class="text-sm font-medium text-gray-900 dark:text-white">
            {{ authStore.user?.email }}
          </span>
        </div>
        <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
          <span class="text-sm text-gray-600 dark:text-gray-400">{{ $t('users.role') }}</span>
          <span class="text-sm font-medium text-gray-900 dark:text-white">
            {{ authStore.userRoleLabel }}
          </span>
        </div>
        <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
          <span class="text-sm text-gray-600 dark:text-gray-400">{{ $t('users.gender') }}</span>
          <span class="text-sm font-medium text-gray-900 dark:text-white">
            {{ authStore.userGenderLabel }}
          </span>
        </div>
        <div class="flex items-center justify-between py-2">
          <span class="text-sm text-gray-600 dark:text-gray-400">{{ $t('settings.memberSince') }}</span>
          <span class="text-sm font-medium text-gray-900 dark:text-white">
            {{ formatDate(authStore.user?.created_at) }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import profileService, { type UpdateProfileForm } from '@/services/profileService'

const authStore = useAuthStore()

const loading = ref(false)
const error = ref<string | null>(null)
const successMessage = ref<string | null>(null)
const showPasswordFields = ref(false)
const showPassword = ref(false)
const showPasswordConfirm = ref(false)

const form = reactive<UpdateProfileForm>({
  name: authStore.user?.name || '',
  username: authStore.user?.username || '',
  phone: authStore.user?.phone || '',
  password: '',
  password_confirmation: ''
})

const hasChanges = computed(() => {
  if (form.password || form.password_confirmation) return true
  if (form.name !== authStore.user?.name) return true
  if (form.username !== authStore.user?.username) return true
  if (form.phone !== (authStore.user?.phone || '')) return true
  return false
})

function resetForm() {
  form.name = authStore.user?.name || ''
  form.username = authStore.user?.username || ''
  form.phone = authStore.user?.phone || ''
  form.password = ''
  form.password_confirmation = ''
  showPasswordFields.value = false
  error.value = null
  successMessage.value = null
}

async function handleSubmit() {
  error.value = null
  successMessage.value = null

  // Validation
  if (!form.name || !form.username) {
    error.value = 'الاسم واسم المستخدم مطلوبان'
    return
  }

  if (form.password && form.password !== form.password_confirmation) {
    error.value = 'كلمات المرور غير متطابقة'
    return
  }

  if (form.password && form.password.length < 8) {
    error.value = 'كلمة المرور يجب أن تكون على الأقل 8 أحرف'
    return
  }

  loading.value = true

  try {
    const updateData: UpdateProfileForm = {
      name: form.name,
      username: form.username,
      phone: form.phone || undefined
    }

    // Only include password if changed
    if (form.password) {
      updateData.password = form.password
      updateData.password_confirmation = form.password_confirmation
    }

    const response = await profileService.updateProfile(updateData)
    
    // Update auth store with new user data
    authStore.user = response.data
    
    successMessage.value = 'تم تحديث الملف الشخصي بنجاح'
    
    // Reset password fields
    form.password = ''
    form.password_confirmation = ''
    showPasswordFields.value = false

    // Clear success message after 3 seconds
    setTimeout(() => {
      successMessage.value = null
    }, 3000)
  } catch (err: any) {
    error.value = err.response?.data?.message || 'حدث خطأ في تحديث الملف الشخصي'
  } finally {
    loading.value = false
  }
}

function formatDate(dateString?: string): string {
  if (!dateString) return '-'
  try {
    return new Date(dateString).toLocaleDateString('ar-EG', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch {
    return dateString
  }
}

// Watch for user changes from auth store
watch(
  () => authStore.user,
  (newUser) => {
    if (newUser) {
      form.name = newUser.name
      form.username = newUser.username
      form.phone = newUser.phone || ''
    }
  }
)
</script>