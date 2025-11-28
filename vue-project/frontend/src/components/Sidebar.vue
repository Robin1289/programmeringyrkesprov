<template>
  <div class="sidebar-container" :class="{ collapsed }">

    <!-- Close button (inside sidebar) -->
    <button class="sidebar-close-btn" v-if="!collapsed" @click="$emit('collapse')">
      <i class="fa-solid fa-angle-left"></i>
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
  { name: 'Hemsida', to: '/dashboard', auth: true },
  { name: 'Uppgifter', to: '/assignments', auth: true },
  { name: 'Resultat', to: '/results', auth: true },
  { name: 'Nivå', to: '/level', auth: true }
]

const adminLinks = [
  { name: 'Admin hemsida', to: '/admin-dashboard', auth: true },
  { name: 'Hantera prov', to: '/admin-quizzes', auth: true },
  { name: 'Hantera resultat', to: '/admin-results', auth: true },
  { name: 'Hantera användare', to: '/admin-users', auth: true }
]

const activeLinks = computed(() => {
  if (!userStore.isLoggedIn) return []
  if (userStore.role === 2 || userStore.role === 3) return adminLinks
  return studentLinks
})
</script>
