<template>
  <div class="container mt-5">
    <!-- Login Form -->
    <div v-if="!userStore.isLoggedIn && !showWelcomePopup">
      <LoginForm @login="handleLogin" />
    </div>

    <!-- Loading / redirect message -->
    <div v-else-if="userStore.isLoggedIn && !showWelcomePopup" class="text-center">
      <p>Redirecting to dashboard...</p>
    </div>

    <!-- Welcome popup -->
    <WelcomePopup
      v-if="showWelcomePopup"
      :username="userStore.name"
      :video-src="'/media/cutevid.mp4'"
      :sound-src="'/media/uwu.mp3'"
      @close="handlePopupClose"
    />

    <!-- Error message -->
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
import WelcomePopup from '../components/WelcomePopup.vue'

const router = useRouter()
const userStore = useUserStore()
const errorMessage = ref('')
const showWelcomePopup = ref(false)

// On mount, redirect if already logged in
onMounted(() => {
  if (userStore.isLoggedIn) {
    router.push('/dashboard')
  }
})

// Handle login
async function handleLogin(credentials) {
  const res = await userStore.login(credentials.email, credentials.password)
  if (res.success) {
    // Instead of redirecting immediately, show popup first
    showWelcomePopup.value = true
  } else {
    errorMessage.value = res.message
  }
}

// Called when the popup finishes
function handlePopupClose() {
  showWelcomePopup.value = false
  router.push('/dashboard')
}
</script>
