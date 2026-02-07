<template>
    <div class="space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <router-link to="/groups" class="hover:text-primary-600">
                {{ $t('groups.title') }}
            </router-link>
            <i class="pi pi-chevron-left text-xs"></i>
            <span class="text-gray-900 dark:text-white">{{ group?.name }}</span>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center py-12">
            <i class="pi pi-spinner pi-spin text-3xl text-primary-600"></i>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="card p-6">
            <div class="text-center text-red-600 dark:text-red-400">
                <i class="pi pi-exclamation-circle text-4xl mb-4"></i>
                <p>{{ error }}</p>
                <button @click="fetchGroupDetails" class="btn-primary mt-4">
                    {{ $t('common.retry') }}
                </button>
            </div>
        </div>

        <!-- Group Details -->
        <div v-else-if="group">
            <!-- Group Info Card -->
            <div class="card p-6">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ group.name }}</h1>
                        <div class="flex items-center gap-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
                            <span class="flex items-center gap-1">
                                <i class="pi pi-user"></i>
                                {{ group.teacher?.name }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="pi pi-users"></i>
                                {{ $t('groups.studentsCount') }}: {{ studentsCount }} 
                            </span>
                            <span :class="group.is_active ? 'text-green-600' : 'text-red-600'">
                                {{ group.is_active ? $t('common.active') : $t('common.inactive') }}
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button @click="openEditModal" class="btn-secondary">
                            <i class="pi pi-pencil mr-2"></i>
                            {{ $t('common.edit') }}
                        </button>
                    </div>
                </div>

                <!-- Schedule -->
                <div v-if="group.schedule?.length" class="mt-4">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        {{ $t('groups.schedule') }}
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="(item, index) in parseSchedule(group.schedule)" :key="index"
                            class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm">
                            {{ getDayName(item.day) }} {{ formatTime(item.start_time) }} - {{
                                formatTime(item.end_time) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="card">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex -mb-px">
                        <button @click="activeTab = 'students'" :class="[
                            'px-6 py-3 text-sm font-medium border-b-2 transition-colors',
                            activeTab === 'students'
                                ? 'border-primary-600 text-primary-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                        ]">
                            <i class="pi pi-users mr-2"></i>
                            {{ $t('groups.students') }}
                        </button>
                        <button @click="activeTab = 'reports'" :class="[
                            'px-6 py-3 text-sm font-medium border-b-2 transition-colors',
                            activeTab === 'reports'
                                ? 'border-primary-600 text-primary-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                        ]">
                            <i class="pi pi-file-edit mr-2"></i>
                            {{ $t('reports.title') }}
                        </button>
                    </nav>
                </div>

                <!-- Students Tab -->
                <div v-show="activeTab === 'students'" class="p-6">
                    <GroupStudentsList 
                        :group="group" 
                        :students="students" 
                        @refresh="fetchStudents"
                        @manage-students="showManageStudents = true"
                    />
                </div>

                <!-- Reports Tab -->
                <div v-show="activeTab === 'reports'" class="p-6">
                    <!-- <GroupReports :group="group" :students="students" /> -->
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        <i class="pi pi-file-edit text-4xl mb-4 block"></i>
                        <p>{{ $t('reports.comingSoon') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Group Modal -->
        <GroupFormModal
            v-if="showEditModal && group"
            :group="group"
            :is-editing="true"
            @close="closeEditModal"
            @updated="onGroupUpdated"
        />

        <!-- Manage Students Modal -->
        <GroupStudentsModal 
            v-if="showManageStudents" 
            :group="group"
			:students="students"
            @close="showManageStudents = false"
            @updated="onStudentsUpdated" 
        />
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
import { getEnumLabel, WeekDayLabels } from '@/constants'

const route = useRoute()
const groupId = computed(() => Number(route.params.id))

const loading = ref(true)
const error = ref<string | null>(null)
const group = ref<Group | null>(null)
const students = ref<any[]>([])
const activeTab = ref<'students' | 'reports'>('students')
const showManageStudents = ref(false)
const showEditModal = ref(false)

const studentsCount = computed(() => students.value.length)

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

/**
 * Parse schedule - handle both string and array formats
 */
function parseSchedule(schedule: any): ScheduleItem[] {
    if (!schedule) return []

    if (Array.isArray(schedule)) {
        return schedule
    }

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

/**
 * Get day name in Arabic using constants
 */
function getDayName(day: string): string {
    if (!day) return ''
    const dayValue = typeof day === 'object' ? (day as any).value : day
    return getEnumLabel(WeekDayLabels, dayValue?.toLowerCase())
}

/**
 * Format time string (handle HH:mm:ss and HH:mm formats)
 */
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

/**
 * Open edit modal (uses existing group data, no API call)
 */
function openEditModal() {
    showEditModal.value = true
}

/**
 * Close edit modal
 */
function closeEditModal() {
    showEditModal.value = false
}

/**
 * Handle group update (update local state with API response)
 */
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