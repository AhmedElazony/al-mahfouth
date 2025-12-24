import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Layouts
import AuthLayout from '@/layouts/AuthLayout.vue'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const routes: RouteRecordRaw[] = [
  // Auth Routes
  {
    path: '/auth',
    component: AuthLayout,
    meta: { guest: true },
    children: [
      {
        path: 'login',
        name: 'login',
        component: () => import('@/pages/auth/LoginPage.vue')
      }
    ]
  },

  // Dashboard Routes (Protected)
  {
    path: '/',
    component: DashboardLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'dashboard',
        component: () => import('@/pages/dashboard/DashboardPage.vue')
      },
    ]
  },

  // 404
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/pages/errors/NotFoundPage.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation Guards
router.beforeEach(async (to, _from, next) => {
  // Dynamically import to avoid circular dependency
  const { useAuthStore } = await import('@/stores/auth')
  const authStore = useAuthStore()

  // Check token directly from localStorage as fallback
  const hasToken = !!localStorage.getItem('token')
  const isAuthenticated = authStore.isAuthenticated || hasToken
  console.log('Auth Guard:', { isAuthenticated, to: to.fullPath })
  // Check if route requires authentication
  if (to.meta.requiresAuth && !isAuthenticated) {
    // Redirect to login with return URL
    next({ name: 'login', query: { redirect: to.fullPath } })
  } 
  // Check if route is for guests only (like login page)
  else if (to.meta.guest && isAuthenticated) {
    // Redirect to dashboard (home)
    next({ name: 'dashboard' })
  } 
  else {
    next()
  }
})

export default router