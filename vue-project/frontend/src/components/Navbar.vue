<template>
  <nav class="navbar navbar-expand-lg  navbar-light bg-pink mb-4 py-3 px-4 shadow-sm">
    <!-- Brand / Logo -->
    <a class="navbar-brand fw-bold text-white" href="#">Läroportal</a>

    <!-- Toggle button for mobile -->
    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarNav"
      aria-controls="navbarNav"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar links -->
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav align-items-center">
        <template v-if="userStore.isLoggedIn">
          <li class="nav-item d-flex align-items-center">
            <button @click="showLogoutPopup = true" class="btn btn-logout btn-sm">
              Logga ut
            </button>
          </li>
        </template>

        <template v-else>
          <li class="nav-item" v-if="route.path !== '/login'">
            <router-link to="/login" class="nav-link text-white">Logga in</router-link>
          </li>
          <li class="nav-item" v-if="route.path !== '/register'">
            <router-link to="/register" class="nav-link text-white">Registrera</router-link>
          </li>
        </template>
      </ul>
    </div>

    <!-- Logout Popup -->
    <LogoutPopup
      v-if="showLogoutPopup"
      :username="userStore.name"
      :video-src="'/media/sadchild.mp4'"
      :sound-src="'/media/tf_nemesis.mp3'"
      @close="handleLogoutPopupClose"
    />
  </nav>
</template>

<script setup>
import { ref } from 'vue'
import { useUserStore } from '../store/userstore.js'
import { useRouter, useRoute } from 'vue-router'
import LogoutPopup from '../components/LogoutPopup.vue'

const userStore = useUserStore()
const router = useRouter()
const route = useRoute()  // <-- get current route

const showLogoutPopup = ref(false)

async function handleLogoutPopupClose() {
  // Hide popup
  showLogoutPopup.value = false

  // Actually log out the user
  await userStore.logout()
  
  // Redirect to login page
  router.push('/login')
}
</script>
