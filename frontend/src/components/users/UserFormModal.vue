<template>
	<div class="fixed inset-0 z-50 overflow-y-auto">
		<!-- Backdrop -->
		<div class="fixed inset-0 bg-black/50" @click="$emit('close')"></div>

		<!-- Modal -->
		<div class="relative min-h-screen flex items-center justify-center p-4">
			<div
				class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
				<!-- Header -->
				<div
					class="sticky top-0 bg-white dark:bg-gray-800 flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 z-10">
					<h2 class="text-xl font-bold text-gray-900 dark:text-white">
						{{ isEditing ? $t('users.editUser') : $t('users.addUser') }}
					</h2>
					<button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
						<i class="pi pi-times text-xl"></i>
					</button>
				</div>

				<!-- Form -->
				<form @submit.prevent="handleSubmit" class="p-6 space-y-6">
					<!-- Error Message -->
					<div v-if="error"
						class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded-lg text-sm">
						<div class="flex items-start gap-2">
							<i class="pi pi-exclamation-circle mt-0.5 flex-shrink-0"></i>
							<span>{{ error }}</span>
						</div>
					</div>

					<!-- Validation Errors -->
					<div v-if="Object.keys(validationErrors).length > 0"
						class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-3 py-2 rounded-lg text-sm">
						<ul class="list-disc list-inside space-y-1">
							<li v-for="(messages, field) in validationErrors" :key="field">
								{{ Array.isArray(messages) ? messages[0] : messages }}
							</li>
						</ul>
					</div>

					<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
						<!-- Name -->
						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
								{{ $t('users.name') }} <span class="text-red-500">*</span>
							</label>
							<input v-model="form.name" type="text" class="input w-full" required />
						</div>

						<!-- Username -->
						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
								{{ $t('users.username') }} <span class="text-red-500">*</span>
							</label>
							<input v-model="form.username" type="text" class="input w-full" dir="ltr" required />
							<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
								{{ $t('users.usernameHint') }}
							</p>
						</div>

						<!-- Email -->
						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
								{{ $t('users.email') }} <span class="text-red-500">*</span>
							</label>
							<input v-model="form.email" type="email" class="input w-full" required />
						</div>

						<!-- Phone -->
						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
								{{ $t('users.phone') }}
							</label>
							<input v-model="form.phone" type="tel" class="input w-full" dir="ltr" />
						</div>

						<!-- Password -->
						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
								{{ $t('users.password') }}
								<span v-if="!isEditing" class="text-red-500">*</span>
								<span v-else class="text-gray-400 text-xs">({{ $t('users.leaveEmptyToKeep') }})</span>
							</label>
							<input v-model="form.password" type="password" class="input w-full"
								:required="!isEditing" />
						</div>

						<!-- Password Confirmation -->
						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
								{{ $t('users.passwordConfirmation') }}
								<span v-if="!isEditing" class="text-red-500">*</span>
							</label>
							<input v-model="form.password_confirmation" type="password" class="input w-full"
								:required="!isEditing" />
						</div>

						<!-- Role (only on create) -->
						<div v-if="!isEditing">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
								{{ $t('users.role') }} <span class="text-red-500">*</span>
							</label>
							<select v-model="form.role" class="input w-full" required>
								<option value="">{{ $t('users.selectRole') }}</option>
								<option value="admin">{{ $t('roles.admin') }}</option>
								<option value="teacher">{{ $t('roles.teacher') }}</option>
								<option value="student">{{ $t('roles.student') }}</option>
							</select>
						</div>

						<!-- Gender -->
						<div>
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
								{{ $t('users.gender') }} <span class="text-red-500">*</span>
							</label>
							<select v-model="form.gender" class="input w-full" required>
								<option value="male">{{ $t('users.male') }}</option>
								<option value="female">{{ $t('users.female') }}</option>
							</select>
						</div>
					</div>

					<!-- Teacher Specialization -->
					<div v-if="showTeacherFields">
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
							{{ $t('users.specialization') }} <span class="text-red-500">*</span>
						</label>
						<input v-model="form.teacher_specialization" type="text" class="input w-full"
							:required="showTeacherFields" />
					</div>

					<!-- Student Fields -->
					<div v-if="showStudentFields" class="space-y-6">
						<div class="border-t border-gray-200 dark:border-gray-700 pt-6">
							<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
								{{ $t('users.studentInfo') }}
							</h3>

							<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
								<div>
									<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
										{{ $t('users.educationalStage') }} <span class="text-red-500">*</span>
									</label>
									<select v-model="form.student_educational_stage" class="input w-full"
										:required="showStudentFields">
										<option value="">{{ $t('users.selectStage') }}</option>
										<option value="no_school">{{ $t('stages.noSchool') }}</option>
										<option value="primary_school">{{ $t('stages.primarySchool') }}</option>
										<option value="preparatory_school">{{ $t('stages.preparatorySchool') }}</option>
										<option value="secondary_school">{{ $t('stages.secondarySchool') }}</option>
										<option value="university_stage">{{ $t('stages.universityStage') }}</option>
										<option value="graduate">{{ $t('stages.graduate') }}</option>
									</select>
								</div>

								<div>
									<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
										{{ $t('users.beginMemorizingAt') }}
									</label>
									<input v-model="form.student_begin_memorizing_at" type="date"
										class="input w-full" />
								</div>

								<div>
									<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
										{{ $t('users.memorizingCompletedAt') }}
									</label>
									<input v-model="form.student_memorizing_completed_at" type="date"
										class="input w-full" />
								</div>
							</div>
						</div>

						<!-- Tajweed Section -->
						<div class="border-t border-gray-200 dark:border-gray-700 pt-6">
							<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
								{{ $t('users.tajweedInfo') }}
							</h3>

							<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
								<div>
									<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
										{{ $t('users.recitationLevel') }}
									</label>
									<select v-model="form.student_tajweed_recitation_level" class="input w-full">
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
									<select v-model="form.student_tajweed_learning_status" class="input w-full">
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
									<textarea v-model="form.student_tajweed_notes" class="input w-full"
										rows="3"></textarea>
								</div>
							</div>
						</div>
					</div>

					<!-- Actions -->
					<div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
						<button type="button" @click="$emit('close')" class="btn-secondary">
							{{ $t('common.cancel') }}
						</button>
						<button type="submit" :disabled="loading" class="btn-primary flex items-center gap-2">
							<i v-if="loading" class="pi pi-spinner pi-spin"></i>
							{{ isEditing ? $t('common.save') : $t('common.add') }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
import { ref, reactive, watch, computed } from 'vue'
import type { User, CreateUserForm, UpdateUserForm, UserRole } from '@/types/models'
import { GradeOptions, LearningStatusOptions } from '@/constants'

interface Props {
	user?: User | null
	isEditing?: boolean
}

const props = withDefaults(defineProps<Props>(), {
	user: null,
	isEditing: false
})

const emit = defineEmits<{
	close: []
	save: [data: CreateUserForm | UpdateUserForm]
}>()

const loading = ref(false)
const error = ref<string | null>(null)
const validationErrors = ref<Record<string, string | string[]>>({})

const form = reactive<CreateUserForm>({
	name: '',
	username: '',
	email: '',
	phone: '',
	password: '',
	password_confirmation: '',
	role: '' as Exclude<UserRole, 'super_admin'>,
	gender: 'male',
	teacher_specialization: '',
	student_educational_stage: '',
	student_begin_memorizing_at: '',
	student_memorizing_completed_at: '',
	student_tajweed_recitation_level: '',
	student_tajweed_learning_status: '',
	student_tajweed_notes: ''
})

function clearErrors() {
	error.value = null
	validationErrors.value = {}
}

// Computed to show role-specific fields
const currentRole = computed(() => props.isEditing ? props.user?.role.value : form.role)
const showTeacherFields = computed(() => currentRole.value === 'teacher')
const showStudentFields = computed(() => currentRole.value === 'student')

// Populate form when editing
watch(
	() => props.user,
	(user) => {
		if (user) {
			form.name = user.name
			form.username = user.username
			form.email = user.email
			form.phone = user.phone || ''
			form.gender = user.gender.value
			form.role = user.role.value as Exclude<UserRole, 'super_admin'>

			// Clear passwords for editing
			form.password = ''
			form.password_confirmation = ''

			// Populate profile data
			if (user.teacher) {
				form.teacher_specialization = user.teacher.specialization || ''
			}

			if (user.student) {
				form.student_educational_stage = user.student.educational_stage?.value || ''
				form.student_begin_memorizing_at = user.student.begin_memorizing_at || ''
				form.student_memorizing_completed_at = user.student.memorizing_completed_at || ''
				form.student_tajweed_recitation_level = user.student.tajweed?.recitation_level?.value || ''
				form.student_tajweed_learning_status = user.student.tajweed?.learning_status?.value || ''
				form.student_tajweed_notes = user.student.tajweed?.notes || ''
			}
		}
	},
	{ immediate: true }
)

async function handleSubmit() {
	loading.value = true
	clearErrors()

	try {
		// Validate passwords match
		if (form.password && form.password !== form.password_confirmation) {
			error.value = 'كلمة المرور غير متطابقة'
			loading.value = false
			return
		}

		// Build data object
		const data: CreateUserForm | UpdateUserForm = props.isEditing
			? buildUpdateData()
			: buildCreateData()

		emit('save', data)
	} catch (err: any) {
		error.value = 'حدث خطأ في حفظ المستخدم'
		loading.value = false
	}
}

/**
 * Called by the parent component to set API errors inside the modal.
 */
function setApiErrors(err: any) {
	if (err.response?.status === 422) {
		const responseData = err.response.data
		if (responseData.errors) {
			validationErrors.value = responseData.errors
		}
		if (responseData.message) {
			error.value = responseData.message
		}
	} else if (err.response?.data?.message) {
		error.value = err.response.data.message
	} else {
		error.value = err.message || 'حدث خطأ في حفظ المستخدم'
	}
	loading.value = false
}

/**
 * Called by the parent on success to stop the loading spinner.
 */
function setSuccess() {
	loading.value = false
}

defineExpose({ setApiErrors, setSuccess })

function buildCreateData(): CreateUserForm {
	const data: CreateUserForm = {
		name: form.name,
		username: form.username,
		email: form.email,
		password: form.password,
		password_confirmation: form.password_confirmation,
		role: form.role,
		gender: form.gender
	}

	if (form.phone) {
		data.phone = form.phone
	}

	if (form.role === 'teacher' && form.teacher_specialization) {
		data.teacher_specialization = form.teacher_specialization
	}

	if (form.role === 'student') {
		if (form.student_educational_stage) {
			data.student_educational_stage = form.student_educational_stage
		}
		if (form.student_begin_memorizing_at) {
			data.student_begin_memorizing_at = form.student_begin_memorizing_at
		}
		if (form.student_memorizing_completed_at) {
			data.student_memorizing_completed_at = form.student_memorizing_completed_at
		}
		if (form.student_tajweed_recitation_level) {
			data.student_tajweed_recitation_level = form.student_tajweed_recitation_level
		}
		if (form.student_tajweed_learning_status) {
			data.student_tajweed_learning_status = form.student_tajweed_learning_status
		}
		if (form.student_tajweed_notes) {
			data.student_tajweed_notes = form.student_tajweed_notes
		}
	}

	return data
}

function buildUpdateData(): UpdateUserForm {
	const data: UpdateUserForm = {
		name: form.name,
		username: form.username,
		email: form.email,
		gender: form.gender
	}

	if (form.phone) {
		data.phone = form.phone
	}

	// Only include password if provided
	if (form.password) {
		data.password = form.password
		data.password_confirmation = form.password_confirmation
	}

	// Include profile data based on role
	if (props.user?.role.value === 'teacher') {
		data.teacher_specialization = form.teacher_specialization
	}

	if (props.user?.role.value === 'student') {
		data.student_educational_stage = form.student_educational_stage
		data.student_begin_memorizing_at = form.student_begin_memorizing_at || undefined
		data.student_memorizing_completed_at = form.student_memorizing_completed_at || undefined
		data.student_tajweed_recitation_level = form.student_tajweed_recitation_level || undefined
		data.student_tajweed_learning_status = form.student_tajweed_learning_status || undefined
		data.student_tajweed_notes = form.student_tajweed_notes || undefined
	}

	return data
}
</script>