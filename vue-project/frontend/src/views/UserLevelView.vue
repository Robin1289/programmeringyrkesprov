<template>
  <div class="dashboard-container">
    <!-- Level Page Header -->
    <div class="level-header d-flex gap-3 align-items-center mt-5 p-3">
      <div class="profile-pic-wrapper level-pic">
        <img src="/public/assets/img/userphp.jpg" alt="Profile" class="profile-pic" />
      </div>

      <div class="d-flex flex-column justify-content-center w-100">
        <!-- Username + level-specific greeting -->
        <div class="level-title mb-1">Hello, {{ userName }}! Your Progress Towards the Next Level.</div>

        <!-- Progress bar -->
        <LevelProgress
          :current-level="currentLevel"
          :next-level="nextLevel"
          :user-points="userPoints"
        />
      </div>
    </div>

    <!-- Level cards -->
    <div class="row g-4 mt-4 level-page">
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
import { ref, onMounted } from 'vue'
import { useUserStore } from '../store/userstore.js'
import LevelProgress from '../components/LevelProgress.vue'

const userStore = useUserStore()
const userName = userStore.name

const currentLevel = ref(null)
const nextLevel = ref(null)
const userPoints = ref(0)
const loading = ref(true)

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
