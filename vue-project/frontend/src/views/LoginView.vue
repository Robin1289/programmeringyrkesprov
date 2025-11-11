<template>
  <div class="login-container">
    <h1>Login</h1>
    <form @submit.prevent="handleLogin">
      <label>Email:</label>
      <input v-model="email" type="email" required />
      
      <label>Password:</label>
      <input v-model="password" type="password" required />
      
      <button type="submit">Logga in</button>
    </form>
    <router-link to="/register">Skapa konto</router-link>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useUserStore } from '../store/userstore.js'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const userStore = useUserStore()
const router = useRouter()

async function handleLogin() {
  const success = await userStore.login(email.value, password.value)
  if (success) {
    router.push('/dashboard') // skicka användaren till dashboard
  } else {
    alert('Fel email eller lösenord')
  }
}
</script>
