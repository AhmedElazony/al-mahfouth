<template>
	<div class="fixed inset-0 z-50 overflow-y-auto">
		<!-- Backdrop -->
		<div class="fixed inset-0 bg-black/50" @click="$emit('close')"></div>

		<!-- Modal -->
		<div class="relative min-h-screen flex items-center justify-center p-4">
			<div
				class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
				<!-- Header -->
				<div
					class="flex-shrink-0 bg-white dark:bg-gray-800 flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
					<h2 class="text-xl font-bold text-gray-900 dark:text-white">
						{{ isEditing ? $t('users.editUser') : $t('users.addUser') }}
					</h2>
					<button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
						<i class="pi pi-times text-xl"></i>
					</button>
				</div>

				<!-- Form - Scrollable Content -->
				<div class="flex-1 overflow-y-auto">
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
							class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
							<p class="text-red-600 dark:text-red-400 text-sm font-medium mb-2 flex items-center gap-2">
								<i class="pi pi-exclamation-triangle"></i>
								يرجى تصحيح الأخطاء التالية:
							</p>
							<ul class="space-y-1 text-sm text-red-600 dark:text-red-400 list-disc list-inside">
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
								<input v-model="form.name" type="text" class="input w-full"
									:class="{ 'border-red-500': validationErrors.name }" required />
								<p v-if="validationErrors.name" class="mt-1 text-xs text-red-500">
									{{ Array.isArray(validationErrors.name) ? validationErrors.name[0] :
										validationErrors.name }}
								</p>
							</div>

							<!-- Username -->
							<div>
								<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
									{{ $t('users.username') }} <span class="text-red-500">*</span>
								</label>
								<input v-model="form.username" type="text" class="input w-full" dir="ltr"
									:class="{ 'border-red-500': validationErrors.username }" required />
								<p v-if="validationErrors.username" class="mt-1 text-xs text-red-500">
									{{ Array.isArray(validationErrors.username) ? validationErrors.username[0] :
										validationErrors.username }}
								</p>
								<p v-else class="mt-1 text-xs text-gray-500 dark:text-gray-400">
									{{ $t('users.usernameHint') }}
								</p>
							</div>

							<!-- Email -->
							<div>
								<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
									{{ $t('users.email') }} <span class="text-red-500">*</span>
								</label>
								<input v-model="form.email" type="email" class="input w-full"
									:class="{ 'border-red-500': validationErrors.email }" required />
								<p v-if="validationErrors.email" class="mt-1 text-xs text-red-500">
									{{ Array.isArray(validationErrors.email) ? validationErrors.email[0] :
										validationErrors.email }}
								</p>
							</div>

							<!-- Phone -->
							<div>
								<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
									{{ $t('users.phone') }}
								</label>
								<input v-model="form.phone" type="tel" class="input w-full" dir="ltr"
									:class="{ 'border-red-500': validationErrors.phone }" />
								<p v-if="validationErrors.phone" class="mt-1 text-xs text-red-500">
									{{ Array.isArray(validationErrors.phone) ? validationErrors.phone[0] :
										validationErrors.phone }}
								</p>
							</div>

							<!-- Password -->
							<div>
								<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
									{{ $t('users.password') }}
									<span v-if="!isEditing" class="text-red-500">*</span>
									<span v-else class="text-gray-400 text-xs">({{ $t('users.leaveEmptyToKeep')
										}})</span>
								</label>
								<input v-model="form.password" type="password" class="input w-full"
									:class="{ 'border-red-500': validationErrors.password }" :required="!isEditing" />
								<p v-if="validationErrors.password" class="mt-1 text-xs text-red-500">
									{{ Array.isArray(validationErrors.password) ? validationErrors.password[0] :
										validationErrors.password }}
								</p>
							</div>

							<!-- Password Confirmation -->
							<div>
								<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
									{{ $t('users.passwordConfirmation') }}
									<span v-if="!isEditing" class="text-red-500">*</span>
								</label>
								<input v-model="form.password_confirmation" type="password" class="input w-full"
									:class="{ 'border-red-500': validationErrors.password_confirmation }"
									:required="!isEditing" />
							</div>

							<!-- Role (only on create) -->
							<div v-if="!isEditing">
								<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
									{{ $t('users.role') }} <span class="text-red-500">*</span>
								</label>
								<select v-model="form.role" class="input w-full"
									:class="{ 'border-red-500': validationErrors.role }" required>
									<option value="">{{ $t('users.selectRole') }}</option>
									<option value="admin">{{ $t('roles.admin') }}</option>
									<option value="teacher">{{ $t('roles.teacher') }}</option>
									<option value="student">{{ $t('roles.student') }}</option>
								</select>
								<p v-if="validationErrors.role" class="mt-1 text-xs text-red-500">
									{{ Array.isArray(validationErrors.role) ? validationErrors.role[0] :
										validationErrors.role }}
								</p>
							</div>

							<!-- Gender -->
							<div>
								<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
									{{ $t('users.gender') }} <span class="text-red-500">*</span>
								</label>
								<select v-model="form.gender" class="input w-full"
									:class="{ 'border-red-500': validationErrors.gender }" required>
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
								:class="{ 'border-red-500': validationErrors.teacher_specialization }"
								:required="showTeacherFields" />
							<p v-if="validationErrors.teacher_specialization" class="mt-1 text-xs text-red-500">
								{{ Array.isArray(validationErrors.teacher_specialization) ?
									validationErrors.teacher_specialization[0] : validationErrors.teacher_specialization }}
							</p>
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
											:class="{ 'border-red-500': validationErrors.student_educational_stage }"
											:required="showStudentFields">
											<option value="">{{ $t('users.selectStage') }}</option>
											<option value="no_school">{{ $t('stages.noSchool') }}</option>
											<option value="primary_school">{{ $t('stages.primarySchool') }}</option>
											<option value="preparatory_school">{{ $t('stages.preparatorySchool') }}
											</option>
											<option value="secondary_school">{{ $t('stages.secondarySchool') }}</option>
											<option value="university_stage">{{ $t('stages.universityStage') }}</option>
											<option value="graduate">{{ $t('stages.graduate') }}</option>
										</select>
										<p v-if="validationErrors.student_educational_stage"
											class="mt-1 text-xs text-red-500">
											{{ Array.isArray(validationErrors.student_educational_stage) ?
												validationErrors.student_educational_stage[0] :
												validationErrors.student_educational_stage }}
										</p>
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
											<option v-for="grade in GradeOptions" :key="grade.value"
												:value="grade.value">
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
					</form>
				</div>

				<!-- Actions - Fixed at bottom -->
				<div
					class="flex-shrink-0 bg-white dark:bg-gray-800 flex items-center justify-start gap-4 p-6 border-t border-gray-200 dark:border-gray-700">
					<button type="submit" :disabled="loading" class="btn-primary flex items-center gap-2"
						@click="handleSubmit">
						<i v-if="loading" class="pi pi-spinner pi-spin"></i>
						{{ isEditing ? $t('common.save') : $t('common.add') }}
					</button>
					<button type="button" @click="$emit('close')" class="btn-secondary">
						{{ $t('common.cancel') }}
					</button>
				</div>
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
	apiError?: string | null
	apiValidationErrors?: Record<string, string[]>
}

