<template>
  <div class="register-container">
    <h1>Registrera konto</h1>
    <form @submit.prevent="handleRegister">
      <label>Name:</label>
      <input v-model="name" type="text" required />

      <label>Email:</label>
      <input v-model="email" type="email" required />
      
      <label>Password:</label>
      <input v-model="password" type="password" required />
      
      <button type="submit">Registrera</button>
    </form>
    <router-link to="/login">Redan medlem? Logga in</router-link>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useUserStore } from '../store/userstore.js'
import { useRouter } from 'vue-router'

const name = ref('')
const email = ref('')
const password = ref('')
const userStore = useUserStore()
const router = useRouter()

async function handleRegister() {
  const result = await userStore.register(name.value, email.value, password.value)
  if (result.success) {
    alert('Konto skapat! Logga in.')
    router.push('/login')
  } else {
    alert(result.message)
  }
}
</script>
