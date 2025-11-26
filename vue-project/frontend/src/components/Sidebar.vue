<template>
  <div class="sidebar" v-if="isOpen">

    <div class="sidebar-header">
      <button class="sidebar-close-btn" @click="$emit('toggleSidebar')">
        Stäng
      </button>
    </div>

    <ul class="nav nav-pills flex-column mt-3">
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
</template>




<script setup>
import { computed, defineProps } from 'vue'
import { useUserStore } from '../store/userstore.js'

defineProps({
  isOpen: Boolean
})

const userStore = useUserStore()

// Close button as a "link" object
const closeLink = { name: '✖ Close Menu', action: true }

// Sidebar links
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

// Pick which links to show
const activeLinks = computed(() => {
  if (!userStore.isLoggedIn) return []
  if (userStore.role === 2 || userStore.role === 3) return adminLinks
  return studentLinks
})
</script>
