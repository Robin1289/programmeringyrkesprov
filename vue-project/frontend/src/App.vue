<template>
  <div id="app" class="d-flex min-vh-100">

    <!-- Sidebar -->
    <Sidebar 
      v-if="userStore.isLoggedIn"
      :isOpen="sidebarOpen"
      @toggleSidebar="toggleSidebar"
    />

    <!-- OPEN BUTTON (floating on app) -->
    <button 
      v-if="userStore.isLoggedIn && !sidebarOpen" 
      class="sidebar-open-btn"
      @click="toggleSidebar"
    >
      🎀
    </button>

    <!-- Main content -->
    <div class="flex-grow-1">
      <Navbar />
      <router-view />
      <Footer />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useUserStore } from './store/userstore.js'
import Navbar from './components/Navbar.vue'
import Sidebar from './components/Sidebar.vue'
import Footer from './components/Footer.vue'

const userStore = useUserStore()
const sidebarOpen = ref(true)

function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value
}

onMounted(() => {
  userStore.fetchUser()
})
</script>
