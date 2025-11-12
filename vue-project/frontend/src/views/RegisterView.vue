<template>
  <div class="register-container">
    <h1>Registrera konto</h1>

    <RegisterForm @register="handleRegister" />

  </div>
</template>

<script setup>
import { useUserStore } from '../store/userstore.js'
import { useRouter } from 'vue-router'
import RegisterForm from '../components/RegisterForm.vue'

const userStore = useUserStore()
const router = useRouter()

async function handleRegister({ name, email, password }) {
  const result = await userStore.register(name, email, password)
  if (result.success) {
    alert('Konto skapat! Logga in.')
    router.push('/login')
  } else {
    alert(result.message)
  }
}
</script>
