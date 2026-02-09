<template>
	<div class="space-y-6">
		<!-- Header -->
		<div>
			<h1 class="text-2xl font-bold text-gray-900 dark:text-white">
				{{ $t('dashboard.welcome') }} {{ authStore.user?.name }}
			</h1>
			<p class="text-gray-500 dark:text-gray-400">{{ $t('dashboard.subtitle') }}</p>
		</div>

		<!-- Loading State -->
		<div v-if="loading" class="flex justify-center py-12">
			<i class="pi pi-spinner pi-spin text-3xl text-primary-600"></i>
		</div>

		<!-- Admin Dashboard -->
		<template v-else-if="authStore.isAdmin || authStore.isSuperAdmin">
			<!-- System Stats -->
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
				<!-- Total Groups -->
				<div class="card p-6">
					<div class="flex items-center gap-4">
						<div
							class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
							<i class="pi pi-book text-blue-600 dark:text-blue-400 text-xl"></i>
						</div>
						<div>
							<p class="text-sm text-gray-500 dark:text-gray-400">{{ $t('dashboard.totalGroups') }}</p>
							<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.groups_count || 0 }}
							</p>
						</div>
					</div>
				</div>

				<!-- Total Students -->
				<div class="card p-6">
					<div class="flex items-center gap-4">
						<div
							class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
							<i class="pi pi-users text-green-600 dark:text-green-400 text-xl"></i>
						</div>
						<div>
							<p class="text-sm text-gray-500 dark:text-gray-400">{{ $t('dashboard.totalStudents') }}</p>
							<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.students_count || 0 }}
							</p>
						</div>
					</div>
				</div>

				<!-- Total Teachers -->
				<div class="card p-6">
					<div class="flex items-center gap-4">
						<div
							class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
							<i class="pi pi-user text-purple-600 dark:text-purple-400 text-xl"></i>
						</div>
						<div>
							<p class="text-sm text-gray-500 dark:text-gray-400">{{ $t('dashboard.totalTeachers') }}</p>
							<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.teachers_count || 0 }}
							</p>
						</div>
					</div>
				</div>

				<!-- Active Students -->
				<div class="card p-6">
					<div class="flex items-center gap-4">
						<div
							class="w-12 h-12 bg-amber-100 dark:bg-amber-900/20 rounded-lg flex items-center justify-center">
							<i class="pi pi-check-circle text-amber-600 dark:text-amber-400 text-xl"></i>
						</div>
						<div>
							<p class="text-sm text-gray-500 dark:text-gray-400">{{ $t('dashboard.activeStudents') }}</p>
							<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats?.active_students_count
								|| 0 }}</p>
						</div>
					</div>
				</div>
			</div>

			<!-- Activity Overview -->
			<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
				<!-- Students Activity Chart -->
				<div class="card p-6">
					<h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
						{{ $t('dashboard.studentsActivity') }}
					</h2>
					<div class="space-y-4">
						<!-- Active Students Bar -->
						<div>
							<div class="flex items-center justify-between mb-2">
								<span class="text-sm text-gray-600 dark:text-gray-400">{{ $t('dashboard.activeStudents')
								}}</span>
								<span class="text-sm font-medium text-gray-900 dark:text-white">
									{{ stats?.active_students_count || 0 }}
								</span>
							</div>
							<div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
								<div class="bg-green-600 h-2 rounded-full transition-all duration-300"
									:style="{ width: `${activeStudentsPercentage}%` }"></div>
							</div>
						</div>

						<!-- Inactive Students Bar -->
						<div>
							<div class="flex items-center justify-between mb-2">
								<span class="text-sm text-gray-600 dark:text-gray-400">{{
									$t('dashboard.inactiveStudents') }}</span>
								<span class="text-sm font-medium text-gray-900 dark:text-white">
									{{ stats?.inactive_students_count || 0 }}
								</span>
							</div>
							<div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
								<div class="bg-red-600 h-2 rounded-full transition-all duration-300"
									:style="{ width: `${inactiveStudentsPercentage}%` }"></div>
							</div>
						</div>
					</div>
				</div>

				<!-- Quick Stats Summary -->
				<div class="card p-6">
					<h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
						{{ $t('dashboard.quickSummary') }}
					</h2>
					<div class="space-y-3">
						<div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
							<span class="text-sm text-gray-600 dark:text-gray-400">{{
								$t('dashboard.avgStudentsPerGroup') }}</span>
							<span class="text-sm font-bold text-gray-900 dark:text-white">
								{{ avgStudentsPerGroup }}
							</span>
						</div>
						<div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
							<span class="text-sm text-gray-600 dark:text-gray-400">{{
								$t('dashboard.avgGroupsPerTeacher') }}</span>
							<span class="text-sm font-bold text-gray-900 dark:text-white">
								{{ avgGroupsPerTeacher }}
							</span>
						</div>
						<div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
							<span class="text-sm text-gray-600 dark:text-gray-400">{{ $t('dashboard.activeRate')
							}}</span>
							<span class="text-sm font-bold text-green-600 dark:text-green-400">
								{{ activeStudentsPercentage.toFixed(1) }}%
							</span>
						</div>
					</div>
				</div>
			</div>
		</template>

		<!-- Teacher Dashboard -->
		<template v-else-if="authStore.isTeacher">
			<!-- Quick Stats -->
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<!-- My Groups -->
				<div class="card p-6">
					<div class="flex items-center gap-4">
						<div
							class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
							<i class="pi pi-book text-blue-600 dark:text-blue-400 text-xl"></i>
						</div>
						<div>
							<p class="text-sm text-gray-500 dark:text-gray-400">{{ $t('dashboard.myGroups') }}</p>
							<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ teacherGroupsCount }}</p>
						</div>
					</div>
				</div>
			</div>

			<!-- Teacher Information -->
			<div class="card p-4">
				<h2
					class="text-base font-semibold text-gray-900 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
					{{ $t('dashboard.myInfo') }}
				</h2>
				<div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-sm">
					<div class="flex items-center justify-between py-1.5">
						<span class="text-gray-600 dark:text-gray-400">{{ $t('users.name') }}</span>
						<span class="font-medium text-gray-900 dark:text-white">{{ authStore.user?.name }}</span>
					</div>
					<div class="flex items-center justify-between py-1.5">
						<span class="text-gray-600 dark:text-gray-400">{{ $t('users.email') }}</span>
						<span class="font-medium text-gray-900 dark:text-white">{{ authStore.user?.email }}</span>
					</div>
					<div class="flex items-center justify-between py-1.5">
						<span class="text-gray-600 dark:text-gray-400">{{ $t('users.username') }}</span>
						<span class="font-medium text-gray-900 dark:text-white">{{ authStore.user?.username }}</span>
					</div>
					<div class="flex items-center justify-between py-1.5">
						<span class="text-gray-600 dark:text-gray-400">{{ $t('users.phone') }}</span>
						<span class="font-medium text-gray-900 dark:text-white">{{ authStore.user?.phone || '-'
						}}</span>
					</div>
				</div>
			</div>

			<!-- My Groups List -->
			<div class="card p-4">
				<h2
					class="text-base font-semibold text-gray-900 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
					{{ $t('dashboard.myGroupsList') }}
				</h2>

				<!-- Loading Groups -->
				<div v-if="loadingGroups" class="flex justify-center py-8">
					<i class="pi pi-spinner pi-spin text-2xl text-primary-600"></i>
				</div>

				<!-- Groups List -->
				<div v-else-if="teacherGroups.length > 0" class="space-y-3">
					<div v-for="group in teacherGroups" :key="group.id"
						class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer"
						@click="viewGroupDetails(group)">
						<div class="flex items-center gap-3">
							<div
								class="w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center">
								<i class="pi pi-book text-primary-600 dark:text-primary-400"></i>
							</div>
							<div>
								<p class="font-medium text-gray-900 dark:text-white">{{ group.name }}</p>
								<p class="text-xs text-gray-500 dark:text-gray-400">
									<span
										:class="group.is_online ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400'">
										<i :class="group.is_online ? 'pi pi-video' : 'pi pi-map-marker'"
											class="text-xs mr-1"></i>
										{{ group.is_online ? $t('groups.online') : $t('groups.offline') }}
									</span>
								</p>
							</div>
						</div>
						<div class="flex items-center gap-2">
							<span :class="group.is_active
								? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
								: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'" class="px-2 py-1 text-xs rounded-full">
								{{ group.is_active ? $t('common.active') : $t('common.inactive') }}
							</span>
							<i class="pi pi-chevron-left text-gray-400"></i>
						</div>
					</div>
				</div>

				<!-- Empty State -->
				<div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
					<i class="pi pi-book text-3xl mb-2 block"></i>
					<p>{{ $t('dashboard.noGroups') }}</p>
				</div>

				<!-- View All Link -->
				<router-link v-if="teacherGroups.length > 0" to="/dashboard/groups"
					class="block text-center mt-4 text-sm text-primary-600 dark:text-primary-400 hover:underline">
					{{ $t('dashboard.viewAllGroups') }}
				</router-link>
			</div>
		</template>

		<!-- Quick Actions (for all roles) -->
		<div class="card p-6">
			<h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
				{{ $t('dashboard.quickActions') }}
			</h2>
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
				<!-- View Groups -->
				<router-link to="/dashboard/groups"
					class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors group">
					<i
						class="pi pi-book text-xl text-gray-600 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400"></i>
					<div>
						<p class="font-medium text-sm text-gray-900 dark:text-white">{{ $t('dashboard.viewGroups') }}
						</p>
						<p class="text-xs text-gray-500 dark:text-gray-400">{{ $t('dashboard.manageGroups') }}</p>
					</div>
				</router-link>

				<!-- Manage Users (Admin only) -->
				<router-link v-if="authStore.isAdmin || authStore.isSuperAdmin" to="/dashboard/users"
					class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors group">
					<i
						class="pi pi-users text-xl text-gray-600 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400"></i>
					<div>
						<p class="font-medium text-sm text-gray-900 dark:text-white">{{ $t('dashboard.manageUsers') }}
						</p>
						<p class="text-xs text-gray-500 dark:text-gray-400">{{ $t('dashboard.manageUsersDesc') }}</p>
					</div>
				</router-link>

				<!-- Community -->
				<router-link to="/community"
					class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors group">
					<i
						class="pi pi-comments text-xl text-gray-600 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400"></i>
					<div>
						<p class="font-medium text-sm text-gray-900 dark:text-white">{{ $t('dashboard.community') }}</p>
						<p class="text-xs text-gray-500 dark:text-gray-400">{{ $t('dashboard.communityDesc') }}</p>
					</div>
				</router-link>

				<!-- Settings -->
				<router-link to="/dashboard/settings"
					class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors group">
					<i
						class="pi pi-cog text-xl text-gray-600 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400"></i>
					<div>
						<p class="font-medium text-sm text-gray-900 dark:text-white">{{ $t('nav.settings') }}</p>
						<p class="text-xs text-gray-500 dark:text-gray-400">{{ $t('dashboard.settingsDesc') }}</p>
					</div>
				</router-link>
			</div>
		</div>

		<!-- Recent Activity (placeholder for future) -->
		<div v-if="authStore.isAdmin || authStore.isSuperAdmin" class="card p-6">
			<h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
				{{ $t('dashboard.recentActivity') }}
			</h2>
			<div class="text-center py-8 text-gray-500 dark:text-gray-400">
				<i class="pi pi-clock text-3xl mb-2 block"></i>
				<p>{{ $t('dashboard.recentActivityComingSoon') }}</p>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import statsService, { type SystemStats } from '@/services/statsService'
