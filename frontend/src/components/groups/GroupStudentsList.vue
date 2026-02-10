<template>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $t('groups.studentsList') }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $t('groups.studentsCount') }}: {{ students.length }}
                </p>
            </div>
            <button @click="$emit('manage-students')" class="btn-secondary w-full sm:w-auto">
                <i class="pi pi-cog mr-2"></i>
                <span class="hidden sm:inline">{{ $t('groups.manageStudents') }}</span>
                <span class="sm:hidden">إدارة</span>
            </button>
        </div>

        <!-- Search & Filters -->
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <div class="relative">
                    <i class="pi pi-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input 
                        v-model="searchQuery" 
                        type="text" 
                        :placeholder="$t('common.search')"
                        class="input w-full pr-10" 
                    />
                </div>
            </div>
        </div>

        <!-- Students List -->
        <div v-if="paginatedStudents.length > 0" class="space-y-3">
            <div 
                v-for="student in paginatedStudents" 
                :key="student.id"
                class="card p-3 sm:p-4 hover:shadow-md transition-shadow"
            >
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <!-- Student Info -->
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <!-- Avatar -->
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center flex-shrink-0"
                        >
                            <span class="text-primary-600 dark:text-primary-400 font-semibold text-base sm:text-lg">
                                {{ getStudentInitial(student) }}
                            </span>
                        </div>

                        <!-- Details -->
                        <div class="flex-1 min-w-0">
                            <button 
                                @click="viewStudentProfile(student)"
                                class="font-medium text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 hover:underline text-base sm:text-lg block text-right transition-colors truncate w-full"
                            >
                                {{ getStudentName(student) }}
                            </button>

                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                <!-- Join Date -->
                                <span class="flex items-center gap-1">
                                    <i class="pi pi-calendar text-xs"></i>
                                    <span class="hidden sm:inline">{{ formatDate(student.joined_at) }}</span>
                                    <span class="sm:hidden">{{ formatDateShort(student.joined_at) }}</span>
                                </span>

                                <!-- Mobile Badges -->
                                <span 
                                    v-if="student.memorizing_amount"
                                    class="sm:hidden px-2 py-0.5 bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 text-xs rounded-full font-medium"
                                >
                                    {{ student.memorizing_amount.for_view }}
                                </span>

                                <span 
                                    v-if="student.student_status" 
                                    :class="getStatusBadgeClass(student.student_status.value)"
                                    class="sm:hidden px-2 py-0.5 text-xs rounded-full font-medium"
                                >
                                    {{ student.student_status.for_view }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Badges & Actions -->
                    <div class="hidden sm:flex items-center gap-2 flex-shrink-0">
                        <!-- Memorizing Amount Badge -->
                        <span 
                            v-if="student.memorizing_amount"
                            class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 text-sm rounded-full font-medium"
                        >
                            {{ student.memorizing_amount.for_view }}
                        </span>

                        <!-- Student Status Badge -->
                        <span 
                            v-if="student.student_status" 
                            :class="getStatusBadgeClass(student.student_status.value)"
                            class="px-3 py-1 text-sm rounded-full font-medium"
                        >
                            {{ student.student_status.for_view }}
                        </span>

                        <!-- View Button -->
                        <button 
                            @click="viewStudentProfile(student)"
                            class="p-2 text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                            :title="$t('common.view')"
                        >
                            <i class="pi pi-eye text-sm"></i>
                        </button>
                    </div>

                    <!-- Mobile View Button -->
                    <div class="sm:hidden flex justify-end">
                        <button 
                            @click="viewStudentProfile(student)"
                            class="p-2 text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors"
                        >
                            <i class="pi pi-arrow-left text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-8 sm:py-12">
            <i class="pi pi-users text-3xl sm:text-4xl text-gray-300 dark:text-gray-600 mb-3 sm:mb-4 block"></i>
            <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400 mb-3">
                {{ searchQuery || statusFilter ? $t('common.searchNotFound') : $t('groups.noStudents') }}
            </p>
            <button 
                v-if="!searchQuery && !statusFilter" 
                @click="$emit('manage-students')" 
                class="btn-primary mt-2 sm:mt-4"
            >
                <i class="pi pi-plus mr-2"></i>
                {{ $t('students.addStudents') }}
            </button>
        </div>

        <!-- Pagination -->
        <div 
            v-if="totalPages > 1" 
            class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-gray-200 dark:border-gray-700"
        >
            <div class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t('common.showing') }} {{ startIndex + 1 }}-{{ endIndex }} {{ $t('common.of') }} {{ filteredStudents.length }}
            </div>
            <div class="flex items-center gap-2">
                <button
                    @click="previousPage"
                    :disabled="currentPage === 1"
                    class="btn-secondary p-2"
                    :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
                >
                    <i class="pi pi-chevron-right"></i>
                </button>
                <div class="flex items-center gap-1">
                    <button
                        v-for="page in visiblePages"
                        :key="page"
                        @click="goToPage(page)"
                        :class="[
                            'w-8 h-8 rounded-lg text-sm font-medium transition-colors',
                            page === currentPage
                                ? 'bg-primary-600 text-white'
                                : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
                        ]"
                    >
                        {{ page }}
                    </button>
                </div>
                <button
                    @click="nextPage"
                    :disabled="currentPage === totalPages"
                    class="btn-secondary p-2"
                    :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
                >
                    <i class="pi pi-chevron-left"></i>
                </button>
            </div>
        </div>

        <!-- Student Profile Modal -->
        <StudentProfileModal 
            v-if="showProfileModal && selectedStudent" 
            :group="group" 
            :student="selectedStudent"
            @close="closeStudentProfile" 
            @updated="$emit('refresh')" 
        />
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
const showProfileModal = ref(false)
const selectedStudent = ref<GroupStudentResponse | null>(null)
const currentPage = ref(1)
const itemsPerPage = ref(10)

// Computed
const filteredStudents = computed(() => {
    let result = props.students

    // Search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        result = result.filter(student => {
            const name = getStudentName(student).toLowerCase()
            return name.includes(query)
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

const totalPages = computed(() => Math.ceil(filteredStudents.value.length / itemsPerPage.value))

const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage.value, filteredStudents.value.length))

const paginatedStudents = computed(() => {
    return filteredStudents.value.slice(startIndex.value, endIndex.value)
})

const visiblePages = computed(() => {
    const pages: number[] = []
    const maxVisible = 5
    let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
    let end = Math.min(totalPages.value, start + maxVisible - 1)

    if (end - start + 1 < maxVisible) {
        start = Math.max(1, end - maxVisible + 1)
    }

    for (let i = start; i <= end; i++) {
        pages.push(i)
    }

    return pages
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
        return new Date(date).toLocaleDateString('ar-EG', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        })
    } catch {
        return '-'
    }
}

function formatDateShort(date: string | null | undefined): string {
    if (!date) return '-'
    try {
        return new Date(date).toLocaleDateString('ar-EG', { 
            year: '2-digit', 
            month: 'numeric', 
            day: 'numeric' 
        })
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

function goToPage(page: number): void {
    currentPage.value = page
}

function nextPage(): void {
    if (currentPage.value < totalPages.value) {
        currentPage.value++
    }
}

function previousPage(): void {
    if (currentPage.value > 1) {
        currentPage.value--
    }
}

// Reset to page 1 when filter changes
import { watch } from 'vue'
watch([searchQuery, statusFilter], () => {
    currentPage.value = 1
})
</script>