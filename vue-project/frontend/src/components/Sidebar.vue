<template>
  <div class="sidebar-container" :class="{ collapsed }">

    <!-- Close button (inside sidebar) -->
    <button class="sidebar-close-btn" v-if="!collapsed" @click="$emit('collapse')">
      🥺
    </button>

    <!-- Sidebar content -->
    <div class="sidebar-content" v-if="!collapsed">
      <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item" v-for="link in activeLinks" :key="link.name">
          <router-link
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
import { computed, defineProps } from 'vue'
import { useUserStore } from '../store/userstore.js'

defineProps({
  collapsed: Boolean
})

const userStore = useUserStore()

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
