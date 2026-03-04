import { createI18n } from 'vue-i18n'
import ar from './locales/ar'

export default createI18n({
  legacy: false,
  locale: 'ar',
  fallbackLocale: 'ar',
  messages: {
    ar
  }
})