import { all } from "axios";

export default {
  // App
  app: {
    name: 'المحفوظ',
    description: 'منصة متابعة حفظ القرآن الكريم'
  },

  // Navigation
  nav: {
    dashboard: 'لوحة التحكم',
    users: 'المستخدمين',
    students: 'الطلاب',
    teachers: 'المعلمين',
    groups: 'المجموعات',
    reports: 'التقارير',
    settings: 'الإعدادات',
    logout: 'تسجيل الخروج'
  },

  // Auth
  auth: {
    login: 'تسجيل الدخول',
    logout: 'تسجيل الخروج',
    email: 'البريد الإلكتروني',
    username: 'مُعرّف المستخدم',
    password: 'كلمة المرور',
    rememberMe: 'تذكرني',
    forgotPassword: 'نسيت كلمة المرور؟',
    loginButton: 'دخول',
    loginTitle: 'مرحباً بك في منصة المحفوظ',
    loginSubtitle: 'سجل دخولك للمتابعة'
  },

  // Dashboard
  dashboard: {
    title: 'لوحة التحكم',
    welcome: 'مرحباً',
    totalStudents: 'إجمالي الطلاب',
    totalTeachers: 'إجمالي المعلمين',
    totalGroups: 'إجمالي المجموعات',
    todayAttendance: 'حضور اليوم',
    recentActivity: 'النشاط الأخير',
    upcomingClasses: 'الحلقات القادمة'
  },

  // Users
  users: {
    title: 'المستخدمين',
    subtitle: 'إدارة جميع مستخدمي النظام',
    addUser: 'إضافة مستخدم',
    editUser: 'تعديل المستخدم',
    deleteUser: 'حذف المستخدم',
    deleteConfirmation: 'هل أنت متأكد من حذف المستخدم "{name}"؟',
    name: 'الاسم',
    username: 'مُعرّف المستخدم',
    usernameHint: 'يجب أن يكون فريداً ويحتوي على أحرف إنجليزية وأرقام فقط (مثال: amr_123)',
    email: 'البريد الإلكتروني',
    phone: 'رقم الهاتف',
    password: 'كلمة المرور',
    passwordConfirmation: 'تأكيد كلمة المرور',
    leaveEmptyToKeep: 'اتركه فارغاً للإبقاء على القديمة',
    role: 'الدور',
    gender: 'الجنس',
    male: 'ذكر',
    female: 'أنثى',
    selectRole: 'اختر الدور',
    allRoles: 'جميع الأدوار',
    allGenders: 'الكل',
    specialization: 'التخصص',
    educationalStage: 'المرحلة التعليمية',
    selectStage: 'اختر المرحلة',
    beginMemorizingAt: 'تاريخ بداية الحفظ',
    memorizingCompletedAt: 'تاريخ إتمام الحفظ'
  },

  // Educational Stages
  stages: {
    noSchool: 'ما قبل المدرسة',
    primarySchool: 'المرحلة الابتدائية',
    preparatorySchool: 'المرحلة الإعدادية',
    secondarySchool: 'المرحلة الثانوية',
    universityStage: 'المرحلة الجامعية',
    graduate: 'خريج'
  },

  // Students
  students: {
    title: 'الطلاب',
    addStudent: 'إضافة طالب',
    editStudent: 'تعديل بيانات الطالب',
    studentDetails: 'تفاصيل الطالب',
    name: 'الاسم',
    code: 'كود الطالب',
    email: 'البريد الإلكتروني',
    phone: 'رقم الهاتف',
    grade: 'المرحلة الدراسية',
    group: 'المجموعة',
    teacher: 'المعلم',
    joinDate: 'تاريخ الانضمام',
    status: 'الحالة',
    active: 'نشط',
    inactive: 'غير نشط',
    memorization: {
      title: 'بيانات الحفظ',
      currentAmount: 'المقدار الحالي',
      startDate: 'تاريخ بداية الحفظ',
      completionDate: 'تاريخ الختم',
      attendancePerWeek: 'عدد مرات الحضور الأسبوعي',
      attendanceSchedule: 'مواعيد الحضور',
      isOnline: 'أونلاين',
      isOffline: 'حضوري'
    },
    tajweed: {
      title: 'بيانات التجويد',
      recitationLevel: 'مستوى التلاوة',
      theoreticalStatus: 'حالة التجويد النظري',
      tuhfatulAtfalMemorized: 'حفظ تحفة الأطفال',
      jazariyyahMemorized: 'حفظ المقدمة الجزرية',
      studied: 'درس',
      studying: 'يدرس حالياً',
      notStudied: 'لم يدرس بعد',
      proficient: 'متقن',
      good: 'جيد',
      needsImprovement: 'يحتاج تحسين'
    }
  },

  // Teachers
  teachers: {
    title: 'المعلمين',
    addTeacher: 'إضافة معلم',
    editTeacher: 'تعديل بيانات المعلم',
    teacherDetails: 'تفاصيل المعلم',
    name: 'الاسم',
    email: 'البريد الإلكتروني',
    phone: 'رقم الهاتف',
    groups: 'المجموعات',
    studentsCount: 'عدد الطلاب'
  },

  // Groups
  groups: {
    title: 'المجموعات',
    subtitle: 'إدارة مجموعات الحفظ',
    addGroup: 'إضافة مجموعة',
    editGroup: 'تعديل المجموعة',
    deleteGroup: 'حذف المجموعة',
    deleteConfirmation: 'هل أنت متأكد من حذف المجموعة "{name}"؟',
    name: 'اسم المجموعة',
    teacher: 'المعلم',
    selectTeacher: 'اختر المعلم',
    allTeachers: 'جميع المعلمين',
    students: 'الطلاب',
    studentsCount: 'عدد الطلاب',
    schedule: 'المواعيد',
    addScheduleItem: 'إضافة موعد',
    selectDay: 'اختر اليوم',
    type: 'النوع',
    online: 'عبر الإنترنت',
    offline: 'في المسجد',
    allStatuses: 'جميع الحالات',
    selectStudent: 'اختر الطالب',
    joinedAt: 'تاريخ الانضمام',
    noStudents: 'لا يوجد طلاب في هذه المجموعة',
    createdAt: 'تاريخ الإنشاء',
    day: 'اليوم',
    time: 'الوقت',
    from: 'من',
    to: 'إلى',
    noStatus: 'بدون حالة'
  },

  // Reports
  reports: {
    title: 'التقارير',
    addReport: 'إضافة تقرير',
    dailyReport: 'التقرير اليومي',
    weeklyReport: 'التقرير الأسبوعي',
    monthlyReport: 'التقرير الشهري',
    yearlyReport: 'التقرير السنوي',
    attendance: 'الحضور',
    present: 'حاضر',
    absent: 'غائب',
    late: 'متأخر',
    excused: 'معذور',
    regular: 'منتظم',
    irregular: 'غير منتظم',
    disconnected: 'منقطع',
    memorizedAmount: 'المقدار المحفوظ',
    grade: 'الدرجة',
    notes: 'ملاحظات',
    date: 'التاريخ',
    statistics: 'الإحصائيات'
  },

  // Settings
  settings: {
    title: 'الإعدادات',
    profile: 'الملف الشخصي',
    account: 'الحساب',
    supervisors: 'المشرفين',
    addSupervisor: 'إضافة مشرف',
    appearance: 'المظهر',
    darkMode: 'الوضع الداكن',
    language: 'اللغة'
  },

  // Common
  common: {
    save: 'حفظ',
    cancel: 'إلغاء',
    delete: 'حذف',
    edit: 'تعديل',
    add: 'إضافة',
    search: 'بحث',
    filter: 'تصفية',
    actions: 'الإجراءات',
    confirm: 'تأكيد',
    yes: 'نعم',
    no: 'لا',
    loading: 'جارٍ التحميل...',
    noData: 'لا توجد بيانات',
    success: 'تمت العملية بنجاح',
    error: 'حدث خطأ',
    required: 'هذا الحقل مطلوب',
    optional: 'اختياري',
    all: 'الكل',
    from: 'من',
    to: 'إلى',
    date: 'التاريخ',
    time: 'الوقت',
    details: 'التفاصيل',
    view: 'عرض',
    back: 'رجوع',
    next: 'التالي',
    previous: 'السابق',
    close: 'إغلاق',
    showing: 'عرض',
    of: 'من',
    clearFilters: 'مسح كلمات البحث',
    retry: 'إعادة المحاولة',
    active: 'نشط',
    inactive: 'غير نشط',
    status: 'الحالة'
  },

  // Roles
  roles: {
    superAdmin: 'المشرف العام',
    admin: 'مشرف',
    teacher: 'معلم',
    student: 'طالب'
  }
}