import groupService from '@/services/groupService'

const authStore = useAuthStore()
const router = useRouter()
const loading = ref(true)
const loadingGroups = ref(false)
const stats = ref<SystemStats | null>(null)
const teacherGroupsCount = ref(0)
// Removed: const teacherStudentsCount = ref(0)
const teacherGroups = ref<any[]>([])

// Computed properties for admin stats
const activeStudentsPercentage = computed(() => {
	if (!stats.value || !stats.value.students_count) return 0
	return (stats.value.active_students_count / stats.value.students_count) * 100
})

const inactiveStudentsPercentage = computed(() => {
	if (!stats.value || !stats.value.students_count) return 0
	return (stats.value.inactive_students_count / stats.value.students_count) * 100
})

const avgStudentsPerGroup = computed(() => {
	if (!stats.value || !stats.value.groups_count) return 0
	return (stats.value.students_count / stats.value.groups_count).toFixed(1)
})

const avgGroupsPerTeacher = computed(() => {
	if (!stats.value || !stats.value.teachers_count) return 0
	return (stats.value.groups_count / stats.value.teachers_count).toFixed(1)
})

async function fetchTeacherGroups() {
	loadingGroups.value = true
	try {
		const response = await groupService.getGroups({
			teacher_id: authStore.user?.id,
			per_page: 1000
		})

		teacherGroups.value = response.data || []
		teacherGroupsCount.value = teacherGroups.value.length

	} catch (error) {
		console.error('Failed to fetch teacher groups:', error)
	} finally {
		loadingGroups.value = false
	}
}

function viewGroupDetails(group: any) {
	router.push(`/dashboard/groups/${group.id}`)
}

async function fetchData() {
	loading.value = true

	try {
		if (authStore.isAdmin || authStore.isSuperAdmin) {
			const response = await statsService.getSystemStats()
			stats.value = response.data
		} else if (authStore.isTeacher) {
			await fetchTeacherGroups()
		}
	} catch (error: any) {
		console.error('Failed to fetch dashboard data:', error)
	} finally {
		loading.value = false
	}
}

onMounted(() => {
	fetchData()
})
</script>