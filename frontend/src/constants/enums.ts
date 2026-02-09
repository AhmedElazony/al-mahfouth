/**
 * User Roles
 */
export const UserRole = {
  SUPER_ADMIN: 'super_admin',
  ADMIN: 'admin',
  TEACHER: 'teacher',
  STUDENT: 'student',
} as const

export type UserRoleType = typeof UserRole[keyof typeof UserRole]

export const UserRoleLabels: Record<UserRoleType, string> = {
  [UserRole.SUPER_ADMIN]: 'مشرف عام',
  [UserRole.ADMIN]: 'مشرف',
  [UserRole.TEACHER]: 'معلم',
  [UserRole.STUDENT]: 'طالب',
}

export const UserRoleOptions = Object.entries(UserRoleLabels).map(
  ([value, label]) => ({ value, label })
)

/**
 * Memorizing Amount Options
 */
export const MemorizingAmount = {
  LESS_THAN_ONE_QUARTER: 'less_than_one_quarter',
  ONE_QUARTER: 'one_quarter',
  HALF_HIZB: 'half_hizb',
  ONE_HIZB: 'one_hizb',
  ONE_JUZ: 'one_juz',
  MORE_THAN_ONE_JUZ: 'more_than_one_juz',
} as const

export type MemorizingAmountType = typeof MemorizingAmount[keyof typeof MemorizingAmount]

export const MemorizingAmountLabels: Record<MemorizingAmountType, string> = {
  [MemorizingAmount.LESS_THAN_ONE_QUARTER]: 'أقل من ربع جزء',
  [MemorizingAmount.ONE_QUARTER]: 'ربع جزء',
  [MemorizingAmount.HALF_HIZB]: 'نصف حزب',
  [MemorizingAmount.ONE_HIZB]: 'حزب واحد',
  [MemorizingAmount.ONE_JUZ]: 'جزء كامل',
  [MemorizingAmount.MORE_THAN_ONE_JUZ]: 'أكثر من جزء',
}

export const MemorizingAmountOptions = Object.entries(MemorizingAmountLabels).map(
  ([value, label]) => ({ value, label })
)

/**
 * Week Days
 */
export const WeekDay = {
  SATURDAY: 'saturday',
  SUNDAY: 'sunday',
  MONDAY: 'monday',
  TUESDAY: 'tuesday',
  WEDNESDAY: 'wednesday',
  THURSDAY: 'thursday',
  FRIDAY: 'friday'
} as const

export type WeekDayType = typeof WeekDay[keyof typeof WeekDay]

export const WeekDayLabels: Record<WeekDayType, string> = {
  [WeekDay.SATURDAY]: 'السبت',
  [WeekDay.SUNDAY]: 'الأحد',
  [WeekDay.MONDAY]: 'الاثنين',
  [WeekDay.TUESDAY]: 'الثلاثاء',
  [WeekDay.WEDNESDAY]: 'الأربعاء',
  [WeekDay.THURSDAY]: 'الخميس',
  [WeekDay.FRIDAY]: 'الجمعة',
}

export const WeekDayOptions = Object.entries(WeekDayLabels).map(
  ([value, label]) => ({ value, label })
)

/**
 * Student Status in Group
 */
export const StudentStatus = {
  COMMITTED: 'committed',
  ABSENT: 'absent',
  UNCOMMITTED: 'uncommitted',
} as const

export type StudentStatusType = typeof StudentStatus[keyof typeof StudentStatus]

export const StudentStatusLabels: Record<StudentStatusType, string> = {
  [StudentStatus.COMMITTED]: 'ملتزم',
  [StudentStatus.ABSENT]: 'غائب',
  [StudentStatus.UNCOMMITTED]: 'غير ملتزم',
}

export const StudentStatusOptions = Object.entries(StudentStatusLabels).map(
  ([value, label]) => ({ value, label })
)

/**
 * Educational Stages
 */
export const EducationalStage = {
  NO_SCHOOL: 'no_school',
  PRIMARY: 'primary_school',
  PREPARATORY: 'preparatory_school',
  SECONDARY: 'secondary_school',
  UNIVERSITY: 'university_stage',
  GRADUATE: 'graduate',
} as const

export type EducationalStageType = typeof EducationalStage[keyof typeof EducationalStage]

export const EducationalStageLabels: Record<EducationalStageType, string> = {
  [EducationalStage.NO_SCHOOL]: 'ما قبل المدرسة',
  [EducationalStage.PRIMARY]: 'ابتدائي',
  [EducationalStage.PREPARATORY]: 'متوسط',
  [EducationalStage.SECONDARY]: 'ثانوي',
  [EducationalStage.UNIVERSITY]: 'جامعي',
  [EducationalStage.GRADUATE]: 'خريج',
}

export const EducationalStageOptions = Object.entries(EducationalStageLabels).map(
  ([value, label]) => ({ value, label })
)

export const AttendanceStatus = {
  ATTENDED: 'attended',
  ABSENT: 'absent',
  EXCUSED: 'excused',
} as const

export type AttendanceStatusType = typeof AttendanceStatus[keyof typeof AttendanceStatus]

export const AttendanceStatusLabels: Record<AttendanceStatusType, string> = {
  [AttendanceStatus.ATTENDED]: 'حاضر',
  [AttendanceStatus.ABSENT]: 'غائب',
  [AttendanceStatus.EXCUSED]: 'معذور',
}

export const AttendanceStatusOptions = Object.entries(AttendanceStatusLabels).map(
  ([value, label]) => ({ value, label })
)

export const Grade = {
  BAD: 'bad',
  GOOD: 'good',
  VERY_GOOD: 'very_good',
  EXCELLENT: 'excellent',
} as const

export type GradeType = typeof Grade[keyof typeof Grade]

export const GradeLabels: Record<GradeType, string> = {
  [Grade.BAD]: 'سيئ',
  [Grade.GOOD]: 'جيد',
  [Grade.VERY_GOOD]: 'جيد جداً',
  [Grade.EXCELLENT]: 'ممتاز',
}
export const GradeOptions = Object.entries(GradeLabels).map(
  ([value, label]) => ({ value, label })
)

export const LearningStatus = {
    NOT_STARTED: 'not_started',
    IN_PROGRESS: 'in_progress',
    COMPLETED: 'completed',
} as const

export type LearningStatusType = typeof LearningStatus[keyof typeof LearningStatus]

export const LearningStatusLabels: Record<LearningStatusType, string> = {
    [LearningStatus.NOT_STARTED]: 'لم يبدأ',
    [LearningStatus.IN_PROGRESS]: 'قيد الدراسة',
    [LearningStatus.COMPLETED]: 'مكتمل',
}
export const LearningStatusOptions = Object.entries(LearningStatusLabels).map(
    ([value, label]) => ({ value, label })
)

/**
 * Helper function to get label from value
 */
export function getEnumLabel<T extends string>(
  labels: Record<T, string>,
  value: T | string | undefined | null
): string {
  if (!value) return '-'
  return labels[value as T] || value
}
