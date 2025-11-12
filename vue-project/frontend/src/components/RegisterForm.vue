<template>
  <form @submit.prevent="submitForm" class="p-4 bg-white rounded shadow-sm">
    <div class="mb-3">
      <label for="name" class="form-label">Name:</label>
      <input id="name" v-model="name" type="text" class="form-control" required />
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Email:</label>
      <input id="email" v-model="email" type="email" class="form-control" required />
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password:</label>
      <input id="password" v-model="password" type="password" class="form-control" required />
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">Registrera</button>

    <div class="text-center">
      <span>Already a member?</span>
      <router-link to="/login" class="btn btn-outline-secondary ms-2">
        Log in
      </router-link>
    </div>
  </form>
</template>

<script setup>
import { ref, defineEmits } from 'vue'

const name = ref('')
const email = ref('')
const password = ref('')
const emit = defineEmits(['register'])

function submitForm() {
  emit('register', { name: name.value, email: email.value, password: password.value })
}
</script>
