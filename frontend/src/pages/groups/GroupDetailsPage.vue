<template>
	<div class="space-y-4 md:space-y-6 px-4 md:px-0">
		<!-- Breadcrumb & Header -->
		<div class="flex items-center gap-2 text-xs md:text-sm text-gray-500 dark:text-gray-400">
			<router-link to="/groups" class="hover:text-primary-600">
				{{ $t('groups.title') }}
			</router-link>
			<i class="pi pi-chevron-left text-xs"></i>
			<span class="text-gray-900 dark:text-white truncate">{{ group?.name }}</span>
		</div>

		<!-- Loading State -->
		<div v-if="loading" class="flex justify-center py-12">
			<i class="pi pi-spinner pi-spin text-3xl text-primary-600"></i>
		</div>

		<!-- Error State -->
		<div v-else-if="error" class="card p-4 md:p-6">
			<div class="text-center text-red-600 dark:text-red-400">
				<i class="pi pi-exclamation-circle text-3xl md:text-4xl mb-4"></i>
				<p class="text-sm md:text-base">{{ error }}</p>
				<button @click="fetchGroupDetails" class="btn-primary mt-4">
					{{ $t('common.retry') }}
				</button>
			</div>
		</div>

		<!-- Group Details -->
		<div v-else-if="group">
			<!-- Group Info Card -->
			<div class="card p-4 md:p-6">
				<!-- Header - Stack on mobile -->
				<div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-4 md:mb-6">
					<div class="flex-1 min-w-0">
						<h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white truncate">
							{{ group.name }}
						</h1>
						<div
							class="flex flex-wrap items-center gap-2 md:gap-4 mt-2 text-xs md:text-sm text-gray-500 dark:text-gray-400">
							<span class="flex items-center gap-1">
								<i class="pi pi-user text-xs"></i>
								<span class="truncate max-w-[120px] md:max-w-none">{{ group.teacher?.name }}</span>
							</span>
							<span class="flex items-center gap-1 whitespace-nowrap">
								<i class="pi pi-users text-xs"></i>
								{{ studentsCount }}
							</span>
							<span :class="group.is_active ? 'text-green-600' : 'text-red-600'"
								class="whitespace-nowrap">
								{{ group.is_active ? $t('common.active') : $t('common.inactive') }}
							</span>
						</div>
					</div>

					<!-- Action Button - Full width on mobile -->
					<div class="flex flex-col gap-2 w-full md:w-auto">
						<div class="flex gap-2">
							<button v-if="canEditGroup" @click="openEditModal"
								class="btn-secondary flex-1 md:flex-none text-sm md:text-base">
								<i class="pi pi-pencil mr-2"></i>
								<span class="hidden sm:inline">{{ $t('common.edit') }}</span>
								<span class="sm:hidden">تعديل</span>
							</button>
							<button v-else disabled
								class="btn-secondary flex-1 md:flex-none text-sm md:text-base opacity-50 cursor-not-allowed"
								:title="$t('groups.contactAdminToEdit')">
								<i class="pi pi-pencil mr-2"></i>
								<span class="hidden sm:inline">{{ $t('common.edit') }}</span>
								<span class="sm:hidden">تعديل</span>
							</button>
						</div>
						<!-- Teacher Info Message -->
						<div v-if="!canEditGroup"
							class="flex items-start gap-2 p-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
							<i
								class="pi pi-info-circle text-blue-600 dark:text-blue-400 text-sm mt-0.5 flex-shrink-0"></i>
							<p class="text-xs text-blue-700 dark:text-blue-300">
								{{ $t('common.contactAdminToEdit') }}
							</p>
						</div>
					</div>
				</div>

				<!-- Schedule - Horizontal scroll on mobile -->
				<div v-if="group.schedule?.length" class="mt-4">
					<h3 class="text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
						{{ $t('groups.schedule') }}
					</h3>
					<div class="flex gap-2 overflow-x-auto pb-2 -mx-4 px-4 md:mx-0 md:px-0 md:flex-wrap">
						<span v-for="(item, index) in parseSchedule(group.schedule)" :key="index"
							class="px-2 md:px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-xs md:text-sm whitespace-nowrap flex-shrink-0">
							{{ getDayName(item.day) }} {{ formatTime(item.start_time) }} - {{ formatTime(item.end_time)
							}}
						</span>
					</div>
				</div>
			</div>

			<!-- Tabs -->
			<div class="card">
				<!-- Tab Navigation - Scrollable on mobile -->
				<div class="border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
					<nav class="flex -mb-px min-w-max md:min-w-0">
						<button @click="activeTab = 'students'" :class="[
							'px-4 md:px-6 py-2 md:py-3 text-xs md:text-sm font-medium border-b-2 transition-colors whitespace-nowrap',
							activeTab === 'students'
								? 'border-primary-600 text-primary-600'
								: 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
						]">
							<i class="pi pi-users mr-1 md:mr-2"></i>
							{{ $t('groups.students') }}
						</button>
						<button @click="activeTab = 'reports'" :class="[
							'px-4 md:px-6 py-2 md:py-3 text-xs md:text-sm font-medium border-b-2 transition-colors whitespace-nowrap',
							activeTab === 'reports'
								? 'border-primary-600 text-primary-600'
								: 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
						]">
							<i class="pi pi-file-edit mr-1 md:mr-2"></i>
							{{ $t('reports.title') }}
						</button>
					</nav>
				</div>

				<!-- Students Tab -->
				<div v-show="activeTab === 'students'" class="p-4 md:p-6">
					<GroupStudentsList :group="group" :students="students" @refresh="fetchStudents"
						@manage-students="showManageStudents = true" />
				</div>

				<!-- Reports Tab -->
				<div v-show="activeTab === 'reports'" class="p-4 md:p-6">
					<GroupReportsList :group="group" :students="students" />
				</div>
			</div>
		</div>

		<!-- Edit Group Modal - Full screen on mobile -->
		<GroupFormModal v-if="showEditModal && group" :group="group" :is-editing="true" @close="closeEditModal"
			@updated="onGroupUpdated" />

		<!-- Manage Students Modal - Full screen on mobile -->
		<GroupStudentsModal v-if="showManageStudents" :group="group" :students="students"
			@close="showManageStudents = false" @updated="onStudentsUpdated" />
	</div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import type { Group, ScheduleItem } from '@/types/models'
