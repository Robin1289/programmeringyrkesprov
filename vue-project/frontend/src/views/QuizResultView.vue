<template>
  <div class="container py-4">
    <ResultPage />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useUserStore } from '../store/userstore.js'
import ResultPage from '@/components/ResultPage.vue'

const userStore = useUserStore()

onMounted(async () => {
  await fetch("https://yrkesprov.fwh.is/backend/api/level_up.php", {
    method: "POST",
    credentials: "include"
  })

  if (!userStore.isLoggedIn) {
    await userStore.fetchUser()
  }
})
</script>
