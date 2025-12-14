import type { App } from 'vue'
import PrimeVue from 'primevue/config'
import Aura from '@primevue/themes/aura'
import ToastService from 'primevue/toastservice'
import ConfirmationService from 'primevue/confirmationservice'

// Components
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Dropdown from 'primevue/dropdown'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'
import Toast from 'primevue/toast'
import Menu from 'primevue/menu'
import Card from 'primevue/card'
import Tag from 'primevue/tag'
import Avatar from 'primevue/avatar'
import Sidebar from 'primevue/sidebar'
import Breadcrumb from 'primevue/breadcrumb'
import ProgressBar from 'primevue/progressbar'
import Skeleton from 'primevue/skeleton'
import ConfirmDialog from 'primevue/confirmdialog'

export default {
  install(app: App) {
    app.use(PrimeVue, {
      theme: {
        preset: Aura,
        options: {
          prefix: 'p',
          darkModeSelector: '.dark',
          cssLayer: false
        }
      },
      ripple: true,
      locale: {
        startsWith: 'يبدأ بـ',
        contains: 'يحتوي على',
        notContains: 'لا يحتوي على',
        endsWith: 'ينتهي بـ',
        equals: 'يساوي',
        notEquals: 'لا يساوي',
        noFilter: 'بدون تصفية',
        lt: 'أقل من',
        lte: 'أقل من أو يساوي',
        gt: 'أكبر من',
        gte: 'أكبر من أو يساوي',
        dateIs: 'التاريخ هو',
        dateIsNot: 'التاريخ ليس',
        dateBefore: 'التاريخ قبل',
        dateAfter: 'التاريخ بعد',
        clear: 'مسح',
        apply: 'تطبيق',
        matchAll: 'مطابقة الكل',
        matchAny: 'مطابقة أي',
        addRule: 'إضافة قاعدة',
        removeRule: 'حذف قاعدة',
        accept: 'نعم',
        reject: 'لا',
        choose: 'اختر',
        upload: 'رفع',
        cancel: 'إلغاء',
        dayNames: ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'],
        dayNamesShort: ['أحد', 'اثن', 'ثلا', 'أرب', 'خمي', 'جمع', 'سبت'],
        dayNamesMin: ['ح', 'ن', 'ث', 'ر', 'خ', 'ج', 'س'],
        monthNames: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
        monthNamesShort: ['ين', 'فب', 'مار', 'أبر', 'ماي', 'يون', 'يول', 'أغس', 'سب', 'أكت', 'نوف', 'ديس'],
        today: 'اليوم',
        weekHeader: 'أس',
        firstDayOfWeek: 6,
        dateFormat: 'dd/mm/yy',
        weak: 'ضعيف',
        medium: 'متوسط',
        strong: 'قوي',
        passwordPrompt: 'أدخل كلمة المرور',
        emptyFilterMessage: 'لا توجد نتائج',
        emptyMessage: 'لا توجد خيارات متاحة'
      }
    })

    app.use(ToastService)
    app.use(ConfirmationService)

    // Register components globally
    app.component('PButton', Button)
    app.component('PInputText', InputText)
    app.component('PPassword', Password)
    app.component('PDropdown', Dropdown)
    app.component('PDataTable', DataTable)
    app.component('PColumn', Column)
    app.component('PDialog', Dialog)
    app.component('PToast', Toast)
    app.component('PMenu', Menu)
    app.component('PCard', Card)
    app.component('PTag', Tag)
    app.component('PAvatar', Avatar)
    app.component('PSidebar', Sidebar)
    app.component('PBreadcrumb', Breadcrumb)
    app.component('PProgressBar', ProgressBar)
    app.component('PSkeleton', Skeleton)
    app.component('PConfirmDialog', ConfirmDialog)
  }
}