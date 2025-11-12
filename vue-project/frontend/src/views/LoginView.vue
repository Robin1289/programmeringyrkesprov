<template>
  <div class="container mt-5">
    <div v-if="!userStore.isLoggedIn">
      <h2 class="mb-4">Logga in</h2>
      <LoginForm @login="handleLogin" />
    </div>

    <div v-else class="text-center">
      <p>Redirecting to dashboard...</p>
    </div>

    <div v-if="errorMessage" class="alert alert-danger mt-3">
      {{ errorMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../store/userstore.js'
import LoginForm from '../components/LoginForm.vue'

const router = useRouter()
const userStore = useUserStore()
const errorMessage = ref('')

// Redirect if already logged in on mount
onMounted(() => {
  if (userStore.isLoggedIn) {
    router.push('/dashboard')
  }
})

// Watch login state and redirect immediately after login
watch(() => userStore.isLoggedIn, (loggedIn) => {
  if (loggedIn) {
    router.push('/dashboard')
  }
})

async function handleLogin(credentials) {
  const res = await userStore.login(credentials.email, credentials.password)
  if (!res.success) {
    errorMessage.value = res.message
  }
}
</script>
