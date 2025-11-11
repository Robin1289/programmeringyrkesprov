import { createRouter, createWebHistory } from 'vue-router'

// Views
import HomeView from '../views/HomeView.vue'
import DashboardView from '../views/DashboardView.vue'
import AdminDashboard from '../views/AdminDashboard.vue'
import QuizListView from '../views/QuizListView.vue'
import QuizResultView from '../views/QuizResultView.vue'
import AddQuestionView from '../views/AddQuestionView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'

// Pinia store (för att kolla login status och roll)
import { useUserStore } from '../store/userstore.js'

const routes = [
    {
    path: '/login',
    name: 'Login',
    component: LoginView
    },
    {
    path: '/register',
    name: 'Register',
    component: RegisterView
    },
  {
    path: '/',
    name: 'Home',
    component: HomeView
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: DashboardView,
    meta: { requiresAuth: true } // kräver inloggning
  },
  {
    path: '/admindashboard',
    name: 'AdminDashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, requiresAdmin: true } // admin-route
  },
  {
    path: '/quizzes',
    name: 'QuizList',
    component: QuizListView,
    meta: { requiresAuth: true }
  },
  {
    path: '/quizresult',
    name: 'QuizResult',
    component: QuizResultView,
    meta: { requiresAuth: true }
  },
  {
    path: '/add-question',
    name: 'AddQuestion',
    component: AddQuestionView,
    meta: { requiresAuth: true, requiresAdmin: true } // admin-route
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const userStore = useUserStore()
  if (to.meta.requiresAuth && !userStore.isLoggedIn) {
    // Om sidan kräver login och användaren inte är inloggad → skicka till Home
    next({ name: 'Login' })
  } else {
    next()
  }
})  

export default router
