// ============= Models ============ //
export interface User {
    id: number
    name: string
    username: string
    email: string
    email_verified: boolean
    phone: string | null
    phone_verified: boolean
    role: {
        for_view: string
        value: UserRole
    }
    gender: {
        for_view: string
        value: 'male' | 'female'
    }
    is_active: boolean
    created_at: string
    student?: StudentProfile
    teacher?: TeacherProfile
}

export interface StudentProfile {
    id: number
    educational_stage: {
        for_view: string
        value: string
    }
    begin_memorizing_at: string | null
    memorizing_completed_at: string | null
    created_by?: {
        id: number
        name: string
    }
}

export interface StudentTajweed {
    recitation_level: {
        for_view: string
        value: string
    }
    tajweed_learning_status: {
        for_view: string
        value: string
    }
    notes: string | null
}

export interface TeacherProfile {
    id: number
    specialization: string | null
    created_by?: {
        id: number
        name: string
    }
}

export interface Group {
    id: number
    name: string
    teacher_id: number
    teacher?: {
        id: number
        name: string
        specialization?: string
    }
    schedule: ScheduleItem[]
    is_online: boolean
    is_active: boolean
    students_count?: number
    created_at: string
    updated_at: string
}

export interface ScheduleItem {
    day: WeekDay
    start_time: string
    end_time: string
}

export type WeekDay = 'sunday' | 'monday' | 'tuesday' | 'wednesday' | 'thursday' | 'friday' | 'saturday'

export interface GroupStudent {
    id: number
    name: string
    username: string
    email: string
    phone?: string
    pivot: {
        student_status: string | null
        memorizing_amount: string
        joined_at: string
        left_at: string | null
    }
}

export type UserRole = 'super_admin' | 'admin' | 'teacher' | 'student'

export type UserRoleValue = User['role']['value']

// ============= Forms ============ //
// == User Forms == //
export interface CreateUserForm {
    name: string
    username: string
    email: string
    phone?: string
    password: string
    password_confirmation: string
    role: Exclude<UserRole, 'super_admin'>
    gender: 'male' | 'female'
    // Teacher fields
    teacher_specialization?: string
    // Student fields
    student_educational_stage?: string
    student_begin_memorizing_at?: string
    student_memorizing_completed_at?: string
    // Tajweed fields
    student_tajweed_recitation_level?: string
    student_tajweed_learning_status?: string
    student_tajweed_notes?: string
}

export interface UpdateUserForm {
    name?: string
    username?: string
    email?: string
    phone?: string
    password?: string
    password_confirmation?: string
    gender?: 'male' | 'female'
    // Teacher fields
    teacher_specialization?: string
    // Student fields
    student_educational_stage?: string
    student_begin_memorizing_at?: string
    student_memorizing_completed_at?: string
    // Tajweed fields
    student_recitation_level?: string
    student_tajweed_learning_status?: string
    student_tajweed_notes?: string
}

// == Group Forms == //
export interface CreateGroupForm {
    name: string
    teacher_id: number
    schedule: ScheduleItem[]
    is_online?: boolean
    is_active?: boolean
}

export interface UpdateGroupForm {
    name?: string
    teacher_id?: number
    schedule?: ScheduleItem[]
    is_online?: boolean
    is_active?: boolean
}

// ============= Filters ============ //
export interface UserFilters {
    search?: string
    role?: UserRole
    gender?: 'male' | 'female'
    per_page?: number
    page?: number
}
export interface GroupFilters {
    search?: string
    teacher_id?: number
    is_active?: boolean
    per_page?: number
    page?: number
}
