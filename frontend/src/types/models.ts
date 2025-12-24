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
  created_at: string
}

export type UserRole = 'super_admin' | 'admin' | 'teacher' | 'student'

export type UserRoleValue = User['role']['value']

export interface Student {
  id: number
  code: string
  name: string
  email: string
  phone?: string
  grade?: string
  group_id?: number
  teacher_id?: number
  status: 'active' | 'inactive'
  memorization_data?: MemorizationData
  tajweed_data?: TajweedData
  created_at: string
  updated_at: string
}

export interface MemorizationData {
  current_amount: string
  start_date: string
  completion_date?: string
  attendance_per_week: number
  attendance_schedule: string[]
  is_online: boolean
}

export interface TajweedData {
  recitation_level: 'proficient' | 'good' | 'needs_improvement'
  theoretical_status: 'studied' | 'studying' | 'not_studied'
  tuhfatul_atfal_memorized: boolean
  jazariyyah_memorized: boolean
}

export interface Teacher {
  id: number
  name: string
  email: string
  phone?: string
  groups_count?: number
  students_count?: number
  created_at: string
  updated_at: string
}

export interface Group {
  id: number
  name: string
  teacher_id: number
  teacher?: Teacher
  students?: Student[]
  students_count?: number
  schedule?: string[]
  created_at: string
  updated_at: string
}

export interface AttendanceRecord {
  id: number
  student_id: number
  student?: Student
  date: string
  status: 'present' | 'absent' | 'late' | 'excused'
  memorized_amount?: string
  grade?: number
  notes?: string
  created_at: string
  updated_at: string
}

export interface Report {
  id: number
  group_id: number
  group?: Group
  type: 'daily' | 'weekly' | 'monthly' | 'yearly'
  date_from: string
  date_to: string
  statistics: ReportStatistics
  created_at: string
  updated_at: string
}

export interface ReportStatistics {
  total_students: number
  regular_count: number
  irregular_count: number
  disconnected_count: number
  attendance_rate: number
}