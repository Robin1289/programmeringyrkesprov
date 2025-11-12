<template>
  <div class="dashboard-container">
    <!-- Header with username and progress bar -->
    <div class="dashboard-header d-flex gap-3 align-items-start">
      <div class="profile-pic-wrapper">
        <img src="/public/assets/img/userphp.jpg" alt="Profile" class="profile-pic" />
      </div>

      <div class="d-flex flex-column w-100">
        <!-- Username -->
        <div class="dashboard-title">Hello, {{ userName }}!</div>

        <!-- Progress bar directly under the username -->
        <div class="mt-2 w-100">
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

    <!-- Level cards -->
    <div class="row g-4 mt-4">
      <div class="col-md-6" v-if="currentLevel">
        <div class="dashboard-card p-4">
          <h3>Current Level: {{ currentLevel.l_name }}</h3>
          <p>Points: {{ userPoints }} / {{ nextLevel?.l_min_points ?? 0 }}</p>
        </div>
      </div>

      <div class="col-md-6" v-if="nextLevel">
        <div class="dashboard-card p-4">
          <h3>Next Level: {{ nextLevel.l_name }}</h3>
          <p>Requires {{ nextLevel.l_min_points }} points</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useUserStore } from '../store/userstore.js'

const userStore = useUserStore()
const userName = userStore.name

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
    const response = await fetch('http://localhost/yrkesprov/vue-project/backend/api/levels.php', {
      credentials: 'include'
    })
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
