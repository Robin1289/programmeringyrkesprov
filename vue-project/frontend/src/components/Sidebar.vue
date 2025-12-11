<template>
  <div class="sidebar-container" :class="{ open: open }">

    <button
      v-if="open"
      class="sidebar-close-btn sidebar-btn-opened"
      @click="$emit('close')"
    >
      <i class="fa-solid fa-angle-right"></i>
    </button>

    <!-- Sidebar content -->
    <div class="sidebar-content" v-if="open">
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
import { computed, defineProps, defineEmits } from 'vue'
import { useUserStore } from '../store/userstore.js'

defineProps({
  open: Boolean
})

const emit = defineEmits(["close"])
const userStore = useUserStore()

const studentLinks = [
  { name: 'Hemsida', to: '/dashboard' },
  { name: 'Uppgifter', to: '/assignments' },
  { name: 'Resultat', to: '/results' },
  { name: 'Nivå', to: '/level' }
]

const adminLinks = [
  { name: 'Admin hemsida', to: '/admin-dashboard' },
  { name: 'Hantera prov', to: '/admin-quizzes' },
  { name: 'Hantera resultat', to: '/admin-results' },
  { name: 'Hantera användare', to: '/admin-users' }
]

const activeLinks = computed(() => {
  if (!userStore.isLoggedIn) return []
  if (userStore.role === 2 || userStore.role === 3) return adminLinks
  return studentLinks
})
</script>
