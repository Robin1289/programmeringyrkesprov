<template>
  <div class="container mt-5">

    <!-- Login Form -->
    <div v-if="!userStore.isLoggedIn && !showWelcomePopup">
      <LoginForm />
    </div>

    <!-- Loading / Redirect message -->
    <div v-else-if="userStore.isLoggedIn && !showWelcomePopup" class="text-center">
      <p>Redirecting...</p>
    </div>

    <!-- Welcome Popup (3 seconds) -->
    <WelcomePopup
      v-if="showWelcomePopup"
      :username="userStore.name"
      :video-src="'/media/cutevid.mp4'"
      :sound-src="'/media/uwu.mp3'"
    />

    <div v-if="errorMessage" class="alert alert-danger mt-3 text-center">
      {{ errorMessage }}
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../store/userstore.js'
import LoginForm from '../components/LoginForm.vue'
import WelcomePopup from '../components/WelcomePopup.vue'

const router = useRouter()
const userStore = useUserStore()

const errorMessage = ref('')
const showWelcomePopup = ref(false)


// If already logged in → redirect
onMounted(() => {
  if (userStore.isLoggedIn) {
    if (userStore.role === 1) {
      router.push('/dashboard')
    } else {
      router.push('/admin-dashboard')
    }
  }
})


// Listen for login via Pinia
userStore.$onAction(({ name, after, onError }) => {
  if (name === "login") {

    after(() => {
      // Login succeeded
      errorMessage.value = ""

      // Show welcome popup
      showWelcomePopup.value = true

      // Wait 3 seconds, then close + redirect
      setTimeout(() => {
        redirectAfterPopup()
      }, 3000)
    })

    onError((err) => {
      errorMessage.value = err.message || "Något gick fel."
    })
  }
})


// Redirect based on user role
function redirectAfterPopup() {
  showWelcomePopup.value = false

  if (userStore.role === 1) {
    router.push('/dashboard')
  } else {
    router.push('/admin-dashboard')
  }
}
</script>
