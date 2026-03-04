<template>
    <div class="fixed inset-0 z-50 overflow-y-auto" @click="$emit('close')">
        <div class="fixed inset-0 bg-black/50" style="z-index: 1;"></div>

        <div class="relative min-h-screen flex items-center justify-center p-4" style="z-index: 2;">
            <div @click.stop
                class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
                <!-- Header -->
                <div
                    class="sticky top-0 bg-white dark:bg-gray-800 flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 z-10">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $t('groups.students') }}
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ group.name }}</p>
                    </div>
                    <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="pi pi-times text-xl"></i>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-4 space-y-4">
                    <!-- Add Student Form -->
                    <div class="flex flex-wrap gap-2 items-center">
                        <select v-model="selectedStudentId" class="input flex-1 min-w-[150px] py-1.5 text-sm">
                            <option value="">{{ $t('groups.selectStudent') }}</option>
                            <option v-for="student in availableStudents" :key="student.id" :value="student.id">
                                {{ student.name }}
                            </option>
                        </select>
                        <select v-model="memorizingAmount" class="input w-24 py-1.5 text-sm">
                            <option v-for="option in MemorizingAmountOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                        <select v-model="studentStatus" class="input w-24 py-1.5 text-sm">
                            <option value="">{{ $t('groups.noStatus') }}</option>
                            <option v-for="option in StudentStatusOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                        <button @click="addStudent" :disabled="!selectedStudentId || addingStudent"
                            class="btn-primary py-1.5 px-3 text-sm flex items-center gap-1 disabled:opacity-50">
                            <i :class="addingStudent ? 'pi pi-spinner pi-spin' : 'pi pi-plus'" class="text-xs"></i>
                            {{ $t('common.add') }}
                        </button>
                    </div>

                    <!-- Error -->
                    <div v-if="error"
                        class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-3 py-2 rounded-lg text-sm">
                        {{ error }}
                    </div>

                    <!-- Loading -->
                    <div v-if="loadingStudents" class="flex justify-center py-8">
                        <i class="pi pi-spinner pi-spin text-3xl text-primary-600"></i>
                    </div>

                    <!-- Students List -->
                    <div v-else-if="students.length > 0" class="space-y-2">
                        <div v-for="item in students" :key="item.id"
                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg gap-3">
                            <!-- Student Info -->
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div
                                    class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center flex-shrink-0">
                                    <span class="text-primary-600 dark:text-primary-400 font-medium text-sm">
                                        {{ getStudentInitial(item) }}
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <button
                                        @click="openStudentProfile(item)"
                                        class="font-medium text-gray-900 dark:text-white text-sm truncate block text-right hover:text-primary-600 dark:hover:text-primary-400 hover:underline transition-colors"
                                    >
                                        {{ getStudentName(item) }}
                                    </button>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $t('groups.joinedAt') }}: {{ formatDate(item.joined_at) }}
                                    </div>
                                </div>
                            </div>

                            <!-- Editing Mode -->
                            <div v-if="editingStudentId === getStudentId(item)" class="flex items-center gap-2">
                                <select v-model="editForm.memorizing_amount" class="input py-1 text-xs w-24">
                                    <option v-for="option in MemorizingAmountOptions" :key="option.value"
                                        :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                                <select v-model="editForm.student_status" class="input py-1 text-xs w-24">
                                    <option value="">{{ $t('groups.noStatus') }}</option>
                                    <option v-for="option in StudentStatusOptions" :key="option.value"
                                        :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                                <button @click="saveEdit(item)" :disabled="savingEdit"
                                    class="p-1.5 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded">
                                    <i :class="savingEdit ? 'pi pi-spinner pi-spin' : 'pi pi-check'"
                                        class="text-sm"></i>
                                </button>
                                <button @click="cancelEdit"
                                    class="p-1.5 text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 rounded">
                                    <i class="pi pi-times text-sm"></i>
                                </button>
                            </div>

                            <!-- Display Mode -->
                            <div v-else class="flex items-center gap-2">
                                <!-- Memorizing Amount Badge -->
                                <span
                                    class="px-2 py-0.5 bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 text-xs rounded-full">
                                    {{ getMemorizingAmountLabel(item) }}
                                </span>
                                <!-- Student Status Badge -->
                                <span v-if="getStudentStatusValue(item)"
                                    :class="getStatusBadgeClass(getStudentStatusValue(item))"
                                    class="px-2 py-0.5 text-xs rounded-full">
                                    {{ getStudentStatusLabel(item) }}
                                </span>
                                <!-- View Profile Button -->
                                <button @click="openStudentProfile(item)"
                                    class="p-1.5 text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 rounded"
                                    :title="$t('common.view')">
                                    <i class="pi pi-eye text-sm"></i>
                                </button>
                                <!-- Edit Assignment Buttons -->
                                <button @click="startEdit(item)"
                                    class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded"
                                    :title="$t('common.edit')">
                                    <i class="pi pi-pencil text-sm"></i>
                                </button>
                                <!-- Remove Student Button -->
                                <button @click="confirmDelete(item)"
                                    :disabled="removingStudentId === getStudentId(item)"
                                    class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded disabled:opacity-50"
                                    :title="$t('common.delete')">
                                    <i :class="removingStudentId === getStudentId(item) ? 'pi pi-spinner pi-spin' : 'pi pi-trash'"
                                        class="text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="pi pi-users text-3xl mb-3 block"></i>
                        <p class="text-sm">{{ $t('groups.noStudents') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Student Profile Modal -->
        <StudentProfileModal v-if="showProfileModal && selectedStudent" :group="group" :student="selectedStudent"
            @close="closeStudentProfile" />
		
		<!-- Delete Confirmation Modal -->
		<ConfirmModal v-if="showDeleteModal && selectedStudent"
			:title="$t('groups.confirmRemoveStudent')"
			:message="$t('groups.removeStudentConfirmation', { name: selectedStudent?.student?.name || '' })"
			:confirm-text="$t('common.delete')" :cancel-text="$t('common.cancel')" variant="danger"
			@confirm="handleDelete"
			@cancel="showDeleteModal = false; selectedStudent = null"
		/>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import type { Group } from '@/types/models'
import groupService, { type GroupStudentResponse } from '@/services/groupService'
import StudentProfileModal from '@/components/groups/StudentProfileModal.vue'
import userService from '@/services/userService'
import {
    MemorizingAmount,
    MemorizingAmountLabels,
    MemorizingAmountOptions,
    StudentStatus,
    StudentStatusLabels,
    StudentStatusOptions,
    getEnumLabel
} from '@/constants'
import ConfirmModal from '../common/ConfirmModal.vue'

interface Props {
    group: Group
    students?: GroupStudentResponse[]
}

const props = defineProps<Props>()

const emit = defineEmits<{
    close: []
    updated: [students: GroupStudentResponse[]]
}>()

// Loading states
const loadingStudents = ref(!props.students)
const loadingAllStudents = ref(true)
const error = ref<string | null>(null)

// Data
const students = ref<GroupStudentResponse[]>(props.students ? [...props.students] : [])
const allStudents = ref<{ id: number; name: string }[]>([])

// Add student form
const selectedStudentId = ref<number | ''>('')
const memorizingAmount = ref(MemorizingAmount.ONE_QUARTER)
const studentStatus = ref('')
const addingStudent = ref(false)
const showDeleteModal = ref(false)

// Edit student
const editingStudentId = ref<number | null>(null)
const editForm = reactive({
    memorizing_amount: '',
    student_status: ''
})
const savingEdit = ref(false)

// Remove student
const removingStudentId = ref<number | null>(null)

// Student Profile Modal
const showProfileModal = ref(false)
const selectedStudent = ref<GroupStudentResponse | null>(null)

function openStudentProfile(item: GroupStudentResponse) {
    selectedStudent.value = item
    showProfileModal.value = true
}

function closeStudentProfile() {
    showProfileModal.value = false
    selectedStudent.value = null
}

// Helper functions to handle different API response structures
function getStudentId(item: GroupStudentResponse): number | undefined {
    return item.student?.id || (item as any).student_id
}

function getStudentName(item: GroupStudentResponse): string {
    return item.student?.name || (item as any).name || '—'
}

function getStudentInitial(item: GroupStudentResponse): string {
    const name = getStudentName(item)
    return name?.charAt(0) || '?'
}

function getMemorizingAmountLabel(item: GroupStudentResponse): string {
    if (item.memorizing_amount?.for_view) {
        return item.memorizing_amount.for_view
    }
    if (item.memorizing_amount?.value) {
        return getEnumLabel(MemorizingAmountLabels, item.memorizing_amount.value)
    }
    if (typeof item.memorizing_amount === 'string') {
        return getEnumLabel(MemorizingAmountLabels, item.memorizing_amount)
    }
    return '—'
}

function getStudentStatusValue(item: GroupStudentResponse): string | null {
    if (item.student_status?.value) {
        return item.student_status.value
    }
    if (typeof item.student_status === 'string') {
        return item.student_status
    }
    return null
}

function getStudentStatusLabel(item: GroupStudentResponse): string {
    if (item.student_status?.for_view) {
        return item.student_status.for_view
    }
    const value = getStudentStatusValue(item)
    if (value) {
        return getEnumLabel(StudentStatusLabels, value)
    }
    return ''
}

const availableStudents = computed(() => {
    const enrolledIds = students.value.map(s => getStudentId(s)).filter(Boolean)
    return allStudents.value.filter(s => !enrolledIds.includes(s.id))
})

function formatDate(date: string | undefined | null): string {
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

async function fetchStudents() {
    loadingStudents.value = true
    error.value = null

    try {
        const response = await groupService.getGroupStudents(props.group.id)
        students.value = Array.isArray(response.data) ? response.data : []
    } catch (err: any) {
        console.error('Error fetching students:', err)
        error.value = err.response?.data?.message || 'حدث خطأ في جلب الطلاب'
    } finally {
        loadingStudents.value = false
    }
}

async function fetchAllStudents() {
    loadingAllStudents.value = true

    try {
        const response = await userService.getUsers({ role: 'student', per_page: 100 })
        if (Array.isArray(response.data)) {
            allStudents.value = response.data.map(u => ({ id: u.id, name: u.name }))
        }
    } catch (err) {
        console.error('Failed to fetch students:', err)
    } finally {
        loadingAllStudents.value = false
    }
}

async function addStudent() {
    if (!selectedStudentId.value) return

    addingStudent.value = true
    error.value = null

    try {
        const payload: {
            student_id: number
            memorizing_amount: string
            student_status?: string
        } = {
            student_id: selectedStudentId.value as number,
            memorizing_amount: memorizingAmount.value
        }

        if (studentStatus.value) {
            payload.student_status = studentStatus.value
        }

        const response = await groupService.assignStudent(props.group.id, payload)

        // Use the response data directly
        if (Array.isArray(response.data)) {
            students.value = response.data
        }

        selectedStudentId.value = ''
        studentStatus.value = ''
        emit('updated', students.value)
    } catch (err: any) {
        error.value = err.response?.data?.message || 'حدث خطأ في إضافة الطالب'
    } finally {
        addingStudent.value = false
    }
}

function startEdit(item: GroupStudentResponse) {
    editingStudentId.value = getStudentId(item) || null

    if (item.memorizing_amount?.value) {
        editForm.memorizing_amount = item.memorizing_amount.value
    } else if (typeof item.memorizing_amount === 'string') {
        editForm.memorizing_amount = item.memorizing_amount
    } else {
        editForm.memorizing_amount = MemorizingAmount.ONE_QUARTER
    }

    editForm.student_status = getStudentStatusValue(item) || ''
}

function cancelEdit() {
    editingStudentId.value = null
    editForm.memorizing_amount = ''
    editForm.student_status = ''
}

async function saveEdit(item: GroupStudentResponse) {
    const studentId = getStudentId(item)
    if (!studentId) return

    savingEdit.value = true
    error.value = null

    try {
        const payload: {
            memorizing_amount?: string
            student_status?: string
        } = {
            memorizing_amount: editForm.memorizing_amount
        }

        if (editForm.student_status) {
            payload.student_status = editForm.student_status
        }

        const response = await groupService.updateStudent(props.group.id, studentId, payload)

        // Use the response data directly - API returns updated list
        if (Array.isArray(response.data)) {
            students.value = response.data
        }

        cancelEdit()
        emit('updated', students.value)
    } catch (err: any) {
        error.value = err.response?.data?.message || 'حدث خطأ في تحديث بيانات الطالب'
    } finally {
        savingEdit.value = false
    }
}

// Delete actions
function confirmDelete(student: GroupStudentResponse) {
	selectedStudent.value = student
	showDeleteModal.value = true
}

async function handleDelete() {
	const studentId = getStudentId(selectedStudent.value!)
	if (!studentId) return

	try {
		await groupService.removeStudent(props.group.id, studentId)

		students.value = students.value.filter(s => getStudentId(s) !== studentId)
        emit('updated', students.value)
		showDeleteModal.value = false
		selectedStudent.value = null
	} catch (err) {
		// Error handled in store
	}
}

onMounted(() => {
    if (!props.students) {
        fetchStudents()
    }
    fetchAllStudents()
})
</script>