import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '../store/userstore.js'
import DashboardView from '../views/DashboardView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import UserLevelView from '@/views/UserLevelView.vue'
import AssignmentView from '@/views/AssignmentView.vue'
import QuizView from '@/views/QuizView.vue' 
import ResultCard from '@/views/ResultCard.vue'
import AdminDashboardView from '@/views/AdminDashboard.vue'
import AdminResults from '@/components/AdminResults.vue'
import AdminQuizzes from '@/components/AdminQuizzes.vue'
import AdminUsers from '@/components/AdminUsers.vue'
import QuizResultView from '@/views/QuizResultView.vue'

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/login', component: LoginView },
  { path: '/register', component: RegisterView },
  { path: '/results', component: ResultCard, meta: { requiresAuth: true } },
  { path: '/results/:id', name: 'results', component: QuizResultView, meta: { requiresAuth: true } },
  { path: '/level', component: UserLevelView, meta: { requiresAuth: true } },
  { path: '/assignments', component: AssignmentView, meta: { requiresAuth: true } },
  { path: '/dashboard', component: DashboardView, meta: { requiresAuth: true }},
  { path: '/admin-dashboard', component: AdminDashboardView, meta: { requiresAuth: true, adminOnly: true }},
  { path: '/quiz/:id', name: 'Quiz', component: QuizView, props: true , meta: { requiresAuth: true }},
  { path: '/admin-quizzes', component: AdminQuizzes, meta: { requiresAuth: true, adminOnly: true }},
  { path: '/admin-results', component: AdminResults, meta: { requiresAuth: true, adminOnly: true }},
  { path: '/admin-users', component: AdminUsers, meta: { requiresAuth: true, adminOnly: true }}
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore()

  // wait for fetchUser if not ready
  if (!userStore.ready) await userStore.fetchUser()

  if (to.meta.requiresAuth && !userStore.isLoggedIn) {
    return next('/login')
  }

  // Prevent logged-in users from going to login/register
  if ((to.path === '/login' || to.path === '/register') && userStore.isLoggedIn) {
    return next('/dashboard')
  }

  next()
})

export default router
