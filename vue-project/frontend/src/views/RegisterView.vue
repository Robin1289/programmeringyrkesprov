<template>
  <div class="register-wrapper">

    <!-- Left Video -->
    <div class="register-side-video left">
      <video autoplay muted loop playsinline>
        <source src="/media/register1.mp4" type="video/mp4">
      </video>
    </div>

    <!-- CENTER FORM -->
    <div class="register-center">
      <RegisterForm
        @register="handleRegister"
        :errorMessage="errorMessage"
      />
    </div>

    <!-- Welcome Popup -->
    <WelcomePopup
      v-if="showWelcomePopup"
      :username="tempName"
      :video-src="'/media/cutevid.mp4'"
      :sound-src="'/media/uwu.mp3'"
    />

    <!-- Right Video -->
    <div class="register-side-video right">
      <video autoplay muted loop playsinline>
        <source src="/media/register2.mp4" type="video/mp4">
      </video>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useUserStore } from '../store/userstore.js'
import { useRouter } from 'vue-router'

import RegisterForm from '../components/RegisterForm.vue'
import WelcomePopup from '../components/WelcomePopup.vue'

const userStore = useUserStore()
const router = useRouter()

const errorMessage = ref("")       // visible error on screen
const showWelcomePopup = ref(false)
const tempName = ref("")           // for popup before login

async function handleRegister({ name, email, password }) {

  errorMessage.value = ""          // reset error

  const result = await userStore.register(name, email, password)

  // REGISTRATION FAILED → show error on page
  if (!result.success) {
    errorMessage.value = result.message
    return
  }

  // Save name for popup
  tempName.value = name
  showWelcomePopup.value = true

  // Login after popup
  setTimeout(async () => {
    try {
      await userStore.login({ email, password })

      router.push(userStore.role === 1 ? "/dashboard" : "/admin-dashboard")

    } catch (err) {
      errorMessage.value = "Registreringen lyckades, men inloggningen misslyckades."
    }
  }, 3000)
}
</script>
