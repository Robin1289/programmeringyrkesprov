<template>
  <div id="app" class="flex min-h-screen">
    <!-- Sidebar visas endast om användaren är inloggad -->
    <Sidebar v-if="userStore.isLoggedIn" />

    <div class="flex-1 flex flex-col">
      <!-- Navbar -->
      <nav class="navbar">
        <div class="logo">Läroportal</div>
        <div class="nav-actions">
          <template v-if="userStore.isLoggedIn">
            <span class="mr-4">Hej, {{ userStore.name }}</span>
            <button @click="logout" class="btn-logout">Logga ut</button>
          </template>
          <template v-else>
            <router-link to="/" class="nav-link">Home</router-link>
            <!-- Login-route kan läggas till senare -->
          </template>
        </div>
      </nav>

      <!-- Main content -->
      <main class="main-content">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useUserStore } from './store/userstore.js'
import Sidebar from './components/Sidebar.vue'

const userStore = useUserStore()

// Auto-login vid app-start
onMounted(() => {
  userStore.fetchUser()
})

// Logga ut-funktion
function logout() {
  userStore.logout()
}
</script>
