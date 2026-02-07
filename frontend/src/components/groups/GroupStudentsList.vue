<template>
	<div class="space-y-4">
		<!-- Header -->
		<div class="flex items-center justify-between">
			<div>
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white">
					{{ $t('groups.studentsList') }}
				</h3>
				<p class="text-sm text-gray-500 dark:text-gray-400">
					{{ $t('groups.studentsCount') }}: {{ students.length }}
				</p>
			</div>
			<button @click="$emit('manage-students')" class="btn-secondary">
				<i class="pi pi-cog mr-2"></i>
				{{ $t('groups.manageStudents') }}
			</button>
		</div>

		<!-- Search & Filters -->
		<div class="flex flex-col sm:flex-row gap-3">
			<div class="flex-1">
				<div class="relative">
					<i class="pi pi-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
					<input v-model="searchQuery" type="text" :placeholder="$t('common.search')"
						class="input w-full pr-10" />
				</div>
			</div>
		</div>

		<!-- Students List -->
		<div v-if="filteredStudents.length > 0" class="space-y-2">
			<div v-for="student in filteredStudents" :key="student.id"
				class="card p-4 hover:shadow-md transition-shadow">
				<div class="flex items-center justify-between gap-4">
					<!-- Student Info -->
					<div class="flex items-center gap-4 flex-1 min-w-0">
						<!-- Avatar -->
						<div
							class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center flex-shrink-0">
							<span class="text-primary-600 dark:text-primary-400 font-semibold text-lg">
								{{ getStudentInitial(student) }}
							</span>
						</div>

						<!-- Details -->
						<div class="flex-1 min-w-0">
							<button @click="viewStudentProfile(student)"
								class="font-medium text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 hover:underline text-lg block text-right transition-colors">
								{{ getStudentName(student) }}
							</button>

							<div
								class="flex flex-wrap items-center gap-3 mt-1 text-sm text-gray-500 dark:text-gray-400">
								<!-- Join Date -->
								<span class="flex items-center gap-1">
									<i class="pi pi-calendar text-xs"></i>
									{{ formatDate(student.joined_at) }}
								</span>
							</div>
						</div>
					</div>

					<!-- Badges & Actions -->
					<div class="flex items-center gap-2 flex-shrink-0 sm:hidden md:flex">
						<!-- Memorizing Amount Badge -->
						<span v-if="student.memorizing_amount"
							class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 text-sm rounded-full font-medium sm:hidden md:inline-block">
							{{ student.memorizing_amount.for_view }}
						</span>

						<!-- Student Status Badge -->
						<span v-if="student.student_status" :class="getStatusBadgeClass(student.student_status.value)"
							class="px-3 py-1 text-sm rounded-full font-medium sm:hidden md:inline-block">
							{{ student.student_status.for_view }}
						</span>

						<!-- Actions -->
						<div class="flex items-center gap-1">
							<button @click="viewStudentProfile(student)"
								class="p-2 text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
								:title="$t('common.view')">
								<i class="pi pi-eye text-sm"></i>
							</button>
						</div>
					</div>
				</div>

			</div>
		</div>

		<!-- Empty State -->
		<div v-else class="text-center py-12">
			<i class="pi pi-users text-4xl text-gray-300 dark:text-gray-600 mb-4 block"></i>
			<p class="text-gray-500 dark:text-gray-400">
				{{ searchQuery || statusFilter ? $t('groups.noStudentsFound') : $t('groups.noStudentsYet') }}
			</p>
			<button v-if="!searchQuery && !statusFilter" @click="$emit('manage-students')" class="btn-primary mt-4">
				<i class="pi pi-plus mr-2"></i>
				{{ $t('groups.addStudents') }}
			</button>
		</div>

		<!-- Student Profile Modal -->
		<StudentProfileModal v-if="showProfileModal && selectedStudent" :group="group" :student="selectedStudent"
			@close="closeStudentProfile" @updated="$emit('refresh')" />
	</div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import type { Group } from '@/types/models'
import type { GroupStudentResponse } from '@/services/groupService'
import StudentProfileModal from './StudentProfileModal.vue'
import { StudentStatus } from '@/constants'

interface Props {
	group: Group
	students: GroupStudentResponse[]
}

const props = defineProps<Props>()

const emit = defineEmits<{
	refresh: []
	'manage-students': []
}>()

// State
const searchQuery = ref('')
const statusFilter = ref('')
const expandedStudents = ref(new Set<number>())
const showProfileModal = ref(false)
const selectedStudent = ref<GroupStudentResponse | null>(null)

// Computed
const filteredStudents = computed(() => {
	let result = props.students

	// Search filter
	if (searchQuery.value) {
		const query = searchQuery.value.toLowerCase()
		result = result.filter(student => {
			const name = getStudentName(student).toLowerCase()
			const phone = student.student?.phone || ''
			return name.includes(query) || phone.includes(query)
		})
	}

	// Status filter
	if (statusFilter.value) {
		result = result.filter(student =>
			student.student_status?.value === statusFilter.value
		)
	}

	return result
})

// Methods
function getStudentName(student: GroupStudentResponse): string {
	return student.student?.name || '-'
}

function getStudentInitial(student: GroupStudentResponse): string {
	const name = getStudentName(student)
	return name.charAt(0).toUpperCase()
}

function formatDate(date: string | null | undefined): string {
	if (!date) return '-'
	try {
		return new Date(date).toLocaleDateString('ar-EG')
	} catch {
		return '-'
	}
}

function getStatusBadgeClass(status: string | null | undefined): string {
	switch (status) {
		case StudentStatus.COMMITTED:
			return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
		case StudentStatus.ABSENT:
			return 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
		case StudentStatus.UNCOMMITTED:
			return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
		default:
			return 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
	}
}

function viewStudentProfile(student: GroupStudentResponse): void {
	selectedStudent.value = student
	showProfileModal.value = true
}

function closeStudentProfile(): void {
	showProfileModal.value = false
	selectedStudent.value = null
}
</script>