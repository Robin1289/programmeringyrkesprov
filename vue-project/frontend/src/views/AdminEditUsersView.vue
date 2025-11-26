<template>
  <div class="container py-4">
    <h1 class="hk-title mb-4">Edit User</h1>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border hk-spinner" role="status"></div>
    </div>

    <!-- FIX: Only show form when user is loaded -->
    <form v-else-if="user" class="hk-form">

      <div class="mb-3">
        <label class="form-label">User ID</label>
        <input type="text" class="form-control" v-model="user.id" disabled>
      </div>

      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" class="form-control" v-model="user.name">
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" v-model="user.email">
      </div>

      <div class="mb-3">
        <label class="form-label">Points</label>
        <input type="number" class="form-control" v-model="user.points">
      </div>

      <div class="mb-3">
        <label class="form-label">Level</label>
        <input type="number" class="form-control" v-model="user.level">
      </div>

      <div class="mb-3">
        <label class="form-label">Role</label>
        <select class="form-select" v-model="user.role">
          <option value="1">Student</option>
          <option value="2">Teacher</option>
          <option value="3">Admin</option>
        </select>
      </div>
      <!-- Password Change Section -->
        <div class="mb-3">
        <label class="form-label">New Password</label>
        <input 
            type="password" 
            class="form-control" 
            v-model="password"
            placeholder="Leave empty to keep current password"
        >
        </div>

        <div class="mb-3">
        <label class="form-label">Repeat Password</label>
        <input 
            type="password" 
            class="form-control" 
            v-model="password2"
            placeholder="Repeat the new password"
        >
        </div>


      <div class="d-flex gap-3 mt-4">
        <button type="button" class="btn hk-btn" @click="saveUser">Save</button>
        <button type="button" class="btn btn-danger hk-delete" @click="deleteUser">Delete</button>
      </div>

    </form>

    <!-- Optional error fallback -->
    <div v-else class="alert alert-danger mt-4">
      Failed to load user.
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const userId = route.params.id

const password = ref("")
const password2 = ref("")
const user = ref(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const response = await fetch(
      `http://localhost/yrkesprov/vue-project/backend/api/users.php?action=get&id=${userId}`,
      { credentials: 'include' }
    )
    const data = await response.json()

    if (data.success) {
      user.value = data.user
    }
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
})

async function saveUser() {
  // Validate password only if admin tries to change it
  if (password.value.length > 0 || password2.value.length > 0) {
    if (password.value !== password2.value) {
      alert("Passwords do not match!");
      return;
    }

    if (password.value.length < 6) {
      alert("Password must be at least 6 characters long.");
      return;
    }
  }

  const payload = {
    ...user.value,
    password: password.value || null
  };

  const response = await fetch(
    `http://localhost/yrkesprov/vue-project/backend/api/users.php?action=update`,
    {
      method: 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    }
  );

  const data = await response.json();

  if (data.success) {
    alert('User updated!');
    router.push('/admin/users');
  } else {
    alert('Error: ' + data.message);
  }
}


function deleteUser() {
  alert("Backend not implemented yet.")
}
</script>
