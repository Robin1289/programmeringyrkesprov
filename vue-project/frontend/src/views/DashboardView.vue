<template>
  <div>
    <Dashboard
      v-if="userStore.ready"
      :name="userStore.name"
      :points="userStore.points"
      :level="userStore.level"
      :badges="userStore.badges"
    />
    <div v-else class="text-center py-5">Loading...</div>
  </div>
</template>

<script setup>
import { useUserStore } from '../store/userstore.js'
import { onMounted } from 'vue'
import Dashboard from '../components/Dashboard.vue'

const userStore = useUserStore()

onMounted(() => {
  if (!userStore.isLoggedIn) {
    userStore.fetchUser()
  }
})
</script>
