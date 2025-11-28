<template>
<div class="manage-users-container">
<h1 class="admin-page-title">Manage Users</h1>

<div class="admin-card">
  <table class="table kitty-table">

      <thead>
        <tr>
          <th>ID</th>
          <th>Namn</th>
          <th>Epost</th>
          <th>Poäng</th>
          <th>Nivå</th>
          <th>Roll</th>
          <th>Redigera</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>{{ user.points }}</td>
          <td>{{ user.level }}</td>
          <td>{{ user.role }}</td>
          <td>
            <button class="kitty-btn-edit" @click="goToUser(user.id)">
              Redigera
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import router from '@/router'

const users = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const response = await fetch(
      'http://localhost/yrkesprov/vue-project/backend/api/users.php?action=getAll',
      { credentials: 'include' }
    )
    const data = await response.json()
    if (data.success) {
      users.value = data.users
    }
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
})

function goToUser(id) {
  router.push(`/admin-users/${id}`)
}
</script>
