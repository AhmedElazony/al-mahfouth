<template>
	<div class="fixed inset-0 z-[60] overflow-y-auto" @click="$emit('close')"> <!-- Backdrop -->
		<div class="fixed inset-0 bg-black/50" style="z-index: 1;"></div>

		<div class="relative min-h-screen flex items-center justify-center p-4" style="z-index: 2;">
			<div @click.stop
				class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
				<!-- Header -->
				<div
					class="sticky top-0 bg-white dark:bg-gray-800 flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 z-10">
					<div>
						<h2 class="text-lg font-bold text-gray-900 dark:text-white">
							{{ isEditing ? $t('students.editStudent') : $t('students.studentDetails') }}
						</h2>
						<p class="text-sm text-gray-500 dark:text-gray-400">{{ studentName }}</p>
					</div>
					<button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
						<i class="pi pi-times text-xl"></i>
					</button>
				</div>

				<!-- Loading -->
				<div v-if="loading" class="flex justify-center py-12">
					<i class="pi pi-spinner pi-spin text-3xl text-primary-600"></i>
				</div>

				<!-- Content -->
				<div v-else class="p-4 space-y-6">
					<!-- Error -->
					<div v-if="error"
						class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-3 py-2 rounded-lg text-sm">
						{{ error }}
					</div>

					<!-- View Mode -->
					<div v-if="!isEditing && studentData">
						<!-- Basic Info -->
						<div class="space-y-4">
							<h3
								class="text-md font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
								{{ $t('users.studentInfo') }}
							</h3>

							<div class="grid grid-cols-2 gap-4">
								<div>
									<label class="text-sm text-gray-500 dark:text-gray-400">{{
										$t('users.educationalStage') }}</label>
									<p class="text-gray-900 dark:text-white">{{ studentData.educational_stage?.for_view
										|| '-' }}</p>
								</div>
								<div>
									<label class="text-sm text-gray-500 dark:text-gray-400">{{
										$t('users.beginMemorizingAt') }}</label>
									<p class="text-gray-900 dark:text-white">{{ studentData.begin_memorizing_at || '-'
									}}</p>
								</div>
								<div>
									<label class="text-sm text-gray-500 dark:text-gray-400">{{
										$t('users.memorizingCompletedAt') }}</label>
									<p class="text-gray-900 dark:text-white">{{ studentData.memorizing_completed_at ||
										'-' }}</p>
								</div>
							</div>
						</div>

						<!-- Tajweed Info -->
						<div class="space-y-4 mt-6">
							<h3
								class="text-md font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
								{{ $t('users.tajweedInfo') }}
							</h3>

							<div class="grid grid-cols-2 gap-4">
								<div>
									<label class="text-sm text-gray-500 dark:text-gray-400">{{
										$t('users.recitationLevel') }}</label>
									<p class="text-gray-900 dark:text-white">{{
										studentData.tajweed?.recitation_level?.for_view || '-' }}</p>
								</div>
								<div>
									<label class="text-sm text-gray-500 dark:text-gray-400">{{
										$t('users.tajweedLearningStatus') }}</label>
									<p class="text-gray-900 dark:text-white">{{
										studentData.tajweed?.learning_status?.for_view || '-' }}</p>
								</div>
								<div class="col-span-2" v-if="studentData.tajweed?.notes">
									<label class="text-sm text-gray-500 dark:text-gray-400">{{ $t('users.tajweedNotes')
									}}</label>
									<p class="text-gray-900 dark:text-white">{{ studentData.tajweed.notes }}</p>
								</div>
							</div>
						</div>

						<!-- Group Assignment Info -->
						<div class="space-y-4 mt-6" v-if="groupAssignment">
							<h3
								class="text-md font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
								{{ $t('groups.groupStudentInfo') }}
							</h3>

							<div class="grid grid-cols-2 gap-4">
								<div>
									<label class="text-sm text-gray-500 dark:text-gray-400">{{
										$t('students.memorization.amount') }}</label>
									<p class="text-gray-900 dark:text-white">{{
										groupAssignment.memorizing_amount?.for_view || '-' }}</p>
								</div>
								<div>
									<label class="text-sm text-gray-500 dark:text-gray-400">{{ $t('students.status')
									}}</label>
									<p class="text-gray-900 dark:text-white">{{ groupAssignment.student_status?.for_view
										|| '-' }}</p>
								</div>
								<div>
									<label class="text-sm text-gray-500 dark:text-gray-400">{{ $t('groups.joinedAt')
									}}</label>
									<p class="text-gray-900 dark:text-white">{{ formatDate(groupAssignment.joined_at) }}
									</p>
								</div>
							</div>
						</div>
					</div>

					<!-- Edit Mode -->
					<form v-if="isEditing" @submit.prevent="handleSave" class="space-y-6">
						<!-- Student Info Section -->
						<div>
							<h3
								class="text-md font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">
								{{ $t('users.studentInfo') }}
							</h3>

							<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
								<div>
									<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
										{{ $t('users.educationalStage') }}
									</label>
									<select v-model="form.educational_stage" class="input w-full">
										<option value="">{{ $t('common.select') }}</option>
										<option v-for="stage in EducationalStageOptions" :key="stage.value"
											:value="stage.value">
											{{ stage.label }}
										</option>
									</select>
								</div>

								<div>
									<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
										{{ $t('users.beginMemorizingAt') }}
									</label>
									<input v-model="form.begin_memorizing_at" type="date" class="input w-full" />
								</div>

								<div>
									<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
										{{ $t('users.memorizingCompletedAt') }}
									</label>
									<input v-model="form.memorizing_completed_at" type="date" class="input w-full" />
								</div>
							</div>
						</div>

						<!-- Tajweed Section -->
						<div>
							<h3
								class="text-md font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">
								{{ $t('users.tajweedInfo') }}
							</h3>

							<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
								<div>
									<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
										{{ $t('users.recitationLevel') }}
									</label>
									<select v-model="form.tajweed_recitation_level" class="input w-full">
										<option value="">{{ $t('common.select') }}</option>
										<option v-for="grade in GradeOptions" :key="grade.value" :value="grade.value">
											{{ grade.label }}
										</option>
									</select>
								</div>

								<div>
									<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
										{{ $t('users.tajweedLearningStatus') }}
									</label>
									<select v-model="form.tajweed_learning_status" class="input w-full">
										<option value="">{{ $t('common.select') }}</option>
										<option v-for="status in LearningStatusOptions" :key="status.value"
											:value="status.value">
											{{ status.label }}
										</option>
									</select>
								</div>

								<div class="md:col-span-2">
									<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
										{{ $t('users.tajweedNotes') }}
									</label>
									<textarea v-model="form.tajweed_notes" class="input w-full" rows="3"
										maxlength="1000"></textarea>
									<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
										{{ form.tajweed_notes?.length || 0 }}/1000
									</p>
								</div>
							</div>
						</div>
					</form>
				</div>

				<!-- Footer -->
				<div
					class="sticky bottom-0 bg-white dark:bg-gray-800 flex items-center justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
					<template v-if="!isEditing">
						<button @click="$emit('close')" class="btn-secondary">
							{{ $t('common.close') }}
						</button>
						<button @click="startEditing" class="btn-primary flex items-center gap-2">
							<i class="pi pi-pencil text-sm"></i>
							{{ $t('common.edit') }}
						</button>
					</template>
					<template v-else>
						<button @click="cancelEditing" class="btn-secondary" :disabled="saving">
							{{ $t('common.cancel') }}
						</button>
						<button @click="handleSave" class="btn-primary flex items-center gap-2" :disabled="saving">
							<i :class="saving ? 'pi pi-spinner pi-spin' : 'pi pi-check'" class="text-sm"></i>
							{{ $t('common.save') }}
						</button>
					</template>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import type { Group, StudentProfile } from '@/types/models'
