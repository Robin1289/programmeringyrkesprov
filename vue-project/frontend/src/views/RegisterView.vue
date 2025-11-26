<template>
  <div class="register-wrapper">

    <!-- Left Video -->
    <div class="register-side-video left">
      <video autoplay muted loop playsinline>
        <source src="/media/register1.mp4" type="video/mp4">
      </video>
    </div>

    <!-- Center Form -->
    <div class="register-center">
      <RegisterForm @register="handleRegister" />
    </div>

    <!-- Right Video -->
    <div class="register-side-video right">
      <video autoplay muted loop playsinline>
        <source src="/media/register2.mp4" type="video/mp4">
      </video>
    </div>

  </div>
</template>

<script setup>
import { useUserStore } from '../store/userstore.js'
import { useRouter } from 'vue-router'
import RegisterForm from '../components/RegisterForm.vue'

const userStore = useUserStore()
const router = useRouter()

async function handleRegister({ name, email, password }) {
  // Register user
  const result = await userStore.register(name, email, password)

  if (!result.success) {
    alert(result.message)
    return
  }

  // Auto-login
  try {
    await userStore.login({
      email: email,
      password: password
    })

    // Redirect based on role (login already loads role)
    if (userStore.role === 1) {
      router.push('/dashboard')
    } else {
      router.push('/admin-dashboard')
    }

  } catch (err) {
    alert("Registration worked, but auto-login failed. Try logging in manually.")
  }
}
</script>

