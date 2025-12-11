<template>
  <div id="app" class="d-flex">

    <!-- SIDEBAR -->
    <Sidebar
      v-if="userStore.isLoggedIn"
      :open="isSidebarOpen"
      @close="closeSidebar"
    />

    <!-- OPEN BUTTON -->
    <button
      v-if="!isSidebarOpen && !hideSidebarButton && userStore.isLoggedIn"
      class="sidebar-open-btn sidebar-btn-closed"
      @click="openSidebar"
    >
      <i class="fa-solid fa-angle-right"></i>
    </button>

    <!-- MAIN CONTENT -->
    <div class="main-wrapper" :class="{ 'sidebar-open': isSidebarOpen }">
      <Navbar />
      <BadgePopup :badge="userStore.badgePopup" />
      <div class="app-main">
        <router-view />
      </div>
      <Footer />
    </div>
  </div>
</template>


<script setup>
import { ref, computed } from 'vue'
import { useUserStore } from './store/userstore.js'
import { useRoute } from "vue-router";


import Sidebar from './components/Sidebar.vue'
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'
import BadgePopup from "./components/BadgePopup.vue"

const route = useRoute();

const hideSidebarButton = computed(() =>
  route.path === "/login" || route.path === "/register"
);


const userStore = useUserStore()
const isSidebarOpen = ref(false);

function openSidebar() {
  isSidebarOpen.value = true;
}

function closeSidebar() {
  isSidebarOpen.value = false;
}

</script>
