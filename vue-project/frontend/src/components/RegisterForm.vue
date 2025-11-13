<template>
  <div class="auth-container">
    <!-- Left looping video -->
    <video autoplay muted loop class="side-video">
      <source src="/media/register1.mp4" type="video/mp4" />
      Your browser does not support the video tag.
    </video>

    <!-- Register form -->
    <form @submit.prevent="submitForm" class="auth-form">
      <h2 class="mb-4 text-center text-pink">Create an Account</h2>

      <div class="mb-3">
        <label for="name" class="form-label fw-semibold">Name</label>
        <input
          id="name"
          v-model="name"
          type="text"
          class="form-control"
          placeholder="Your full name"
          required
        />
      </div>

      <div class="mb-3">
        <label for="email" class="form-label fw-semibold">Email</label>
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
        <label for="password" class="form-label fw-semibold">Password</label>
        <input
          id="password"
          v-model="password"
          type="password"
          class="form-control"
          placeholder="********"
          required
        />
      </div>

      <button type="submit" class="btn btn-primary w-100 mb-3">Register</button>

      <div class="text-center">
        <span>Already have an account?</span>
        <router-link to="/login" class="btn btn-outline-secondary ms-2">
          Log in
        </router-link>
      </div>
    </form>

    <!-- Right looping video -->
    <video autoplay muted loop class="side-video">
      <source src="/media/register2.mp4" type="video/mp4" />
      Your browser does not support the video tag.
    </video>
  </div>
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
