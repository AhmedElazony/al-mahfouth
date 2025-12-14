export default {
  // App
  app: {
    name: 'المحفوظ',
    description: 'منصة متابعة حفظ القرآن الكريم'
  },

  // Navigation
  nav: {
    dashboard: 'لوحة التحكم',
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
    username: 'اسم المستخدم',
    password: 'كلمة المرور',
    rememberMe: 'تذكرني',
    forgotPassword: 'نسيت كلمة المرور؟',
    loginButton: 'دخول',
    loginTitle: 'مرحباً بك في المحفوظ',
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
    addGroup: 'إضافة مجموعة',
    editGroup: 'تعديل المجموعة',
    groupDetails: 'تفاصيل المجموعة',
    name: 'اسم المجموعة',
    teacher: 'المعلم',
    students: 'الطلاب',
    studentsCount: 'عدد الطلاب',
    schedule: 'المواعيد',
    createdAt: 'تاريخ الإنشاء'
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
    loading: 'جاري التحميل...',
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
    close: 'إغلاق'
  },

  // Roles
  roles: {
    superAdmin: 'المشرف العام',
    supervisor: 'مشرف',
    teacher: 'معلم',
    student: 'طالب'
  }
}