import type { GroupStudentResponse, UpdateStudentProfilePayload } from '@/services/groupService'
import groupService from '@/services/groupService'
import { GradeOptions, LearningStatusOptions, EducationalStageOptions } from '@/constants'

interface Props {
	group: Group
	student: GroupStudentResponse
}

const props = defineProps<Props>()

const emit = defineEmits<{
	close: []
	updated: []
}>()

// State
const loading = ref(true)
const saving = ref(false)
const error = ref<string | null>(null)
const isEditing = ref(false)
const studentData = ref<StudentProfile | null>(null)

// Form - includes all fields from UpdateStudentProfileRequest
const form = reactive({
	educational_stage: '',
	begin_memorizing_at: '',
	memorizing_completed_at: '',
	tajweed_recitation_level: '',
	tajweed_learning_status: '',
	tajweed_notes: ''
})

// Computed
const studentId = computed(() => props.student.student?.id || (props.student as any).student_id)
const studentName = computed(() => props.student.student?.name || (props.student as any).name || '-')
const groupAssignment = computed(() => props.student)

// Methods
function formatDate(date: string | undefined | null): string {
	if (!date) return '-'
	try {
		return new Date(date).toLocaleDateString('ar-EG')
	} catch {
		return '-'
	}
}

async function fetchStudentProfile() {
	loading.value = true
	error.value = null

	try {
		const response = await groupService.getGroupStudent(props.group.id, studentId.value)
		studentData.value = response.data
	} catch (err: any) {
		console.error('Error fetching student profile:', err)
		error.value = err.response?.data?.message || 'حدث خطأ في جلب بيانات الطالب'
	} finally {
		loading.value = false
	}
}

