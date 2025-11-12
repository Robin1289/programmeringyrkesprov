<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-pink mb-4 py-3 px-4 shadow-sm">
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
            <button @click="handleLogout" class="btn btn-logout btn-sm">
              Logga ut
            </button>
          </li>
        </template>
        <template v-else>
          <li class="nav-item">
            <router-link to="/" class="nav-link text-white">Home</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/login" class="nav-link text-white">Login</router-link>
          </li>
        </template>
      </ul>
    </div>
  </nav>
</template>

<script setup>
import { useUserStore } from '../store/userstore.js'
import { useRouter } from 'vue-router'

const userStore = useUserStore()
const router = useRouter()

async function handleLogout() {
  await userStore.logout()
  router.push('/login')
}
</script>
