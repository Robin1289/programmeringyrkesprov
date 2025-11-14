<template>
  <div id="app" class="d-flex min-vh-100">
    <!-- Sidebar: only show if user is logged in -->
    <Sidebar v-if="userStore.isLoggedIn" />

    <!-- Main content -->
    <div class="flex-grow-1">
      <Navbar />
      <router-view />
      <Footer />
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useUserStore } from './store/userstore.js'
import Navbar from './components/Navbar.vue'
import Sidebar from './components/Sidebar.vue'
import Footer from './components/Footer.vue'

const userStore = useUserStore()

// Auto-fetch logged-in user data on app start
onMounted(() => {
  userStore.fetchUser()
})

// Optional global logout function
function logout() {
  userStore.logout()
}
</script>
