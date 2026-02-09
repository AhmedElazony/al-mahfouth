<template>
	<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
		<!-- Header -->
		<header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
			<div class="px-4 sm:px-6 lg:px-8">
				<div class="flex items-center justify-between h-16">
					<!-- Logo -->
					<div class="flex items-center gap-4">
						<router-link to="/dashboard"
							class="text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-200 transition-colors font-bold text-lg">
							<h1 class="text-xl font-bold text-primary-600">{{ $t('app.name') }}</h1>
						</router-link>
					</div>

					<!-- Desktop Navigation -->
					<nav class="hidden md:flex items-center gap-6">
						<router-link to="/dashboard" exact
							class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
							active-class="!text-primary-600 dark:!text-primary-400 font-medium">
							{{ $t('nav.dashboard') }}
						</router-link>

						<!-- Admin only: Users Management -->
						<router-link v-if="authStore.isAdmin" to="/dashboard/users"
							class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
							active-class="!text-primary-600 dark:!text-primary-400 font-medium">
							{{ $t('nav.users') }}
						</router-link>

						<!-- Admin/Teacher only links -->
						<template v-if="authStore.isAdmin || authStore.isTeacher">
							<router-link to="/dashboard/groups"
								class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
								active-class="!text-primary-600 dark:!text-primary-400 font-medium">
								{{ $t('nav.groups') }}
							</router-link>
						</template>
						<template>
							<router-link to="/community"
								class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
								active-class="!text-primary-600 dark:!text-primary-400 font-medium">
								{{ $t('nav.community') }}
							</router-link>
						</template>
					</nav>

					<!-- Right Side: User Menu & Mobile Menu Toggle -->
					<div class="flex items-center gap-2">
						<!-- Theme Toggle -->
						<button @click="themeStore.toggleTheme"
							class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
							<i :class="themeStore.isDark ? 'pi pi-sun' : 'pi pi-moon'"></i>
						</button>

						<!-- User Menu -->
						<div class="relative" ref="userMenuRef">
							<button @click="toggleUserMenu"
								class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
								<div
									class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
									<span class="text-primary-600 dark:text-primary-400 font-semibold text-sm">
										{{ authStore.user?.name?.charAt(0).toUpperCase() }}
									</span>
								</div>
								<div class="hidden sm:block text-right">
									<p class="text-sm font-medium text-gray-900 dark:text-white">
										{{ authStore.user?.name }}
									</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">
										{{ authStore.userRoleLabel}}
									</p>
								</div>
								<i
									class="pi pi-chevron-down text-xs text-gray-600 dark:text-gray-400 hidden sm:block"></i>
							</button>

							<!-- User Dropdown -->
							<div v-if="showUserMenu"
								class="absolute left-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50">
								<router-link to="/dashboard/settings" @click="closeUserMenu"
									class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
									<i class="pi pi-cog"></i>
									{{ $t('nav.settings') }}
								</router-link>
								<button @click="handleLogout"
									class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
									<i class="pi pi-sign-out"></i>
									{{ $t('nav.logout') }}
								</button>
							</div>
						</div>

						<!-- Mobile Menu Toggle -->
						<button @click="toggleMobileMenu"
							class="md:hidden p-2 text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
							<i :class="showMobileMenu ? 'pi pi-times' : 'pi pi-bars'" class="text-xl"></i>
						</button>
					</div>
				</div>

				<!-- Mobile Navigation Menu -->
				<transition enter-active-class="transition duration-200 ease-out"
					enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0"
					leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100 translate-y-0"
					leave-to-class="opacity-0 -translate-y-2">
					<nav v-if="showMobileMenu"
						class="md:hidden pb-4 pt-2 space-y-1 border-t border-gray-200 dark:border-gray-700 mt-2">
						<router-link to="/dashboard" @click="closeMobileMenu"
							class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors"
							active-class="!text-primary-600 dark:!text-primary-400 bg-primary-50 dark:bg-primary-900/20 font-medium">
							<i class="pi pi-home mr-2"></i>
							{{ $t('nav.dashboard') }}
						</router-link>

						<!-- Admin only: Users Management -->
						<router-link v-if="authStore.isAdmin" to="/dashboard/users" @click="closeMobileMenu"
							class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors"
							active-class="!text-primary-600 dark:!text-primary-400 bg-primary-50 dark:bg-primary-900/20 font-medium">
							<i class="pi pi-users mr-2"></i>
							{{ $t('nav.users') }}
						</router-link>

						<!-- Admin/Teacher only links -->
						<template v-if="authStore.isAdmin || authStore.isTeacher">
							<router-link to="/dashboard/groups" @click="closeMobileMenu"
								class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors"
								active-class="!text-primary-600 dark:!text-primary-400 bg-primary-50 dark:bg-primary-900/20 font-medium">
								<i class="pi pi-th-large mr-2"></i>
								{{ $t('nav.groups') }}
							</router-link>
						</template>

						<!-- Settings -->
						<router-link to="/dashboard/settings" @click="closeMobileMenu"
							class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors"
							active-class="!text-primary-600 dark:!text-primary-400 bg-primary-50 dark:bg-primary-900/20 font-medium">
							<i class="pi pi-cog mr-2"></i>
							{{ $t('nav.settings') }}
						</router-link>

						<!-- Logout -->
						<button @click="handleLogout"
							class="w-full text-right px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
							<i class="pi pi-sign-out mr-2"></i>
							{{ $t('nav.logout') }}
						</button>
					</nav>
				</transition>
			</div>
		</header>

		<!-- Main Content -->
		<main class="px-4 sm:px-6 lg:px-8 py-8">
			<router-view />
		</main>
	</div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import { useThemeStore } from '@/stores/theme'


const authStore = useAuthStore()
const themeStore = useThemeStore()
const router = useRouter()

// Theme
const isDark = themeStore.isDark
const toggleTheme =	themeStore.toggleTheme 

// User Menu
const showUserMenu = ref(false)
const userMenuRef = ref<HTMLElement | null>(null)

// Mobile Menu
const showMobileMenu = ref(false)

function toggleUserMenu() {
	showUserMenu.value = !showUserMenu.value
}

function closeUserMenu() {
	showUserMenu.value = false
}

function toggleMobileMenu() {
	showMobileMenu.value = !showMobileMenu.value
}

function closeMobileMenu() {
	showMobileMenu.value = false
}

async function handleLogout() {
	try {
		await authStore.logout()
		closeUserMenu()
		closeMobileMenu()
		router.push('/auth/login')
	} catch (error) {
		console.error('Logout failed:', error)
	}
}

// Close user menu when clicking outside
function handleClickOutside(event: MouseEvent) {
	if (userMenuRef.value && !userMenuRef.value.contains(event.target as Node)) {
		closeUserMenu()
	}
}

onMounted(() => {
	document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
	document.removeEventListener('click', handleClickOutside)
})
</script>