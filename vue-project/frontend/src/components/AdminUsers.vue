<template>
  <div>
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border hk-spinner" role="status"></div>
    </div>

    <table v-else class="table table-hover table-bordered hk-table">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Points</th>
          <th>Level</th>
          <th>Role</th>
          <th>Edit</th>
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
            <button class="btn btn-sm hk-btn" @click="goToUser(user.id)">
              Edit
            </button>
          </td>
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
  router.push(`/admin-users/${id}`)
}
</script>
