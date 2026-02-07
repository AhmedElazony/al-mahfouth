<template>
	<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
		<!-- Header -->
		<header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
			<div class="px-4 sm:px-6 lg:px-8">
				<div class="flex items-center justify-between h-16">
					<!-- Logo -->
					<div class="flex items-center gap-4">
						<router-link to="/"
							class="text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-200 transition-colors font-bold text-lg">
							<h1 class="text-xl font-bold text-primary-600">{{ $t('app.name') }}</h1>
						</router-link>
					</div>

					<!-- Navigation -->
					<nav class="hidden md:flex items-center gap-6">
						<router-link to="/"
							class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
							active-class="!text-primary-600 dark:!text-primary-400 font-medium">
							{{ $t('nav.dashboard') }}
						</router-link>

						<!-- Admin only: Users Management -->
						<router-link v-if="authStore.isAdmin" to="/users"
							class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
							active-class="!text-primary-600 dark:!text-primary-400 font-medium">
							{{ $t('nav.users') }}
						</router-link>

						<!-- Admin/Teacher only links -->
						<template v-if="authStore.isAdmin || authStore.isTeacher">
							<router-link to="/groups"
								class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
								active-class="!text-primary-600 dark:!text-primary-400 font-medium">
								{{ $t('nav.groups') }}
							</router-link>
						</template>
					</nav>

					<!-- User Menu -->
					<div class="flex items-center gap-4">
						<!-- Theme Toggle -->
						<button @click="themeStore.toggleTheme"
							class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
							<i :class="themeStore.isDark ? 'pi pi-sun' : 'pi pi-moon'"></i>
						</button>

						<!-- User Info & Logout -->
						<div class="flex items-center gap-3">
							<div class="text-left">
								<p class="text-sm font-medium text-gray-700 dark:text-gray-300">
									{{ authStore.userName }}
								</p>
								<p class="text-xs text-gray-500 dark:text-gray-400">
									{{ authStore.userRoleLabel }}
								</p>
							</div>
							<button @click="handleLogout" :disabled="authStore.loading"
								class="p-2 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors"
								:title="$t('nav.logout')">
								<i v-if="authStore.loading" class="pi pi-spinner pi-spin"></i>
								<i v-else class="pi pi-sign-out"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
		</header>

		<!-- Main Content -->
		<main class="p-4 sm:p-6 lg:p-8">
			<router-view />
		</main>
	</div>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'

const authStore = useAuthStore()
const themeStore = useThemeStore()

async function handleLogout() {
	await authStore.logout()
}
</script>