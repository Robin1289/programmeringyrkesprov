<template>
  <div class="auth-container">

    <!-- Left looping video -->
    <video autoplay muted loop class="side-video">
      <source src="/media/login1.mp4" type="video/mp4" />
      Your browser does not support the video tag.
    </video>

    <!-- Login form -->
    <form @submit.prevent="handleLogin" class="auth-form">
      <h2 class="mb-4 text-center text-pink">Logga in</h2>

      <!-- Username OR Email -->
      <div class="mb-3">
        <label for="identifier" class="form-label fw-semibold">E-post eller användarnamn</label>
        <input
          id="identifier"
          v-model="identifier"
          type="text"
          class="form-control"
          placeholder="E-post eller användarnamn"
          required
        />
      </div>

      <!-- Password -->
      <div class="mb-4">
        <label for="password" class="form-label fw-semibold">Lösenord</label>
        <input
          id="password"
          v-model="password"
          type="password"
          class="form-control"
          placeholder="********"
          required
        />
      </div>

      <button type="submit" class="btn btn-primary w-100 mb-3">Logga in</button>

      <!-- Error message -->
      <p v-if="errorMessage" class="text-danger text-center">{{ errorMessage }}</p>

      <div class="text-center mt-2">
        <span>Har du inget konto?</span>
        <router-link to="/register" class="btn btn-outline-secondary ms-2">
          Registrera
        </router-link>
      </div>
    </form>

    <!-- Right looping video -->
    <video autoplay muted loop class="side-video">
      <source src="/media/login2.mp4" type="video/mp4" />
      Your browser does not support the video tag.
    </video>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useUserStore } from '../store/userstore'

const userStore = useUserStore()

// Values
const identifier = ref('')
const password = ref('')
const errorMessage = ref('')

// Submit handler
async function handleLogin() {
  errorMessage.value = "" // Reset

  try {
    await userStore.login({
      email: identifier.value,    // username OR email
      password: password.value
    })
  } catch (err) {
    errorMessage.value = err.message || "Något gick fel, försök igen."
  }
}
</script>
