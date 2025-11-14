<template>
  <div class="sidebar">
    <ul class="nav nav-pills flex-column mb-auto">

      <!-- LOOP through the correct links based on user role -->
      <li class="nav-item" v-for="link in activeLinks" :key="link.name">
        <router-link
          v-if="!link.auth || (link.auth && userStore.isLoggedIn)"
          :to="link.to"
          class="nav-link"
          active-class="active"
        >
          {{ link.name }}
        </router-link>
      </li>

    </ul>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useUserStore } from '../store/userstore.js'

const userStore = useUserStore()

// Student links
const studentLinks = [
  { name: 'Dashboard', to: '/dashboard', auth: true },
  { name: 'Assignments', to: '/assignments', auth: true },
  { name: 'Results', to: '/results', auth: true },
  { name: 'Level', to: '/level', auth: true }
]

// Admin/Teacher links
const adminLinks = [
  { name: 'AdminDashboard', to: '/admin-dashboard', auth: true },
  { name: 'Manage Quizzes', to: '/admin-quizzes', auth: true },
  { name: 'User Results', to: '/admin-results', auth: true },
  { name: 'Manage Users', to: '/admin-users', auth: true }
]

// Pick sidebar based on user role
const activeLinks = computed(() => {
  if (!userStore.isLoggedIn) return []
  if (userStore.role === 2 || userStore.role === 3) return adminLinks
  return studentLinks
})
</script>
