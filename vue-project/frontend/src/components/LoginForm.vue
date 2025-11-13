<template>
  <div class="auth-container">
    <!-- Left looping video -->
    <video autoplay muted loop class="side-video">
      <source src="/media/login1.mp4" type="video/mp4" />
      Your browser does not support the video tag.
    </video>

    <!-- Login form -->
    <form @submit.prevent="submitForm" class="auth-form">
      <h2 class="mb-4 text-center text-pink">Logga in</h2>

      <div class="mb-3">
        <label for="email" class="form-label fw-semibold">E-post</label>
        <input
          id="email"
          v-model="email"
          type="email"
          class="form-control"
          placeholder="you@example.com"
          required
        />
      </div>

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

      <div class="text-center">
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
import { ref, defineEmits } from 'vue'

const email = ref('')
const password = ref('')
const emit = defineEmits(['login'])

function submitForm() {
  emit('login', { email: email.value, password: password.value })
}
</script>
