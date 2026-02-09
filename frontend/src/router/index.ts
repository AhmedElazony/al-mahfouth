import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router'

// Layouts
import AuthLayout from '@/layouts/AuthLayout.vue'
import AppLayout from '@/layouts/AppLayout.vue'

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
		component: AppLayout,
		meta: { requiresAuth: true, roles: ['super_admin', 'admin', 'teacher'] },
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

	// Student Routes
	{
		path: '/student',
		component: AppLayout,
		meta: { requiresAuth: true, roles: ['student'] },
		children: [
			{
				path: '',
				name: 'student-home',
				component: () => import('@/pages/student/StudentHomePage.vue')
			},
			{
				path: 'groups',
				name: 'student-groups',
				component: () => import('@/pages/student/StudentGroupsPage.vue')
			},
			{
				path: 'settings',
				name: 'student-settings',
				component: () => import('@/pages/settings/SettingsPage.vue')
			}
		]
	},

	{
		path: '/community',
		name: 'community',
		component: AppLayout,
		meta: { requiresAuth: true },
		children: [
			{
				path: '',
				name: 'home',
				component: () => import('@/pages/community/CommunityPage.vue'),
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
    const { useAuthStore } = await import('@/stores/auth')
    const authStore = useAuthStore()

    const hasToken = !!localStorage.getItem('token')
    const isAuthenticated = authStore.isAuthenticated || hasToken
    
    // Check if route requires authentication
    if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: 'login', query: { redirect: to.fullPath } })
    }
    // Check role-based access
    else if (to.meta.roles && isAuthenticated) {
        const userRole = authStore.user?.role?.value
        const allowedRoles = to.meta.roles as string[]
        
        if (!userRole || !allowedRoles.includes(userRole)) {
            // Redirect to appropriate dashboard based on role
            if (userRole === 'student') {
                next({ name: 'student-home' })
            } else {
                next({ name: 'dashboard' })
            }
        } else {
            next()
        }
    }
    // Check if route is for guests only
    else if (to.meta.guest && isAuthenticated) {
        // Redirect based on user role
        const userRole = authStore.user?.role?.value
        if (userRole === 'student') {
            next({ name: 'student-home' })
        } else {
            next({ name: 'dashboard' })
        }
    }
    else {
        next()
    }
})

export default router