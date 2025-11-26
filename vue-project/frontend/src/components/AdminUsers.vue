<template>
  <div>
    <h2>User List</h2>

    <div v-if="loading">Loading users...</div>

    <table v-else class="user-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Points</th>
          <th>Level</th>
          <th>Role</th>
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="user in users"
          :key="user.id"
          @click="goToUser(user.id)"
          class="clickable-row"
        >
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>{{ user.points }}</td>
          <td>{{ user.level }}</td>
          <td>{{ user.role }}</td>
        </tr>
      </tbody>
    </table>
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
  router.push(`/admin/users/${id}`)
}
</script>

<style scoped>
.user-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}
.user-table th,
.user-table td {
  padding: 12px;
  border-bottom: 1px solid #ccc;
}
.clickable-row {
  cursor: pointer;
}
.clickable-row:hover {
  background: #f5f5f5;
}
</style>