function startEditing() {
	isEditing.value = true
	// Populate form with all current data
	form.educational_stage = studentData.value?.educational_stage?.value || ''
	form.begin_memorizing_at = studentData.value?.begin_memorizing_at || ''
	form.memorizing_completed_at = studentData.value?.memorizing_completed_at || ''
	form.tajweed_recitation_level = studentData.value?.tajweed?.recitation_level?.value || ''
	form.tajweed_learning_status = studentData.value?.tajweed?.learning_status?.value || ''
	form.tajweed_notes = studentData.value?.tajweed?.notes || ''
}

function cancelEditing() {
	isEditing.value = false
	error.value = null
}

async function handleSave() {
	saving.value = true
	error.value = null

	try {
		// Build payload with all fields (only send non-empty values)
		const payload: UpdateStudentProfilePayload = {
			student_id: studentId.value,
		}

		if (form.educational_stage) {
			payload.educational_stage = form.educational_stage
		}
		if (form.begin_memorizing_at) {
			payload.begin_memorizing_at = form.begin_memorizing_at
		}
		if (form.memorizing_completed_at) {
			payload.memorizing_completed_at = form.memorizing_completed_at
		}
		if (form.tajweed_recitation_level) {
			payload.tajweed_recitation_level = form.tajweed_recitation_level
		}
		if (form.tajweed_learning_status) {
			payload.tajweed_learning_status = form.tajweed_learning_status
		}
		if (form.tajweed_notes !== undefined && form.tajweed_notes !== '') {
			payload.tajweed_notes = form.tajweed_notes
		}

		const response = await groupService.updateStudentProfile(props.group.id, payload)

		// Update studentData with the response data
		if (response.data) {
			studentData.value = response.data
		}

		isEditing.value = false
		emit('updated')
	} catch (err: any) {
		error.value = err.response?.data?.message || 'حدث خطأ في تحديث بيانات الطالب'
	} finally {
		saving.value = false
	}
}

onMounted(() => {
	fetchStudentProfile()
})
</script>
