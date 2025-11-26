<template>
  <div class="sidebar-container" :class="{ collapsed: !isOpen }">

    <!-- Toggle button (always visible) -->
    <button class="sidebar-toggle-btn" @click="toggleSidebar">
      <span v-if="isOpen">✖</span>
      <span v-else>🎀</span>
    </button>

    <!-- Sidebar content -->
    <div class="sidebar-content" v-if="isOpen">
      <ul class="nav nav-pills flex-column mb-auto mt-3">
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

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useUserStore } from '../store/userstore.js'

const userStore = useUserStore()

const isOpen = ref(true)
const toggleSidebar = () => (isOpen.value = !isOpen.value)

const studentLinks = [
  { name: 'Dashboard', to: '/dashboard', auth: true },
  { name: 'Assignments', to: '/assignments', auth: true },
  { name: 'Results', to: '/results', auth: true },
  { name: 'Level', to: '/level', auth: true }
]

const adminLinks = [
  { name: 'Admin Dashboard', to: '/admin-dashboard', auth: true },
  { name: 'Manage Quizzes', to: '/admin-quizzes', auth: true },
  { name: 'User Results', to: '/admin-results', auth: true },
  { name: 'Manage Users', to: '/admin-users', auth: true }
]

const activeLinks = computed(() => {
  if (!userStore.isLoggedIn) return []
  if (userStore.role === 2 || userStore.role === 3) return adminLinks
  return studentLinks
})
</script>
