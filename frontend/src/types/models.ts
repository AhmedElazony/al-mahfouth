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

export interface TeacherProfile {
  id: number
  specialization: string | null
  created_by?: {
    id: number
    name: string
  }
}

export type UserRole = 'super_admin' | 'admin' | 'teacher' | 'student'

export type UserRoleValue = User['role']['value']

export interface CreateUserForm {
  name: string
  username: string
  email: string
  phone?: string
  password: string
  password_confirmation: string
  role: Exclude<UserRole, 'super_admin'>
  gender: 'male' | 'female'
  teacher_specialization?: string
  student_educational_stage?: string
  student_begin_memorizing_at?: string
  student_memorizing_completed_at?: string
}

export interface UpdateUserForm {
  name?: string
  username?: string
  email?: string
  phone?: string
  password?: string
  password_confirmation?: string
  gender?: 'male' | 'female'
  teacher_specialization?: string
  student_educational_stage?: string
  student_begin_memorizing_at?: string
  student_memorizing_completed_at?: string
}

export interface UserFilters {
  search?: string
  role?: UserRole
  gender?: 'male' | 'female'
  per_page?: number
  page?: number
}