const props = withDefaults(defineProps<Props>(), {
	user: null,
	isEditing: false,
	apiError: null,
	apiValidationErrors: () => ({})
})

const emit = defineEmits<{
	close: []
	save: [data: CreateUserForm | UpdateUserForm]
}>()

const loading = ref(false)
const error = ref<string | null>(null)
const validationErrors = ref<Record<string, string | string[]>>({})

// Watch for API errors passed from parent
watch(
	() => props.apiError,
	(newError) => {
		if (newError) {
			error.value = newError
			loading.value = false
		}
	}
)

watch(
	() => props.apiValidationErrors,
	(newErrors) => {
		if (newErrors && Object.keys(newErrors).length > 0) {
			validationErrors.value = newErrors
			loading.value = false
		}
	}
)

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

// Clear errors when form changes
watch(form, () => {
	error.value = null
	validationErrors.value = {}
}, { deep: true })

async function handleSubmit() {
	loading.value = true
	error.value = null
	validationErrors.value = {}

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
		error.value = err.message || 'حدث خطأ'
		loading.value = false
	}
}

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

// Expose method to set errors from parent
function setErrors(errorMsg: string | null, errors: Record<string, string[]> = {}) {
	error.value = errorMsg
	validationErrors.value = errors
	loading.value = false
}

defineExpose({ setErrors })
</script>