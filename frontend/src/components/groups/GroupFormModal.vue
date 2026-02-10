<template>
	<div class="fixed inset-0 z-50 overflow-y-auto">
		<div class="fixed inset-0 bg-black/50" @click="$emit('close')"></div>

		<div class="relative min-h-screen flex items-center justify-center p-4">
			<div
				class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-xl max-h-[90vh] overflow-y-auto">
				<!-- Header -->
				<div
					class="sticky top-0 bg-white dark:bg-gray-800 flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 z-10">
					<h2 class="text-lg font-bold text-gray-900 dark:text-white">
						{{ isEditing ? $t('groups.editGroup') : $t('groups.addGroup') }}
					</h2>
					<button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
						<i class="pi pi-times"></i>
					</button>
				</div>

				<!-- Form -->
				<form @submit.prevent="handleSubmit" class="p-4 space-y-4">
					<!-- Error Message -->
					<div v-if="error"
						class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-3 py-2 rounded-lg text-sm">
						{{ error }}
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

					<!-- Name -->
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
							{{ $t('groups.name') }} <span class="text-red-500">*</span>
						</label>
						<input v-model="form.name" type="text" class="input w-full"
							:class="{ 'border-red-500': validationErrors.name }" required />
						<p v-if="validationErrors.name" class="text-red-500 text-xs mt-1">
							{{ Array.isArray(validationErrors.name) ? validationErrors.name[0] : validationErrors.name
							}}
						</p>
					</div>

					<!-- Teacher -->
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
							{{ $t('groups.teacher') }} <span class="text-red-500">*</span>
						</label>
						<select v-model="form.teacher_id" class="input w-full"
							:class="{ 'border-red-500': validationErrors.teacher_id }" required>
							<option value="">{{ $t('groups.selectTeacher') }}</option>
							<option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
								{{ teacher.name }}
							</option>
						</select>
						<p v-if="validationErrors.teacher_id" class="text-red-500 text-xs mt-1">
							{{ Array.isArray(validationErrors.teacher_id) ? validationErrors.teacher_id[0] :
								validationErrors.teacher_id }}
						</p>
					</div>

					<!-- Schedule -->
					<div class="space-y-2">
						<div class="flex items-center justify-between">
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
								{{ $t('groups.schedule') }} <span class="text-red-500">*</span>
							</label>
							<button type="button" @click="addScheduleItem"
								class="text-primary-600 hover:text-primary-700 text-xs flex items-center gap-1">
								<i class="pi pi-plus text-xs"></i>
								{{ $t('groups.addScheduleItem') }}
							</button>
						</div>

						<p v-if="validationErrors.schedule" class="text-red-500 text-xs">
							{{ Array.isArray(validationErrors.schedule) ? validationErrors.schedule[0] :
								validationErrors.schedule }}
						</p>

						<div v-for="(item, index) in form.schedule" :key="index"
							class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
							<!-- Day -->
							<select v-model="item.day" class="input py-1.5 text-sm w-24" required>
								<option value="">{{ $t('groups.selectDay') }}</option>
								<option v-for="day in WeekDayOptions" :key="day.value" :value="day.value">
									{{ day.label }}
								</option>
							</select>

							<!-- Start Time -->
							<TimePicker v-model="item.start_time" />

							<!-- Arrow -->
							<span class="text-gray-400 text-sm">←</span>

							<!-- End Time -->
							<TimePicker v-model="item.end_time" />

							<!-- Delete Button -->
							<button type="button" @click="removeScheduleItem(index)"
								class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded mr-auto"
								:disabled="form.schedule.length === 1">
								<i class="pi pi-trash text-sm"></i>
							</button>
						</div>
					</div>

					<!-- Options -->
					<div class="grid grid-cols-2 gap-3">
						<label
							class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer">
							<input v-model="form.is_online" type="checkbox" class="w-4 h-4 rounded text-primary-600" />
							<span class="text-sm text-gray-700 dark:text-gray-300">{{ $t('groups.online') }}</span>
						</label>
						<label
							class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer">
							<input v-model="form.is_active" type="checkbox" class="w-4 h-4 rounded text-primary-600" />
							<span class="text-sm text-gray-700 dark:text-gray-300">{{ $t('common.active') }}</span>
						</label>
					</div>

					<!-- Actions -->
					<div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-200 dark:border-gray-700">
						<button type="button" @click="$emit('close')" class="btn-secondary py-1.5 px-3 text-sm">
							{{ $t('common.cancel') }}
						</button>
						<button type="submit" :disabled="loading"
							class="btn-primary py-1.5 px-3 text-sm flex items-center gap-1">
							<i v-if="loading" class="pi pi-spinner pi-spin text-sm"></i>
							{{ isEditing ? $t('common.save') : $t('common.add') }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
import { ref, reactive, watch, onMounted } from 'vue'
import type { Group, CreateGroupForm, UpdateGroupForm, ScheduleItem, WeekDay } from '@/types/models'
import userService from '@/services/userService'
import groupService from '@/services/groupService'
import { WeekDayOptions } from '@/constants'
import TimePicker from '@/components/common/TimePicker.vue'

interface ScheduleFormItem {
	day: string
	start_time: string
	end_time: string
}

interface Props {
	group?: Group | null
	isEditing?: boolean
}

const props = withDefaults(defineProps<Props>(), {
	group: null,
	isEditing: false
})

const emit = defineEmits<{
	close: []
	created: [group: Group]
	updated: [group: Group]
}>()

const loading = ref(false)
const error = ref<string | null>(null)
const validationErrors = ref<Record<string, string | string[]>>({})
const teachers = ref<{ id: number; name: string }[]>([])

const form = reactive<{
	name: string
	teacher_id: number | ''
	schedule: ScheduleFormItem[]
	is_online: boolean
	is_active: boolean
}>({
	name: '',
	teacher_id: '',
	schedule: [{ day: '', start_time: '', end_time: '' }],
	is_online: false,
	is_active: true
})

function clearErrors() {
	error.value = null
	validationErrors.value = {}
}

function parseScheduleFromApi(schedule: any): ScheduleFormItem[] {
	if (!schedule) return [{ day: '', start_time: '', end_time: '' }]

	let scheduleArray = schedule
	if (typeof schedule === 'string') {
		try {
			scheduleArray = JSON.parse(schedule)
		} catch {
			return [{ day: '', start_time: '', end_time: '' }]
		}
	}

	if (!Array.isArray(scheduleArray) || scheduleArray.length === 0) {
		return [{ day: '', start_time: '', end_time: '' }]
	}

	return scheduleArray.map((item: any) => {
		const day = typeof item.day === 'object' ? item.day?.value : item.day
		const startTime = typeof item.start_time === 'object' ? item.start_time?.value : item.start_time
		const endTime = typeof item.end_time === 'object' ? item.end_time?.value : item.end_time

		return {
			day: day || '',
			start_time: formatTimeForInput(startTime),
			end_time: formatTimeForInput(endTime)
		}
	})
}

function formatTimeForInput(time: string | undefined | null): string {
	if (!time) return ''
	if (/^\d{2}:\d{2}$/.test(time)) return time
	if (/^\d{2}:\d{2}:\d{2}$/.test(time)) return time.slice(0, 5)

	const match = time.match(/^(\d{1,2}):(\d{2})\s*(am|pm)?$/i)
	if (match) {
		let hours = parseInt(match[1] || '0')
		const minutes = match[2]
		const period = match[3]?.toLowerCase()

		if (period === 'pm' && hours < 12) hours += 12
		if (period === 'am' && hours === 12) hours = 0

		return `${hours.toString().padStart(2, '0')}:${minutes}`
	}

	return time
}

function getTeacherIdFromGroup(group: Group): number | '' {
	if (group.teacher_id) return group.teacher_id
	if (group.teacher && typeof group.teacher === 'object') {
		return group.teacher.id || ''
	}
	return ''
}

function addScheduleItem() {
	form.schedule.push({ day: '', start_time: '', end_time: '' })
}

function removeScheduleItem(index: number) {
	if (form.schedule.length > 1) {
		form.schedule.splice(index, 1)
	}
}

async function fetchTeachers() {
	try {
		const response = await userService.getUsers({ role: 'teacher', per_page: 100 })
		if (Array.isArray(response.data)) {
			teachers.value = response.data.map(u => ({ id: u.id, name: u.name }))
		}
	} catch (err) {
		console.error('Failed to fetch teachers:', err)
	}
}

watch(
	() => props.group,
	(group) => {
		if (group) {
			form.name = group.name || ''
			form.teacher_id = getTeacherIdFromGroup(group)
			form.schedule = parseScheduleFromApi(group.schedule)
			form.is_online = Boolean(group.is_online)
			form.is_active = Boolean(group.is_active)
		}
	},
	{ immediate: true }
)

async function handleSubmit() {
	loading.value = true
	clearErrors()

	try {
		const validSchedule: ScheduleItem[] = form.schedule.map((item) => ({
			day: item.day as WeekDay, // Type assertion to WeekDay
			start_time: item.start_time,
			end_time: item.end_time
		}))
		if (validSchedule.length === 0) {
			error.value = 'يجب إضافة موعد واحد على الأقل'
			loading.value = false
			return
		}

		// Validate and cast schedule items to proper WeekDay type


		const data: CreateGroupForm | UpdateGroupForm = {
			name: form.name,
			teacher_id: form.teacher_id as number,
			schedule: validSchedule,
			is_online: form.is_online,
			is_active: form.is_active
		}

		if (props.isEditing && props.group) {
			const response = await groupService.updateGroup(props.group.id, data)
			emit('updated', response.data)
		} else {
			const response = await groupService.createGroup(data as CreateGroupForm)
			emit('created', response.data)
		}

		emit('close')
	} catch (err: any) {
		console.error('Error saving group:', err)

		if (err.response?.status === 422) {
			const responseData = err.response.data
			if (responseData.errors) {
				validationErrors.value = responseData.errors
			} else if (responseData.message) {
				error.value = responseData.message
			}
		} else if (err.response?.data?.message) {
			error.value = err.response.data.message
		} else {
			error.value = 'حدث خطأ في حفظ المجموعة'
		}
	} finally {
		loading.value = false
	}
}

onMounted(() => {
	fetchTeachers()
})
</script>
