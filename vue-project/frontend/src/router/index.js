import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '../store/userstore.js'
import DashboardView from '../views/DashboardView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import UserLevelView from '@/views/UserLevelView.vue'
import AssignmentView from '@/views/AssignmentView.vue'
import QuizView from '@/views/QuizView.vue' 
import ResultCard from '@/views/ResultCard.vue'

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/login', component: LoginView },
  { path: '/register', component: RegisterView },
  { path: '/results', component: ResultCard, meta: { requiresAuth: true } },
  { path: '/level', component: UserLevelView, meta: { requiresAuth: true } },
  { path: '/assignments', component: AssignmentView, meta: { requiresAuth: true } },
  { path: '/dashboard', component: DashboardView, meta: { requiresAuth: true } },
   {
    path: '/quiz/:id',
    name: 'Quiz',
    component: QuizView,
    props: true // allows the :id param to be passed as a prop
  },

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
