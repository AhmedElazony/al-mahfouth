import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router'

// Layouts
import AuthLayout from '@/layouts/AuthLayout.vue'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const routes: RouteRecordRaw[] = [
	// Public Home 
	{
		path: '/',
		name: 'landing',
		component: () => import('@/pages/guest/LandingPage.vue'),
	},

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
		path: '/dashboard',
		component: DashboardLayout,
		meta: { requiresAuth: true },
		children: [
			{
				path: '',
				name: 'dashboard',
				component: () => import('@/pages/dashboard/DashboardPage.vue')
			},
			{
				path: 'users',
				name: 'users',
				component: () => import('@/pages/users/UsersListPage.vue'),
				meta: { roles: ['super_admin', 'admin'] }
			},
			{
				path: 'groups',
				name: 'groups',
				component: () => import('@/pages/groups/GroupsListPage.vue'),
				meta: { roles: ['super_admin', 'admin', 'teacher'] }
			},
			{
				path: 'groups/:id',
				name: 'group-details',
				component: () => import('@/pages/groups/GroupDetailsPage.vue'),
				meta: { requiresAuth: true, roles: ['super_admin', 'admin', 'teacher'] }
			},
			{
				path: 'settings',
				name: 'settings',
				component: () => import('@/pages/settings/SettingsPage.vue'),
				meta: { requiresAuth: true }
			}
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