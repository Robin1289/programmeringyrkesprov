<template>
  <div class="sidebar">
    <!-- Navigation links -->
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item" v-for="link in links" :key="link.name">
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
import { useUserStore } from '../store/userstore.js'

const userStore = useUserStore()

const links = [
  { name: 'Dashboard', to: '/dashboard', auth: true },
  { name: 'Assignments', to: '/assignments', auth: true },
  { name: 'Results', to: '/results', auth: true },
  { name: 'Level', to: '/level', auth: true },
]
</script>
