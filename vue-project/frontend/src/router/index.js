import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '../store/userstore.js'
import DashboardView from '../views/DashboardView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import UserLevelView from '@/views/UserLevelView.vue'
import AssignmentView from '@/views/AssignmentView.vue'
import QuizView from '@/views/QuizView.vue' 
import ResultView from '@/views/ResultView.vue'
import AdminDashboardView from '@/views/AdminDashboard.vue'
import AdminResultsView from '@/views/AdminResultsView.vue'
import AdminQuizzes from '@/components/AdminQuizzes.vue'
import AdminUsersView from '@/views/AdminUsersView.vue'
import AdminEditUsersView from '@/views/AdminEditUsersView.vue'
import QuizResultView from '@/views/QuizResultView.vue'
import AdminResultsDetail from '@/views/AdminResultsDetail.vue'

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/login', component: LoginView },
  { path: '/register', component: RegisterView },
  { path: '/results', component: ResultView, meta: { requiresAuth: true, userOnly: true } },
  { path: '/results/:id', name: 'results', component: QuizResultView, meta: { requiresAuth: true, userOnly: true } },
  { path: '/level', component: UserLevelView, meta: { requiresAuth: true, userOnly: true } },
  { path: '/assignments', component: AssignmentView, meta: { requiresAuth: true, userOnly: true } },
  { path: '/dashboard', component: DashboardView, meta: { requiresAuth: true, userOnly: true } },
  { path: '/admin-dashboard', component: AdminDashboardView, meta: { requiresAuth: true, adminOnly: true }},
  { path: '/quiz/:id', name: 'Quiz', component: QuizView, props: true , meta: { requiresAuth: true, userOnly: true } },
  { path: '/admin-quizzes', component: AdminQuizzes, meta: { requiresAuth: true, adminOnly: true }},
  { path: '/admin-results', component: AdminResultsView, meta: { requiresAuth: true, adminOnly: true }},
  { path: '/admin-results/:id', component: AdminResultsDetail, meta: { requiresAuth: true, adminOnly: true }},
  { path: '/admin-users', component: AdminUsersView, meta: { requiresAuth: true, adminOnly: true }},
  { path: '/admin-users/:id', component: AdminEditUsersView, meta: { requiresAuth: true, adminOnly: true }},
]

const router = createRouter({
  history: createWebHistory(),
  routes
})
router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore()

  // 1. Wait until user is loaded (only first time)
  if (!userStore.ready) {
    await userStore.fetchUser()
  }

  const isLoggedIn = userStore.isLoggedIn
  const userRole   = userStore.user?.role || null

  // 2. Requires login?
  if (to.meta.requiresAuth && !isLoggedIn) {
    return next('/login')
  }

  // 3. Prevent logged-in users from seeing login/register
  if ((to.path === '/login' || to.path === '/register') && isLoggedIn) {
    return next('/dashboard')
  }
  // 4. Admin-only route?
  if (to.meta.adminOnly) {
    console.log(userRole)
    if (!isLoggedIn) return next('/login')
    if (userRole !== 3) return next('/dashboard')
  }
  // 5. User-only route?
  if (to.meta.userOnly) {
    if (!isLoggedIn) return next('/login')
    if (userRole !== 1) return next('/admin-dashboard')
  }


  next()
})


export default router
