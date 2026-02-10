<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50" @click="emit('close')"></div>

    <!-- Modal -->
    <div class="relative min-h-screen flex items-center justify-center p-4">
      <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 bg-white dark:bg-gray-800 flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 z-10">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            {{ isEditing ? $t('reports.editReport') : $t('reports.addReport') }}
          </h2>
          <button @click="emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="pi pi-times text-xl"></i>
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
          <!-- Error Display -->
          <div v-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded-lg text-sm">
            {{ error }}
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Student Selection -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ $t('users.student') }} <span class="text-red-500">*</span>
              </label>
              <select 
                v-model="form.student_id" 
                class="input w-full" 
                :disabled="isEditing" 
                :class="{ 'opacity-60 cursor-not-allowed': isEditing }"
                required
				:selected="form.student_id"
              >
                <option value="">{{ $t('groups.selectStudent') }}</option>
                <option
                  v-for="student in students"
                  :key="getStudentId(student)"
                  :value="String(getStudentId(student))"
                >
                  {{ getStudentName(student) }}
                </option>
              </select>
            </div>

            <!-- Date -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ $t('reports.date') }} <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.date"
                type="date"
                class="input w-full"
                required
              />
            </div>

            <!-- Attendance Status -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ $t('reports.attendance') }} <span class="text-red-500">*</span>
              </label>
              <select v-model="form.attendance_status" class="input w-full" required>
                <option value="">{{ $t('common.select') }}</option>
                <option
                  v-for="option in AttendanceStatusOptions"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>
            </div>

            <!-- Memorized Amount -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ $t('reports.memorizedAmount') }}
              </label>
              <select v-model="form.memorized_amount" class="input w-full">
                <option value="">{{ $t('common.select') }}</option>
                <option
                  v-for="option in MemorizingAmountOptions"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>
            </div>

            <!-- Grade -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ $t('reports.grade') }}
              </label>
              <select v-model="form.grade" class="input w-full">
                <option value="">{{ $t('common.select') }}</option>
                <option
                  v-for="option in GradeOptions"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>
            </div>
          </div>

          <!-- Notes (Full Width) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ $t('reports.notes') }}
            </label>
            <textarea
              v-model="form.notes"
              rows="3"
              class="input w-full resize-none"
              :placeholder="$t('reports.notesPlaceholder')"
            ></textarea>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button type="button" @click="emit('close')" class="btn-secondary" :disabled="saving">
              {{ $t('common.cancel') }}
            </button>
            <button
              type="submit"
              class="btn-primary flex items-center gap-2"
              :disabled="saving || !isFormValid"
            >
              <i v-if="saving" class="pi pi-spinner pi-spin"></i>
              {{ isEditing ? $t('common.update') : $t('common.create') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import type { Group } from '@/types/models'
import type { GroupStudentResponse } from '@/services/groupService'
import reportService, { type Report } from '@/services/reportService'
import {
  AttendanceStatusOptions,
  GradeOptions,
  MemorizingAmountOptions
} from '@/constants'

interface Props {
  group: Group
  students: GroupStudentResponse[]
  report?: Report | null
  defaultDate?: string
}

const props = defineProps<Props>()

const emit = defineEmits<{
  close: []
  saved: []
}>()

// State
const saving = ref(false)
const error = ref<string | null>(null)

const form = reactive({
  student_id: '',
  date: '',
  attendance_status: '',
  memorized_amount: '',
  grade: '',
  notes: ''
})

// Computed
const isEditing = computed(() => !!props.report)
const isFormValid = computed(() => {
  return form.student_id && form.date && form.attendance_status
})

// Methods
function getStudentId(student: GroupStudentResponse): number {
  return student.student?.id || 0
}

function getStudentName(student: GroupStudentResponse): string {
  return student.student?.name || '-'
}

function formatDateForApi(dateStr: string): string {
  // Convert from yyyy-mm-dd to dd-mm-yyyy
  const [year, month, day] = dateStr.split('-')
  return `${day}-${month}-${year}`
}

function formatDateFromApi(dateStr: string): string {
  // Convert from dd-mm-yyyy to yyyy-mm-dd
  const [day, month, year] = dateStr.split('-')
  return `${year}-${month}-${day}`
}

function populateForm() {
  if (props.report) {
    form.student_id = String(props.report.student?.id)
    form.date = formatDateFromApi(props.report.date)
    form.attendance_status = props.report.attendance_status.value
    form.memorized_amount = props.report.memorized_amount?.value || ''
    form.grade = props.report.grade?.value || ''
    form.notes = props.report.notes || ''
  } else {
    form.student_id = ''
    form.date = props.defaultDate || new Date().toISOString().split('T')[0] || ''
    form.attendance_status = ''
    form.memorized_amount = ''
    form.grade = ''
    form.notes = ''
  }
}

async function handleSubmit() {
  if (!isFormValid.value) return

  saving.value = true
  error.value = null

  try {
    if (isEditing.value && props.report) {
      // ✅ When editing, don't send student_id
      const payload = {
        date: formatDateForApi(form.date),
        attendance_status: form.attendance_status,
        memorized_amount: form.memorized_amount || undefined,
        grade: form.grade || undefined,
        notes: form.notes || undefined
      }
      await reportService.updateReport(props.group.id, props.report.id, payload as any)
    } else {
      // ✅ When creating, include student_id
      const payload = {
        student_id: Number(form.student_id),
        date: formatDateForApi(form.date),
        attendance_status: form.attendance_status,
        memorized_amount: form.memorized_amount || undefined,
        grade: form.grade || undefined,
        notes: form.notes || undefined
      }
      await reportService.createReport(props.group.id, payload as any)
    }

    emit('saved')
  } catch (err: any) {
    console.error('Error saving report:', err)
    error.value = err.response?.data?.message || 'حدث خطأ في حفظ التقرير'
  } finally {
    saving.value = false
  }
}

watch(() => props.report, populateForm, { immediate: true })
</script>