<template>
  <div class="auth-container">

    <!-- Visible error message -->
    <div
      v-if="errorMessage"
      class="register-error-message text-center mb-3"
    >
      {{ errorMessage }}
    </div>

    <!-- Register form -->
    <form @submit.prevent="submitForm" class="auth-form">
      <h2 class="mb-4 text-center text-pink">Skapa konto</h2>

      <div class="mb-3">
        <label for="name" class="form-label fw-semibold">Namn</label>
        <input
          id="name"
          v-model="name"
          type="text"
          class="form-control"
          placeholder="Ditt namn"
          required
        />
      </div>

      <div class="mb-3">
        <label for="email" class="form-label fw-semibold">E-post</label>
        <input
          id="email"
          v-model="email"
          type="email"
          class="form-control"
          placeholder="du@example.com"
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
          placeholder="*******"
          required
        />
      </div>

      <button type="submit" class="btn btn-primary w-100 mb-3">Registrera</button>

      <div class="text-center">
        <span>Har du redan ett konto?</span>
        <router-link to="/login" class="btn btn-outline-secondary ms-2">
          Logga in
        </router-link>
      </div>
    </form>

  </div>
</template>

<script setup>
import { ref, defineEmits, defineProps } from 'vue'

const props = defineProps({
  errorMessage: String
})

const name = ref('')
const email = ref('')
const password = ref('')
const emit = defineEmits(['register'])

function submitForm() {
  emit('register', { name: name.value, email: email.value, password: password.value })
}
</script>
