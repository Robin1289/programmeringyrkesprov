<template>
  <div id="app" class="d-flex">

    <!-- SIDEBAR -->
    <Sidebar
      v-if="userStore.isLoggedIn"
      :collapsed="collapsed"
      @collapse="collapsed = true"
    />

    <!-- BUTTON WHEN COLLAPSED -->
    <button
      v-if="collapsed && userStore.isLoggedIn"
      class="sidebar-open-btn"
      @click="collapsed = false"
    >
      <i class="fa-solid fa-angle-right"></i>
    </button>

    <!-- EVERYTHING EXCEPT THE SIDEBAR -->
    <div class="main-wrapper flex-grow-1">

      <!-- NAVBAR OUTSIDE app-main -->
      <Navbar />

      <BadgePopup :badge="userStore.badgePopup" />

      <!-- PAGE CONTENT ONLY -->
      <div class="app-main">
        <router-view />
      </div>

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
import BadgePopup from "./components/BadgePopup.vue";

const userStore = useUserStore()
const collapsed = ref(false)
</script>