import groupService, { type GroupStudentResponse } from '@/services/groupService'
import GroupStudentsList from '@/components/groups/GroupStudentsList.vue'
import GroupStudentsModal from '@/components/groups/GroupStudentsModal.vue'
import GroupFormModal from '@/components/groups/GroupFormModal.vue'
import { getEnumLabel, UserRole, WeekDayLabels } from '@/constants'
import GroupReportsList from '@/components/groups/GroupReportsList.vue'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const authStore = useAuthStore()
const groupId = computed(() => Number(route.params.id))

const loading = ref(true)
const error = ref<string | null>(null)
const group = ref<Group | null>(null)
const students = ref<any[]>([])
const activeTab = ref<'students' | 'reports'>('students')
const showManageStudents = ref(false)
const showEditModal = ref(false)

const studentsCount = computed(() => students.value.length)

// Check if user can edit group (only admins can edit)
const canEditGroup = computed(() => {
	return authStore.isSuperAdmin || authStore.isAdmin
})

async function fetchGroupDetails() {
	loading.value = true
	error.value = null

	try {
		const [groupResponse, studentsResponse] = await Promise.all([
			groupService.getGroup(groupId.value),
			groupService.getGroupStudents(groupId.value)
		])

		group.value = groupResponse.data
		students.value = studentsResponse.data || []
	} catch (err: any) {
		error.value = err.response?.data?.message || 'حدث خطأ في جلب بيانات المجموعة'
	} finally {
		loading.value = false
	}
}

function parseSchedule(schedule: any): ScheduleItem[] {
	if (!schedule) return []
	if (Array.isArray(schedule)) return schedule
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

function getDayName(day: string): string {
	if (!day) return ''
	const dayValue = typeof day === 'object' ? (day as any).value : day
	return getEnumLabel(WeekDayLabels, dayValue?.toLowerCase())
}

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

function openEditModal() {
	showEditModal.value = true
}

function closeEditModal() {
	showEditModal.value = false
}

function onGroupUpdated(updatedGroup: Group) {
	group.value = updatedGroup
	closeEditModal()
}

function onStudentsUpdated(updatedStudents: GroupStudentResponse[]) {
	students.value = updatedStudents
}

async function fetchStudents() {
	try {
		const response = await groupService.getGroupStudents(groupId.value)
		students.value = response.data || []
	} catch (err) {
		console.error('Failed to fetch students:', err)
	}
}

onMounted(() => {
	fetchGroupDetails()
})
</script>