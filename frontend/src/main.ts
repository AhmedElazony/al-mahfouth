import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import i18n from './i18n'
import primevue from './plugins/primevue'

// Styles
import './assets/styles/main.css'
import 'primeicons/primeicons.css'

const app = createApp(App)

// Plugins
app.use(createPinia())
app.use(router)
app.use(i18n)
app.use(primevue)

app.mount('#app')