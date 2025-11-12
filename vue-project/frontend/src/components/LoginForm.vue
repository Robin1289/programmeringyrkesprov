<template>
  <form @submit.prevent="submitForm" class="p-4 bg-white rounded shadow-sm">
    <div class="mb-3">
      <label for="email" class="form-label">E-post:</label>
      <input id="email" v-model="email" type="email" class="form-control" required />
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Lösenord:</label>
      <input id="password" v-model="password" type="password" class="form-control" required />
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">Logga in</button>

    <div class="text-center">
      <span>Har du inget konto?</span>
      <router-link to="/register" class="btn btn-outline-secondary ms-2">
        Registrera
      </router-link>
    </div>
  </form>
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
