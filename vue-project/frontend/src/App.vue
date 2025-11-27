<template>
  <div id="app" class="d-flex min-vh-100">

    <!-- SIDEBAR (collapsible) -->
    <Sidebar
      v-if="userStore.isLoggedIn"
      :collapsed="collapsed"
      @collapse="collapsed = true"
    />

    <!-- OPEN BUTTON (when sidebar is collapsed) -->
    <button
      v-if="collapsed && userStore.isLoggedIn"
      class="sidebar-open-btn"
      @click="collapsed = false"
    >
      🎀
    </button>

    <!-- MAIN CONTENT -->
    <div class="flex-grow-1">
      <Navbar />
      <router-view />
      <Footer />
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useUserStore } from './store/userstore.js'

import Sidebar from './components/Sidebar.vue'
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'

const userStore = useUserStore()

// Sidebar state
const collapsed = ref(false)
</script>
