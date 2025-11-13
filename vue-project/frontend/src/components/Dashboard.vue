<template>
  <div class="dashboard-container mt-5">
    <!-- Header with profile and level progress -->
    <div class="dashboard-header d-flex align-items-center mb-5">
      <div class="profile-pic-wrapper me-3">
        <img
          src="/../frontend/public/assets/img/userphp.jpg"
          alt="Profile Picture"
          class="profile-pic"
        />
      </div>
      <div class="d-flex flex-column w-100">
        <h1 class="dashboard-title">Hello, {{ userStore.name }}!</h1>

        <!-- Progress bar under username -->
        <div class="mt-0 w-100" v-if="currentLevel && nextLevel">
          <div class="progress-wrapper">
            <div class="progress">
              <div
                class="progress-bar"
                :style="{ width: progressPercentage + '%' }"
              ></div>
            </div>
            <div class="progress-label mt-1">
              Points: {{ userPoints }} / {{ nextLevel?.l_min_points ?? 0 }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Cards Section -->
    <div class="row g-4">
      <div class="col-md-4">
        <router-link to="/results" class="text-decoration-none">
          <div class="dashboard-card text-center p-4">
            <h3>Results</h3>
            <p>Your quiz and course performance overview.</p>
          </div>
        </router-link>
      </div>

      <div class="col-md-4">
        <router-link to="/level" class="text-decoration-none">
          <div class="dashboard-card text-center p-4">
            <h3>Level</h3>
            <p>Next Level: {{ nextLevel?.l_name ?? 'Loading...' }}  ({{ nextLevel?.l_min_points ?? 0 }}  Points needed)</p>
          </div>
        </router-link>
      </div>

      <div class="col-md-4">
        <router-link to="/assignments" class="text-decoration-none">
          <div class="dashboard-card text-center p-4">
            <h3>Assignments</h3>
            <p>View and manage your active and past assignments.</p>
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted, computed } from 'vue'
import { useUserStore } from '../store/userstore.js'

const userStore = useUserStore()
const currentLevel = ref(null)
const nextLevel = ref(null)
const userPoints = ref(0)
const loading = ref(true)

const progressPercentage = computed(() => {
  if (!currentLevel.value || !nextLevel.value) return 0
  const minPoints = currentLevel.value.l_min_points
  const maxPoints = nextLevel.value.l_min_points
  const progress = userPoints.value - minPoints
  const total = maxPoints - minPoints
  return Math.min(Math.max((progress / total) * 100, 0), 100)
})

async function fetchLevels() {
  try {
    const response = await fetch(
      `http://localhost/yrkesprov/vue-project/backend/api/levels.php?user_id=${userStore.id}`,
      { credentials: 'include' }
    )
    const data = await response.json()
    if (data.success) {
      currentLevel.value = data.currentLevel
      nextLevel.value = data.nextLevel
      userPoints.value = data.userPoints
    } else {
      console.error('Failed to load levels:', data.message)
    }
  } catch (err) {
    console.error('Error fetching levels:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchLevels()
})
</script>
