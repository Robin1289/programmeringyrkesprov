<template>
  <div id="app">
    <h1>Hello Vue!</h1>
  </div>
  </template>

<script setup>

import { useUserStore } from '../store/userstore.js'
import { onMounted } from 'vue'

onMounted(async () => {
  // Run level_up.php when dashboard loads
  await fetch("http://localhost/yrkesprov/vue-project/backend/api/level_up.php", {
    method: "POST",
    credentials: "include"
  });

  // Fetch user only if not already loaded
  if (!userStore.isLoggedIn) {
    await userStore.fetchUser()
  }
})


</script>