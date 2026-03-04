<!-- filepath: frontend/src/pages/auth/LoginPage.vue -->
<template>
	<div class="card p-8">
		<!-- Logo/Header -->
		<div class="text-center mb-8">
			<h1 class="text-3xl font-bold text-primary-600 mb-2">{{ $t('app.name') }}</h1>
			<p class="text-gray-500 dark:text-gray-400">{{ $t('auth.loginSubtitle') }}</p>
		</div>

		<!-- Login Form -->
		<form @submit.prevent="handleLogin" class="space-y-6">
			<!-- Error Message -->
			<div v-if="authStore.error"
				class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded-lg text-sm">
				{{ authStore.error }}
			</div>

			<!-- Username/Email Field -->
			<div>
				<label for="username_or_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
					{{ $t('auth.username') }} / {{ $t('auth.email') }}
				</label>
				<input id="username_or_email" v-model="form.username_or_email" type="text" required
					autocomplete="username" :disabled="authStore.loading" class="input"
					:placeholder="$t('auth.username')" />
			</div>

			<!-- Password Field -->
			<div>
				<label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
					{{ $t('auth.password') }}
				</label>
				<div class="relative">
					<input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" required
						autocomplete="current-password" :disabled="authStore.loading" class="input pl-10"
						:placeholder="$t('auth.password')" />
					<button type="button" @click="showPassword = !showPassword"
						class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
						<i :class="showPassword ? 'pi pi-eye-slash' : 'pi pi-eye'"></i>
					</button>
				</div>
			</div>

			<!-- Submit Button -->
			<button type="submit" :disabled="authStore.loading || !isFormValid"
				class="w-full btn-primary py-3 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
				<i v-if="authStore.loading" class="pi pi-spinner pi-spin"></i>
				<span>{{ authStore.loading ? $t('common.loading') : $t('auth.loginButton') }}</span>
			</button>
		</form>

		<!-- Registration Notice -->
		<div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
			<div
				class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
				<i class="pi pi-info-circle text-blue-600 dark:text-blue-400 text-lg mt-0.5 flex-shrink-0"></i>
				<div class="text-sm">
					<p class="font-medium text-blue-900 dark:text-blue-300 mb-1">
						{{ $t('auth.noAccount') }}
					</p>
					<p class="text-blue-700 dark:text-blue-400">
						{{ $t('auth.contactAdmin') }}
					</p>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const form = ref({
	username_or_email: '',
	password: ''
})

const showPassword = ref(false)

const isFormValid = computed(() => {
	return form.value.username_or_email.trim() !== '' && form.value.password.trim() !== ''
})

async function handleLogin() {
	if (!isFormValid.value) return

	try {
		await authStore.login({
			username_or_email: form.value.username_or_email,
			password: form.value.password
		})
	} catch (err) {
		// Error is handled in the store
		console.error('Login failed:', err)
	}
}
</script>